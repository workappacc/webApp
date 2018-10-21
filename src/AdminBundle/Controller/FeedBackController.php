<?php

namespace AdminBundle\Controller;

use AdminBundle\Security\Voter\FeedBackVoter;
use AdminBundle\Security\Voter\PageVoter;
use FrontBundle\Entity\FeedBack;
use FrontBundle\Service\FeedBackManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FeedBackController extends Controller
{
    const PAGE_POSTS_LIMIT = 5;

    const DEFAULT_POSTS_PAGE_NUMBER = 1;
    /**
     *
     * @Route("/admin/feedbacks", name="feedbacks_show")
     */
    public function showAction(Request $request, FeedBackManager $feedBackManager)
    {
        if (false === $this->isGranted(PageVoter::FEEDBACKS)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $qb = $feedBackManager->getAllFeedBacks();
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', self::DEFAULT_POSTS_PAGE_NUMBER),
            self::PAGE_POSTS_LIMIT
        );

        return $this->render('@Admin/FeedBack/show.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     *
     * @Route("/admin/feedbacks/{id}", name="feedback_show")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeedBackAction($id, FeedBackManager $feedBackManager)
    {
        $feedBack = $feedBackManager->getFeedBack($id);

        if (false === $this->isGranted(FeedBackVoter::VIEW, $feedBack)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return $this->render('@Admin/FeedBack/feedback.html.twig',[
            'feedback' => $feedBack
        ]);
    }

}