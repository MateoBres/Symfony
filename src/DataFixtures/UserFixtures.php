<?php

namespace App\DataFixtures;

use App\Entity\UserFlock\User;

class UserFixtures extends BaseFixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const DEFAULT_USER_REFERENCE = 'default-user';
    public const DEFAULT_TEACHER_REFERENCE = 'teacher-user';

    protected function execute()
    {

        $userRepository = $this->manager->getRepository(User::class);

        if ($userRepository->count([]) > 0) {

            $admin = $userRepository->findOneBy([
               'username' => 'admin',
            ]);

            if ($admin) {
                $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
            }

            $user = $userRepository->findOneBy([
               'username' => 'user',
            ]);

            if ($user) {
                $this->addReference(self::DEFAULT_USER_REFERENCE, $user);
            }

            return;
        }

        /*
         * Admin User
         */
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setEmail('admin@email.it');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEnabled(true);
        $this->manager->persist($admin);

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);

        /*
         * Normal User
         */
        $defaultUser = new User();
        $defaultUser->setUsername('user');
        $defaultUser->setPassword($this->passwordEncoder->encodePassword($defaultUser, 'user'));
        $defaultUser->setEmail('user@email.it');
        $defaultUser->setRoles(['ROLE_DEFAULT']);
        $defaultUser->setEnabled(true);
        $this->manager->persist($defaultUser);

        $this->addReference(self::DEFAULT_USER_REFERENCE, $defaultUser);

        $this->manager->flush();

        /*
         * Teacher User
         */
        $user = new User();
        $user->setUsername('teacher');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'teacher'));
        $user->setEmail('teacher@email.it');
        $user->setRoles(['ROLE_TEACHER']);
        $user->setEnabled(true);
        $this->manager->persist($user);

        $this->addReference(self::DEFAULT_TEACHER_REFERENCE, $user);

        $this->manager->flush();

    }
}