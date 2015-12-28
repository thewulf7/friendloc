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
                'link'   => 'http://friendloc.dev/auth/approve/?hash=' . $user->getUserhash(),
            ]
        );

        $mailer    = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());
        $message   = \Swift_Message::newInstance($subject);

        $message
            ->setFrom($this->getContainer()->get('thewulf7\friendloc\components\config\iConfig')->get('emailFrom'))
            ->setTo([$user->getEmail() => $user->getName()])
            ->setBody($body);

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

        if (!$mailer->send($message, $failures))
        {
            throw new \Exception(implode(',', $failures));
        }

        return true;
    }
}