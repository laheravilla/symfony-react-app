<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var TokenGenerator */
    private $tokenGenerator;

    /** @var MailerInterface */
    private $mailer;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        MailerInterface $mailer
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            return;
        }

        // Hash password here
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

        // Create confirmation token
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());

        // Format username for email
        $username = ucwords(preg_replace("/\.|-|_/", " ", $user->getUsername()));

        // Send email here
        $email = (new Email())
            // (new TemplatedEmail())
            ->from(Address::fromString("{$username} <{$user->getEmail()}>"))
            ->to(Address::fromString("Yurniel Lahera <development.app.tester@gmail.com>"))
            ->subject("Time for Symfony Mailer from API Platform!")
            // ->htmlTemplate()
            // ->context()
            ->date(new \DateTime('now'))
            ->html("<p>Lorem Ipsum...</p>")
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new TransportException("An error occurred when sending mail");
        }
    }
}