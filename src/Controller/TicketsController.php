<?php

namespace App\Controller;

use App\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TicketType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Projects;
use App\Form\ProjectType;
use App\Repository\ProjectsRepository;
use App\Repository\TicketsRepository;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/ticket/edit/{id}", name="ticket_edit")
     */
     public function edit(Request $request, Tickets $ticket): Response
     {
       $projectId = $request->attributes->get('project_id');
       $form = $this->createForm(TicketType::class, $ticket);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
           $ticket->setProject($project);
           $this->getDoctrine()->getManager()->flush();

           return $this->redirectToRoute('projects_show', ['id' => $projectId]);
       }

       return $this->render('tickets/edit.html.twig', [
           'ticket' => $ticket,
           'form' => $form->createView(),
           'project_id' => $projectId,
       ]);
   }


}
