<?php

namespace App\Entity;

interface CreatedAtEntityInterface
{
    public function setCreatedAt(\DateTimeInterface $createdAt): CreatedAtEntityInterface;
}