<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 27/06/19
 * Time: 13:46
 */

namespace App\Handler;


use App\Entity\Customer;
use App\Entity\Files;
use App\Entity\Messaging;
use App\Entity\Sav;
use App\Entity\Source;
use App\Entity\StatusSetting;
use App\Entity\User;
use App\Enum\ClientTypeEnum;
use App\Enum\SenderFileEnum;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class RequestForSavHandler
{
    private $em;
    private $dispatcher;
    private $swiftMailer;
    private $mailer;
    private $twigEngine;
    private $translator;
    private $mailSav = 'webmaster@lamafrance.com';

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, Swift_Mailer $swiftMailer, Environment $twigEngine, TranslatorInterface $translator, Mailer $mailer)
    {
        $this->em = $em;
        $this->dispatcher = LegacyEventDispatcherProxy::decorate($dispatcher);
        $this->mailer = $mailer;
        $this->swiftMailer = $swiftMailer;
        $this->twigEngine = $twigEngine;
        $this->translator = $translator;
    }

    /**
     * @param Sav $sav
     * @param Messaging $message
     * @param bool $sendByCustomer
     * @throws Exception
     */
    public function newMessage(Sav $sav, Messaging $message, bool $sendByCustomer): void
    {
        $sender = $this->translator->trans(SenderFileEnum::get($message->getSender()));
        $always = true;
        $to = null;
        $source = $sav->getSource();
        if (null === $source) {
            return;
        }
        $user = $sav->getUser();
        if (null === $user) {
            return;
        }
        $customer = $sav->getCustomer();
        if (null === $customer) {
            return;
        }
        $to = $message->getSender() === ClientTypeEnum::CUSTOMER ? $user->getEmail() : $customer->getEmail();
        $code = $sav->getSecretCode();

        if ($sav->getClientType() === ClientTypeEnum::DEALER) {
            $typeClient = ClientTypeEnum::DEALER;
        } else {
            $typeClient = ClientTypeEnum::CUSTOMER;
            $to = $customer->getEmail();
            if (null === $to) {
                $customer = $sav->getCustomer();
                if (null !== $customer) {
                    $to = $customer->getEmail();
                }
            }
        }

        if ($message->getSender() !== SenderFileEnum::LAMA) {
            $always = false;
        }

        if ($sendByCustomer) {
            $to = $this->mailSav;
        }

        if (null !== $to) {
            $this->mailer->sendMailNewMessage($sav, $message, $to, $sender, $typeClient, $always, $code);
        }

    }

    /**
     * @param Sav $sav
     * @param Source $source
     * @param string $code
     */
    public function sendConfirmation(Sav $sav, Source $source, string $code): void
    {

        if ($sav->getClientType() === ClientTypeEnum::DEALER) {
            $typeClient = ClientTypeEnum::DEALER;
        } else {
            $typeClient = ClientTypeEnum::CUSTOMER;
        }

        try {
            $messageConfirmation = (new Swift_Message($this->translator->trans('app.mail.sav_confirmation.new_sav')))
                ->setFrom([
                    'sav@lmeco.fr' => 'LM ECO PRODUCTION'
                ])
                ->setTo($sav->getCustomerEmail())
                //->setTo(['alexandre@influcom.fr', 'alexandre.lagoutte@viacesi.fr'])
                ->setBody(
                    $this->twigEngine->render('mail/sav_confirmation.html.twig', [
                        'savId' => $sav->getId(),
                        'code' => $code,
                        'typeClient' => $typeClient
                    ]),
                    'text/html'
                );
        } catch (LoaderError $e) {
            dump($e->getMessage());
            die();
        } catch (RuntimeError $e) {
            dump($e->getMessage());
            die();
        } catch (SyntaxError $e) {
            dump($e->getMessage());
            die();
        }

        $this->swiftMailer->send($messageConfirmation);

        $client = $this->translator->trans(ClientTypeEnum::get($sav->getClientType()));
        $customer = $sav->getCustomer();
        if (null === $customer) {
            return;
        }
        $customerName = $customer->getName();

        $to = $this->mailSav;
        if ($source->getSlug() === 'site-eco-telephone') {
            $to = 'jterzian@eco-imprimante.fr';
        }

        try {
            $messageRecap = (new Swift_Message($this->translator->trans('app.mail.sav_recap.new_recap')))
                ->setFrom([
                    'sav@lmeco.fr' => 'LM ECO PRODUCTION'
                ])
                ->setReplyTo('sav@lmeco.fr')
                ->setTo($to)
                //               ->setTo(['alexandre@influcom.fr', 'alexandre.lagoutte@viacesi.fr'])
                ->setBcc(['ddemoment@eco-imprimante.fr'])
                ->setBody(
                    $this->twigEngine->render('mail/sav_recap.html.twig', [
                        'sav' => $sav,
                        'client' => $client,
                        'customerName' => $customerName
                    ]),
                    'text/html'
                );
        } catch (LoaderError $e) {
            dump($e->getMessage());
            die();
        } catch (RuntimeError $e) {
            dump($e->getMessage());
            die();
        } catch (SyntaxError $e) {
            dump($e->getMessage());
            die();
        }

        $this->swiftMailer->send($messageRecap);
    }

    /**
     * @param Sav $sav
     * @return string
     * @throws Exception
     */
    public function generateCode(Sav $sav): string
    {
        $codeHash = [];
        $i = 0;
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $randomCoeff = 5;
        $code = '';

        $idNumber = str_split($sav->getId());
        foreach ($idNumber as $number) {
            for ($j = 0; $j < $randomCoeff; $j++) {
                $codeHash[$i][] = $alphabet[random_int(0, 25)];
            }

            $codeHash[$i][random_int(0, $randomCoeff - 1)] = $number;
            $i++;
        }

        foreach ($codeHash as $array) {
            $code .= implode('', $array);
        }

        return $code;
    }
}