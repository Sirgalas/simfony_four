<?php
declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Twig\Environment;

class ResetTokenSender
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var array
     */
    private $from;

    public function __construct(\Swift_Mailer $mailer,Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }
    public function send(Email $email,ResetToken $token):void
    {
        $message=(new \Swift_Message('Password resetting'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/reset.html.twig',[
                'token'=>$token->getToken()
            ]),'text/html');

        if(!$this->mailer->send($message)){
            throw new \RuntimeException('Unable to send message.');
        }
    }
}
