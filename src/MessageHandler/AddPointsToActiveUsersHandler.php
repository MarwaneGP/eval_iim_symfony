<?php

namespace App\MessageHandler;

use App\Message\AddPointsToActiveUsers;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsMessageHandler]
class AddPointsToActiveUsersHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(AddPointsToActiveUsers $message)
    {
        $users = $this->em->getRepository(User::class)->findBy(['actif' => true]);

        foreach ($users as $user) {
            $user->setPoints($user->getPoints() + 1000);
        }

        $this->em->flush();
    }
}
