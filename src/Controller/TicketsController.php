<?php

namespace App\Controller;

use App\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TicketType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketsController extends AbstractController
{
    /**
     * @Route("/ticket/show/{id}", name="ticket")
     */
    public function show($id)
    {
        $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
        return $this->render('tickets/show.html.twig', [
            'controller_name' => 'TicketsController',
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/ticket/new", name="/ticket/new")
     */
      public function new(Request $request)
      {
        $form = $this->createForm(TicketType::class);
        $form->add('create', SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          $ticket = ($form->getData());

          $em = $this->getDoctrine()->getManager();

          $em->persist($ticket);
          $em->flush();

          return $this->redirectToRoute('projects');
        }

        return $this->render('tickets/new.html.twig', [
            'controller_name' => 'TicketsController',
            'form' => $form->createView(),
        ]);
    }

}
