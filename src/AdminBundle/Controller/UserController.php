<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Post;
use AdminBundle\Entity\Role;
use AdminBundle\Entity\User;
use AdminBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/users", name="admin_users")
     */
    public function showAction()
    {
        $users = $this->get('AdminBundle\Service\UserManager')->getAllUsers();
        
        return $this->render('@Admin\User\show.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/users/add", name="admin_users_add")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('AdminBundle\Service\UserManager')->addUser($user);
            
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('@Admin\User\add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/users/edit/{id}", name="edit_user")
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository('AdminBundle:User')->find($id);
        if (!$user) {
            return $this->redirectToRoute('admin_users');
        }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('AdminBundle\Service\UserManager')->editUser($user);

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('@Admin\User\edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/users/delete/{id}", name="delete_user")
     */
    public function deleteAction($id)
    {
        $this->get('AdminBundle\Service\UserManager')->deleteUser($id);
        
        return $this->redirectToRoute('admin_users');
    }
}
