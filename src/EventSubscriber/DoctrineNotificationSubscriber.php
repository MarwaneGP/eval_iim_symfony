<?php

namespace App\EventSubscriber;

use App\Entity\Produit;
use App\Entity\Notification;
use App\Repository\UserRepository;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNotificationSubscriber implements EventSubscriber
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->createNotification($args, 'created');
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->createNotification($args, 'updated');
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->createNotification($args, 'deleted');
    }

    private function createNotification(LifecycleEventArgs $args, string $action): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Produit) {
            return;
        }

        $admin = $this->userRepository->findOneByRole('ROLE_ADMIN');

        if (!$admin) {
            return;
        }

        $notification = new Notification();
        $notification->setLabel(sprintf(
            'Product %s: %s on %s',
            $action,
            $entity->getNom(),
            (new \DateTime())->format('Y-m-d H:i:s')
        ));
        $notification->setUser($admin);

        $this->em->persist($notification);
        $this->em->flush();
    }
}
