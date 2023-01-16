<?php

namespace App\EventListener;



use App\Event\AddUserEvent;
use App\Event\ListAllUsersEvent;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class UserAddListener
{
    public function __construct(private LoggerInterface $logger)
    {

    }

    public function onUserAdd(AddUserEvent $event): void
    {
        $this->logger->debug(message:"cc je suis entrain d'écouter l'évenement user_add et une user vient d'être ajoutée et c'est ". $event->getPersonne()->getName());
    }

    public function onListAllUsers(ListAllUsersEvent $event): void
    {
        $this->logger->debug(message:"Le nombre de users dans la base est ". $event->getNbUser());
    }


    public function onListAllUsers2(ListAllUsersEvent $event): void
    {
        $this->logger->debug(message:"Le deuxième LISTENER avec le nombre des users dans la BD est ". $event->getNbUser());
    }


    #[NoReturn]
    public function logKernelRequest(KernelEvent $event): void
    {
        dd($event->getRequest());
    }
}