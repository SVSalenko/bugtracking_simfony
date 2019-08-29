<?php
namespace App\Controller;
use App\Entity\Tickets;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class CommentsController extends AbstractController
{
    /**
     * @Route("/ticket/{ticket_id}/new_comment", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserInterface $user): Response
    {
        $creatorId = $user->getId();
        $ticketId = $request->attributes->get('ticket_id');

        $form = $this->createForm(CommentType::class);
        $form->add('create', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = ($form->getData());
            $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($ticketId);
            $comment->setTicket($ticket);
            $comment->setCreatorId($creatorId);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('ticket', ['id' => $ticketId]);
        }
        return $this->render('comments/new.html.twig', [
            'form' => $form->createView(),
            'ticket_id' => $ticketId,
        ]);
    }

    /**
     * @Route("/ticket/{ticket_id}/comment/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comments $comment): Response
    {
        $ticketId = $request->attributes->get('ticket_id');
        $form = $this->createForm(CommentType::class, $comment);
        $form->add('create', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('ticket', ['id' => $ticketId]);
        }
        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/ticket/{ticket_id}/comment/{id}/delete", name="comment_delete")
     */
    public function delete(Request $request, Comments $comment)
    {
        $ticketId = $request->attributes->get('ticket_id');
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('ticket', ['id' => $ticketId]);
    }
}
