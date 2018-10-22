<?php

namespace AdminBundle\Command;

use AdminBundle\Entity\Role;
use AdminBundle\Entity\User;
use AdminBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCreateAdminUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create-admin-user')
            ->setDescription('Create user with role ADMIN');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $admin = $em->getRepository(User::class)->findBy([
            'username' => 'admin'
        ]);

        if (!$admin) {
            $role = $container->get('AdminBundle\Service\RoleManager')->isRoleExists(RoleManager::ROLE_ADMIN);
            if (!$role) {
                $role = new Role(RoleManager::ROLE_ADMIN);
            }

            $em->persist($role);

            $user = new User();
            $user->setUsername('admin');
            $user->setEmail('admin@admin.com');
            $password = $container->get('security.password_encoder')->encodePassword(
                $user,
                'admin'
            );
            $user->setPassword($password);
            $user->addRole($role);
            $em->persist($user);

            $em->flush();
            $output->writeln('Admin user is created.');
        } else {
            $output->writeln('Admin users exists');
        }
    }
}
