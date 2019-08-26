<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\Projects;
use App\Form\TicketType;
use App\Form\ProjectType;
use App\Repository\TicketsRepository;
use App\Repository\ProjectsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TicketsController extends AbstractController
{
  /**
  * @Route("/ticket/show/{id}", name="ticket")
  */
  public function show($id)
    {
      $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
      return $this->render('tickets/show.html.twig', [
      'ticket' => $ticket,
      ]);
  }

  /**
  * @Route("/ticket/new", name="/ticket/new", methods={"GET","POST"})
  */
  public function new(Request $request): Response
  {
    $projectId = $request->attributes->get('project_id');

    $form = $this->createForm(TicketType::class);
    $form->add('create', SubmitType::class);
    $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid())
      {
        $ticket = ($form->getData());
        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();

        return $this->redirectToRoute('project_show', ['id' => $projectId]);
      }

    return $this->render('tickets/new.html.twig', [
    'form' => $form->createView(),
    'project_id' => $projectId,
    ]);
  }

  /**
  * @Route("/ticket/edit/{id}", name="ticket_edit", methods={"GET","POST"})
  */
  public function edit(Request $request, Tickets $ticket): Response
  {
    $form = $this->createForm(TicketType::class, $ticket);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
      {
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('projects');
      }
    return $this->render('tickets/edit.html.twig', [
        'ticket' => $ticket,
        'form' => $form->createView(),
    ]);
}

  /**
  * @Route("/ticket/delete/{id}", name="ticket_delete", methods={"DELETE"})
  */
  public function delete(Request $request, Tickets $ticket): Response
  {
    if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token')))
    {
      $em = $this->getDoctrine()->getManager();
      $em->remove($ticket);
      $em->flush();
    }
    return $this->redirectToRoute('project_show', ['id' => $projectId]);
  }

}
