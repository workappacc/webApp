<?php

namespace AdminBundle\Controller;

use AdminBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function indexAction()
    {
        if (false === $this->isGranted(PageVoter::MAIN)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return $this->render('@Admin\Default\default.html.twig');
    }
}
