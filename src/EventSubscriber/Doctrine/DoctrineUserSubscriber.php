<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DoctrineUserSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly UserManager $userManager
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        //Si pas d'ID donc ajout de nouveau User
        if (!$user->getId()) {
            $this->userManager->setRandomPassword($user);
        }
    }
}