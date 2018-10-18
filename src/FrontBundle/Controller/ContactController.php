<?php

namespace FrontBundle\Controller;

use FrontBundle\Entity\FeedBack;
use FrontBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="front_contact")
     */
    public function contactAction(Request $request)
    {
        $feedBack = new FeedBack();
        $form = $this->createForm(ContactType::class, $feedBack);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.services.queue_manager')->addToQueues($feedBack);

            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('@Front/Contact/contact.html.twig',[
            'form' => $form->createView(),
            
        ]);
    }
}