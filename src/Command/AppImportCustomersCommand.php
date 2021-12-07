<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 01/04/19
 * Time: 09:49
 */

namespace App\Command;


use App\Entity\Customer;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;

class AppImportCustomersCommand extends Command
{

    protected static $defaultName = 'app:import:customers';

    protected $em;
    protected $dbName;
    protected $dbUser;
    protected $dbPwd;

    public function __construct(EntityManagerInterface $em, string $dbName, string $dbUser, string $dbPwd)
    {
        $this->em = $em;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPwd = $dbPwd;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les users depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $users = [];
        $usersDone = [];
        $customersDone = [];
        $i = 0;
        $batchSize = 100;

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $query = $bdd->query('
            SELECT 
                nomclient AS nomclient,
                id AS id,
                emailclient AS emailclient, 
                complement1 AS complement1,
                complement2 AS complement2,
                codepostalclient AS codepostalclient,
                villeclient AS villeclient,
                paysclient AS paysclient,
                telephoneclient AS telephoneclient,
                date AS date, 
                contactclient AS contactclient 
            FROM sav
            WHERE type = \'0\'
        ');

        while($data = $query->fetch()){
            $data = array_map('trim', $data);

            if ((($data['emailclient'] !== '' && null !== $data['emailclient']) || ($data['nomclient'] !== '' && null !== $data['nomclient'])) && false === in_array($data['nomclient'].$data['emailclient'], $usersDone, true)) {

                $usersDone[] = $data['nomclient'].$data['emailclient'];

                $country = (null !== $data['paysclient'] || $data['paysclient'] !== '') ? $data['paysclient'] : 'FR';

                $users[] = [
                    'id' => $data['id'],
                    'name' => $data['nomclient'],
                    'email' => $data['emailclient'],
                    'address' => $data['complement1'],
                    'additionalAddress' => $data['complement2'],
                    'postalCode' => $data['codepostalclient'],
                    'city' => $data['villeclient'],
                    'country' => $country,
                    'phoneNumber' => $data['telephoneclient'],
                    'createdAt' => $data['date'],
                    'customerContact' => $data['contactclient']
                ];
            }
        }
        $query->closeCursor();

        $count = \count($users);
        $output->writeln('');
        $output->writeln('========== CUSTOMERS ==========');
        $progress = new ProgressBar($output, $count);

        $countryNames = Intl::getRegionBundle()->getCountryNames();

        foreach ($users as $user){
            if(false === in_array($user['name'].$user['email'], $customersDone, true)) {
                /** @var Customer $customer */
                $customer = $manager->getRepository(Customer::class)
                    ->findOneBy([
                        'name' => $user['name']
                    ]);

                if (null === $customer) {
                    $country = 'FR';

                    $customer = new Customer();
                    if(null !== $user['name'] && $user['name'] !== '') {
                        $customer->setName($user['name']);
                    }

                    if(null !== $user['email'] && $user['email'] !== '') {
                        $customer->setEmail($user['email']);
                    }
                    if(null !== $user['address'] && $user['address'] !== '') {
                        $customer->setAddress($user['address']);
                    }
                    if(null !== $user['additionalAddress'] && $user['additionalAddress'] !== '') {
                        $customer->setAdditionalAddress($user['additionalAddress']);
                    }
                    if(null !== $user['postalCode'] && $user['postalCode'] !== '') {
                        $customer->setPostalCode($user['postalCode']);
                    }
                    if(null !== $user['city'] && $user['city'] !== '') {
                        $customer->setCity($user['city']);
                    }
                    if(null !== $user['country'] && $user['country'] !== '') {
                        if (strlen($user['country']) <= 2){
                            if (array_key_exists($user['country'], $countryNames)) {
                                $country = $user['country'];
                            }
                        } else {
                            $userCountry = ucfirst(strtolower($user['country']));
                            foreach ($countryNames as $countryCode => $countryName){
                                if ($countryName === $userCountry){
                                    $country = $countryCode;
                                }
                            }
                        }
                        $customer->setCountry($country);
                    } else {
                        $customer->setCountry('FR');
                    }
                    if(null !== $user['phoneNumber'] && $user['phoneNumber'] !== '') {
                        $customer->setPhoneNumber($user['phoneNumber']);
                    }
                    if(null !== $user['createdAt'] && $user['createdAt'] !== '') {
                        /** @var \DateTime $date */
                        $date = ($user['createdAt'] !== '0000-00-00 00:00:00') ? \DateTime::createFromFormat('Y-m-d H:i:s', $user['createdAt']) : \DateTime::createFromFormat('Y-m-d H:i:s', \date('Y-m-d H:i:s'));
                        $customer->setCreatedAt($date);
                    }
                    if(null !== $user['customerContact'] && $user['customerContact'] !== '') {
                        $customer->setCustomerContact($user['customerContact']);
                    }

                    $customersDone[] = $user['name'].$user['email'];

                    $manager->persist($customer);

                    if ($i % $batchSize === 0) {
                        $manager->flush();
                    }

                    $i++;
                    $progress->setMessage($i, 'item');
                    $progress->advance();
                    $progress->displayMessage($i);
                }
            }
        }
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}