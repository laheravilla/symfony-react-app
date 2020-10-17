<?php

namespace App\Email;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Twig\Environment;

class Mailer
{
    /** @var MailerInterface */
    private $mailer;

    /** @var Environment */
    private $twig;
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(MailerInterface $mailer, Environment $twig, ObjectNormalizer $normalizer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->normalizer = $normalizer;
    }

    public function sendConfirmationEmail(User $user)
    {
        // Format username
        $fullName= ucwords($user->getFullName());

        // Convert user object to array
        $normalizedUser = $this->normalizer->normalize($user);

        // Create associative array with pertinent props
        $props = ["username", "fullName", "confirmationToken"];
        $context = [];
        foreach (array_keys($normalizedUser) as $key) {
            if (in_array($key, $props)) {
                $context[$key] = $normalizedUser[$key];
            };
        }

        $email = (new TemplatedEmail())
            ->from(Address::fromString("Yurniel Lahera <development.app.tester@gmail.com>"))
            ->to(Address::fromString("{$fullName} <{$user->getEmail()}>"))
            ->subject("{$fullName}, you need a mail confirmation!")
            ->htmlTemplate("email/confirmation.html.twig")
            ->context($context)
            ->date(new \DateTime('now'))
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new TransportException();
        }
    }
}