<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface AuthoredEntityInterface
{
    public function getAuthor(): UserInterface;

    public function setAuthor(UserInterface $author): AuthoredEntityInterface;
}