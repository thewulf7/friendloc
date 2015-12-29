<?php
namespace thewulf7\friendloc\services;


use thewulf7\friendloc\components\AbstractService;
use thewulf7\friendloc\models\User;

class EmailService extends AbstractService
{
    public function sendConfirmationEmail(User $user, $passwd = '')
    {
        $subject = 'Confirmation email from ' . $this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('appName');

        $body = $this->getTemplater()->render(
            'email/confirmation.twig',
            [
                'name'   => $user->getName(),
                'email'  => $user->getEmail(),
                'passwd' => $passwd,
                'link'   => 'http://45.55.197.167/auth/approve/?hash=' . $user->getUserhash(),
            ]
        );

        $mailer    = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());
        $message   = \Swift_Message::newInstance($subject);

        $message
            ->setFrom($this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('emailFrom'))
            ->setTo([$user->getEmail() => $user->getName()])
            ->setBody($body);

        $headers =& $message->getHeaders();
        $headers->addIdHeader('Message-ID', md5(time()) . "@friendloc.dev");
        $headers->addTextHeader('MIME-Version', '1.0');
        $headers->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $headers->addParameterizedHeader('Content-type', 'text/html', ['charset' => 'utf-8']);

        if (!$mailer->send($message, $failures))
        {
            throw new \Exception(implode(',', $failures));
        }

        return true;
    }

    public function sendSuccessEmail(User $user)
    {
        $subject = 'Greetings on ' . $this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('appName');

        $body = $this->getTemplater()->render(
            'email/success.twig',
            [
                'name'   => $user->getName(),
            ]
        );

        $mailer    = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());
        $message   = \Swift_Message::newInstance($subject);

        $message
            ->setFrom($this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('emailFrom'))
            ->setTo([$user->getEmail() => $user->getName()])
            ->setBody($body);

        $headers =& $message->getHeaders();
        $headers->addIdHeader('Message-ID', md5(time()) . "@friendloc.dev");
        $headers->addTextHeader('MIME-Version', '1.0');
        $headers->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $headers->addParameterizedHeader('Content-type', 'text/html', ['charset' => 'utf-8']);

        if (!$mailer->send($message, $failures))
        {
            throw new \Exception(implode(',', $failures));
        }

        return true;
    }
}