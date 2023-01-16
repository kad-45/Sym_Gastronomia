<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ListAllUsersEvent extends Event
{
    const LIST_ALL_USERS_EVENT = 'users_list_all';

    public function __construct(private int $nbUser)
    {

    }

    /**
     * @return int
     */
    public function getNbUser(): int
    {
        return $this->nbUser;
    }
}