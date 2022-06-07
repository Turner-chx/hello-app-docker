<?php


namespace App\Mailer;


use App\Entity\Messaging;
use App\Entity\Sav;
use App\Entity\SavArticle;
use App\Enum\ClientTypeEnum;
use Doctrine\ORM\PersistentCollection;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    private $twig;
    private $mailer;
    private $translator;
    private $projectDir;
    private $router;
    private $pdfHandler;
    private $parameterHandler;
    private $from = ['no-reply@savcartouches.com' => 'SAV CARTOUCHE'];

    public function __construct(Environment $twig, Swift_Mailer $mailer, TranslatorInterface $translator, string $projectDir, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
        $this->router = $router;
    }

    /**
     * @param Swift_Message $message
     * @return int
     */
    private function sendMail(Swift_Message $message): int
    {
        return $this->mailer->send($message);
    }

    /**
     * @param Sav $sav
     */
    public function sendMailSavNew(Sav $sav)
    {
        if ($sav->getClientType() === ClientTypeEnum::DEALER) {
            $typeClient = ClientTypeEnum::DEALER;
        } else {
            $typeClient = ClientTypeEnum::CUSTOMER;
        }

        $customer = $sav->getCustomer();
        if (null === $customer) {
            return;
        }
        $email = $customer->getEmail();

        try {
            $messageConfirmation = (new \Swift_Message($this->translator->trans('app.mail.sav_new.new_sav')))
                ->setFrom($this->from)
                ->setTo(strtolower($email))
//                ->setBcc(['ddemoment@eco-imprimante.fr'])
//                ->setReplyTo('sav@lmeco.fr')
                ->setBody(
                    $this->twig->render('mail/sav_new.html.twig', [
                        'savId' => $sav->getId(),
                        'code' => $sav->getSecretCode(),
                        'typeClient' => $typeClient
                    ]),
                    'text/html'
                );
            $this->sendMail($messageConfirmation);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }

    /**
     * @param Sav $sav
     */
    public function sendAdminMailSavNew(Sav $sav)
    {

        try {
            $messageConfirmation = (new \Swift_Message($this->translator->trans('app.mail.sav_recap.new_recap')))
                ->setFrom($this->from)
                ->setTo('webmaster@lamafrance.com')
//                ->setBcc(['ddemoment@eco-imprimante.fr'])
//                ->setReplyTo('sav@lmeco.fr')
                ->setBody(
                    $this->twig->render('mail/admin/admin_sav_new.html.twig', [
                        'sav' => $sav,
                    ]),
                    'text/html'
                );
            $this->sendMail($messageConfirmation);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }

    /**
     * @param Sav $sav
     * @param Messaging $message
     * @param string $to
     * @param string $sender
     * @param string $typeClient
     * @param bool $always
     * @param string $code
     */
    public function sendMailNewMessage(Sav $sav, Messaging $message, string $to, string $sender, string $typeClient, bool $always, string $code)
    {

        try {
            $messageMessage = (new Swift_Message($this->translator->trans('app.mail.sav_message.new_message', ['%savId%' => $sav->getId()])))
                ->setFrom([
                    'no-reply@savcartouches.com' => 'SAVCARTOUCHES'
                ])
                ->setTo(strtolower($to))
                ->setBody(
                    $this->twig->render('mail/sav_message.html.twig', [
                        'sav' => $sav,
                        'message' => $message,
                        'sender' => $sender,
                        'typeClient' => $typeClient,
                        'always' => $always,
                        'code' => $code
                    ]),
                    'text/html'
                );
            $this->sendMail($messageMessage);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }

    public function sendMailSavSendLink($email, Sav $sav, $code, $typeClient)
    {
        try {
            $messageConfirmation = (new \Swift_Message($this->translator->trans('app.mail.sav_send_link.new_sav')))
                ->setFrom($this->from)
                ->setTo(strtolower($email))
                ->setBody(
                    $this->twig->render('mail/sav_send_link.html.twig', [
                        'savId' => $sav->getId(),
                        'code' => $code,
                        'typeClient' => $typeClient
                    ]),
                    'text/html'
                );
            $this->sendMail($messageConfirmation);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }

    public function sendMailCommercialSav(Sav $sav, ?PersistentCollection $savArticles)
    {
        $issue = '';
        $text = '';
        /** @var SavArticle $savArticle */
        foreach ($sav->getSavArticles() as $savArticle) {
            $issue .= $savArticle->getDisplayNatureSetting();
            $ref = null !== $savArticle->getArticle() ? $savArticle->getArticle()->getReference() : $savArticle->getUnknownArticle();
            $text .= "<br> Référence(s): " . $ref . ", 
                            <br> Problème rencontré :<br>" . $issue . ", 
                            <br> Quantité : 1 <br>";
        }
        $source = $sav->getSource();
        if (null === $source) {
            return;
        }
        $emails = [];
        if (null !== $source) {
            foreach ($source->getEmails() as $email) {
                $emails[] = strtolower($email->getEmail());
            }
        }
        $customer = $sav->getCustomer();
        if (null === $customer) {
            return;
        }
        $email = $source->getDealerEmail();
        $link = $this->router->generate('follow-my-sav', ['codeSav' => $sav->getSecretCode()], UrlGeneratorInterface::ABSOLUTE_URL);
        if ($sav->getDivaltoNumber() !== null) {
            $message = (new Swift_Message($this->translator->trans('app.mail.mail_commercial.name1', ['%idsav%' => $sav->getId()])))
                ->setFrom(['no-reply@savcartouches.com' => 'Savcartouches'])
                ->setTo(strtolower($customer->getEmail()))
                ->setBody(
                    "Bonjour, <br> <br>
                            Vous avez contacté notre Service après-vente. <br>
                            Un dossier a été ouvert sous la référence : " . $sav->getId() . " <br>
                            Je vous informe que nous avons procédé au remplacement gracieux des produits ci-dessous, avec expédition à votre adresse : <br> 
                            $text
                            <br> En espérant conserver votre fidélité, <br > 
                            Cordialement <br>
                            Service Technique<br>
                            Cartouches jet d'encre et Toners<br>
                            Ceci est un message automatique : merci de ne pas y répondre<br>
                            <a target='_blank' href='" . $link . "'>" . $link . "</a><br>
                            <img src='https://savcartouches.com/images/image001.jpg' alt=''>"
                );
            $this->sendMail($message);
            $message = (new Swift_Message($this->translator->trans('app.mail.mail_commercial.name3', ['%idsav%' => $sav->getId()])))
                ->setFrom(['no-reply@savcartouches.com' => 'Savcartouches'])
                ->setTo(strtolower($email))
                ->setCc($emails)
                ->setBody(
                    "Bonjour, <br> <br> 
                            Votre client " . $customer->getName() . " a contacté notre Service après-vente.  
                            <br> Un dossier a été ouvert sous la référence " . $sav->getId() . "

                            <br><br>Je vous informe que nous avons procédé au remplacement gracieux des produits ci-dessous, avec expédition directe chez votre client. 
                            $text

                            <br>Soucieux de vous apporter le meilleur service,
                            <br>Cordialement<br>
                            Service Technique<br>
                            Cartouches jet d'encre et Toners<br>
                            En cas de question, veuillez vous munir de la référence de dossier et contacter votre contact commercial habituel .<br>
                            Ceci est un message automatique : merci de ne pas y répondre <br>
                            <a target='_blank' href='" . $link . "'>" . $link . "</a><br>
                            <img src='https://savcartouches.com/images/image001.jpg' alt=''>"
                );
        } else {
            $message = (new Swift_Message($this->translator->trans('app.mail.mail_commercial.name2', ['%idsav%' => $sav->getId()])))
                ->setFrom(['no-reply@savcartouches.com' => 'Savcartouches'])
                ->setTo(strtolower($customer->getEmail()))
                ->setBody(
                    "Bonjour,<br> <br>
                            Vous avez contacté notre Service après-vente. <br>
                            Un dossier a été ouvert sous la référence " . $sav->getId() . " concernant le(s) produit(s) ci-dessous 
                            $text
                            <br > Nous avons été ravis de pouvoir vous aider et vous apporter une solution . Le dossier est clôturé .
                            <br> Souhaitant vous avoir apporté entière satisfaction,
                            <br> Cordialement<br>
                            Service Technique<br>
                            Cartouches jet d'encre et Toners<br>
                            Ceci est un message automatique : merci de ne pas y répondre<br>
                            <a target='_blank' href='" . $link . "'>" . $link . "</a><br>
                            <img src='https://savcartouches.com/images/image001.jpg' alt=''>"
                );
            $this->sendMail($message);
            $message = (new Swift_Message($this->translator->trans('app.mail.mail_commercial.name4', ['%idsav%' => $sav->getId()])))
                ->setFrom(['no-reply@savcartouches.com' => 'Savcartouches'])
                ->setTo(strtolower($email))
                ->setCc($emails)
                ->setBody(
                    "Bonjour,<br> <br>
                            Votre client " . $customer->getName() . " a contacté notre Service après-vente. 
                            <br>Un dossier a été ouvert sous la référence " . $sav->getId() . " concernant le(s) produit(s) ci- dessous <br> <br>
                            $text
                            
                            <br>Le souci rencontré par votre client a pu être réglé sans échange de produit  et le dossier est clôturé.
                            <br>Soucieux de vous apporter le meilleur service,
                            <br>Cordialement,<br>
                            Service Technique<br>
                            Cartouches jet d'encre et Toners<br>
                            Ceci est un message automatique : merci de ne pas y répondre<br>
                            En cas de question, veuillez vous munir de la référence de dossier et contacter votre contact commercial habituel.<br>
                            <a target='_blank' href='" . $link . "'>" . $link . "</a><br>
                            <img src='https://savcartouches.com/images/image001.jpg' alt=''>"
                );
        }
        $this->sendMail($message);
    }
}