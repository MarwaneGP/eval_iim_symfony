<?php

namespace App\MessageHandler;

use App\Message\AddPointsToActiveUsers;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsMessageHandler]
class AddPointsToActiveUsersHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(AddPointsToActiveUsers $message)
    {
        $users = $this->em->getRepository(User::class)->findBy(['isActive' => true]);

        foreach ($users as $user) {
            $user->setPoints($user->getPoints() + 10); // Or any logic you want
        }

        $this->em->flush();
    }
}
