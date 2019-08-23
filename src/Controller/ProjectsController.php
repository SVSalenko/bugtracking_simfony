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
    public function new(Request $request)
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
    public function show($id, TicketsRepository $ticketsRepository): Response
    {
        $project = $this->getDoctrine()->getRepository(Projects::class)->find($id);
        //  $tickets = $this->getDoctrine()->getRepository(Tickets::class)->findAll();
//        if(!$project){
//          throw $this->createNotFoundException('Project not found');
//        }
        return $this->render('projects/show.html.twig', [
            'project' => $project,
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    /**
     * @Route("{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Projects $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }
        return $this->redirectToRoute('projects');
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
}
