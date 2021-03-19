<?php
/**
 * Created by PhpStorm.
 * User: yash
 */

namespace App\EventListener\UserFlock;


use App\Entity\UserFlock\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function prePersist(User $user, LifecycleEventArgs $args)
    {
        $this->preSave($user, $args);
    }

    public function preUpdate(User $user, LifecycleEventArgs $args)
    {
        $this->preSave($user, $args);
    }

    private function preSave(User $user, LifecycleEventArgs $args)
    {
        if ($user->getId() === 1) {
            $userRoles = $user->getRoles();
            $userRoles[] = 'ROLE_SUPER_ADMIN';
            $user->setRoles($userRoles);
        }

        if ($user->getPlainPassword()) {

            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPlainPassword())
            );
        }
    }
}