<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Tickets;
use App\Repository\ProjectsRepository;
use App\Repository\TicketsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class ProjectsController extends AbstractController
{

    /**
     * @Route("/projects", name="projects")
     */
    public function index()
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAll();
        return $this->render('projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/projects/new", name="projects/new")
     */
    public function new(Request $request): Response
    {
      $form = $this->createForm(ProjectType::class);
      $form->add('create', SubmitType::class);
      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $project = ($form->getData());
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();

        return $this->redirectToRoute('projects');
      }


        return $this->render('projects/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/show/{id}", name="project_show")
     */
     public function show($id, Projects $project, TicketsRepository $ticketsRepository): Response
     {
         return $this->render('projects/show.html.twig', [
             'project' => $project,
             'tickets' => $ticketsRepository->findBy(['project' => $id]),
         ]);
     }

    /**
    * @Route("project/edit/{id}", name="project_edit", methods={"GET","POST"})
    */
   public function edit(Request $request, Projects $project): Response
   {
       $form = $this->createForm(ProjectType::class, $project);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $this->getDoctrine()->getManager()->flush();
           return $this->redirectToRoute('projects');
       }
       return $this->render('projects/edit.html.twig', [
           'project' => $project,
           'form' => $form->createView(),
       ]);
   }

   /**
    * @Route("project/delete/{id}", name="project_delete")
    */
   public function delete(Projects $project)
   {
           $em = $this->getDoctrine()->getManager();
           $em->remove($project);
           $em->flush();
       return $this->redirectToRoute('projects');
   }

}
