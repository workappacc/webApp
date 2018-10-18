<?php

namespace AdminBundle\Controller;

use FrontBundle\Entity\FeedBack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FeedBackController extends Controller
{

    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/feedbacks", name="feedbacks_show")
     */
    public function showAction(Request $request)
    {
        $qb = $this->get('FrontBundle\Service\FeedBackManager')->getAllFeedBacks();
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('@Admin/FeedBack/show.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/feedbacks/{id}", name="feedback_show")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeedBackAction($id)
    {
        $feedBack = $this->get('FrontBundle\Service\FeedBackManager')->getFeedBack($id);

        return $this->render('@Admin/FeedBack/feedback.html.twig',[
            'feedback' => $feedBack
        ]);
    }

}