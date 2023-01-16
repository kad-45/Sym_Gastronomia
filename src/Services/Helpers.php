<?php

namespace App\Services;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;


class Helpers
{

    public function __construct(private LoggerInterface $logger, private Security $security)
    {

    }

    public function sayCc(): string
    {
        $this->logger->info(message:'Je dis CC');
        return 'cc';
    }

    public function getUser(): User
    {
        if ($this->security->isGranted('ROLE_ADMIN'))
        {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
    }
}