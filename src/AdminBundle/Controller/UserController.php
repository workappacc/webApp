<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\User;
use AdminBundle\Form\UserType;
use AdminBundle\Security\Voter\PageVoter;
use AdminBundle\Security\Voter\UserVoter;
use AdminBundle\Service\RoleManager;
use AdminBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    /**
     *
     *
     * @Route("/admin/users", name="admin_users")
     */
    public function showAction(UserManager $userManager)
    {
        if (false === $this->isGranted(PageVoter::USERS)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $users = $userManager->getAllUsers();
        
        return $this->render('@Admin\User\show.html.twig', [
            'users' => $users
        ]);
    }

    /**
     *
     * @Route("/admin/users/add", name="admin_users_add")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, UserManager $userManager, RoleManager $roleManager)
    {
        $user = new User();
        $roles = $roleManager->getAllRoles();
        $roles['roles_disabled'] = false;
        $form = $this->createForm(UserType::class, $user, $roles);

        $form->handleRequest($request);

        if (false === $this->isGranted(UserVoter::ADD, $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->addUser($user);
            
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('@Admin\User\add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/admin/users/edit/{id}", name="edit_user")
     */
    public function editAction(Request $request, $id, UserManager $userManager, RoleManager $roleManager)
    {
        $roles = $roleManager->getAllRoles();
        $roles['roles_disabled'] = true;
        $user = $this->getDoctrine()->getRepository('AdminBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted(UserVoter::EDIT, $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $this->createForm(UserType::class, $user, $roles);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->editUser($user);

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('@Admin\User\edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/admin/users/delete/{id}", name="delete_user")
     */
    public function deleteAction($id, UserManager $userManager)
    {
        $user = $this->getDoctrine()->getRepository('AdminBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted(UserVoter::DELETE, $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $userManager->deleteUser($user);
        
        return $this->redirectToRoute('admin_users');
    }
}
