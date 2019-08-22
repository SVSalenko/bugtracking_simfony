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

class ProjectsController extends AbstractController
{

    /**
     * @Route("/projects", name="projects")
     */
    public function index()
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAll();
        return $this->render('projects/index.html.twig', [
            'controller_name' => 'ProjectsController',
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
            'controller_name' => 'ProjectsController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/show/{id}", name="project/show")
     */
    public function show($id)
    {
        $project = $this->getDoctrine()->getRepository(Projects::class)->find($id);
        $tickets = $this->getDoctrine()->getRepository(Tickets::class)->findAll();
//        if(!$project){
//          throw $this->createNotFoundException('Project not found');
//        }
        return $this->render('projects/show.html.twig', [
            'controller_name' => 'ProjectsController',
            'project' => $project,
            'tickets' => $tickets,
        ]);
    }

}
