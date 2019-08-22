<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{
    /**
     * @Route("/registr", name="registr")
     */
    public function new(Request $request)
    {
      $form = $this->createForm(RegistrType::class);
      $form->add('create', SubmitType::class);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $registr = ($form->getData());

        $em = $this->getDoctrine()->getManager();

        $em->persist($registr);
        $em->flush();

        return $this->redirectToRoute('projects');
      }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }
}
