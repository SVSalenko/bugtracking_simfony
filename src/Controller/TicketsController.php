<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\Projects;
use App\Form\TicketType;
use App\Form\ProjectType;
use App\Repository\TicketsRepository;
use App\Repository\CommentsRepository;
use App\Repository\ProjectsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @IsGranted("ROLE_USER")
 */
class TicketsController extends AbstractController
{
  /**
  * @Route("/ticket/show/{id}", name="ticket")
  */
  public function show($id,  CommentsRepository $CommentsRepository): Response
    {
      $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
      return $this->render('tickets/show.html.twig', [
      'ticket' => $ticket,
      'comments' => $CommentsRepository->findBy(['ticket' => $id]),
      ]);
  }

  /**
  * @Route("/project/{project_id}/ticket/new", name="/ticket/new")
  */
  public function new(Request $request, UserInterface $user)
  {
    $creatorId = $user->getId();
    $projectId = $request->attributes->get('project_id');

    $form = $this->createForm(TicketType::class);
    $form->add('create', SubmitType::class);
    $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid())
      {
        /*
        * @var UploadedFile $File
        */
        $File = $form['file']->getData();
          if ($File)
          {
            $orig_file_name = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
            //$safeFile = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $orig_file_name);
            $newFile = $orig_file_name.'-'.uniqid().'.'.$File->guessExtension();
              try {
                $File->move(
                $this->getParameter('brochures_directory'),
                $newFile
                );
                } catch (FileException $e) {
                // ... handle exception if something happens during file upload
              }

          }
        $ticket = ($form->getData());
        $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
        $ticket->setProject($project);
        $ticket->setCreatorId($creatorId);
        $ticket->setFile($newFile);
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
  * @Route("/project/{project_id}/ticket/edit/{id}", name="ticket_edit", methods={"GET","POST"})
  */
  public function edit(Request $request, Tickets $ticket): Response
  {
    $projectId = $request->attributes->get('project_id');
    $form = $this->createForm(TicketType::class, $ticket);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
      {
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('project_show', ['id' => $projectId]);
      }
    return $this->render('tickets/edit.html.twig', [
        'ticket' => $ticket,
        'form' => $form->createView(),
    ]);
}

  /**
  * @Route("/project/{project_id}/ticket/delete/{id}", name="ticket_delete", methods={"GET"})
  */
  public function delete(Request $request, Tickets $ticket)
  {
    $projectId = $request->attributes->get('project_id');

      $em = $this->getDoctrine()->getManager();
      $em->remove($ticket);
      $em->flush();
    return $this->redirectToRoute('project_show', ['id' => $projectId]);
  }

}
