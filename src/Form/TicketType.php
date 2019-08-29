<?php

namespace App\Form;

use App\Entity\Tickets;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('type', ChoiceType::class, [
              'choices' => [
                'Task' => 'task',
                'Bug' => 'bug',
                ]])
            ->add('status', ChoiceType::class, [
              'choices' => [
                'New' => 'new',
                'In progress' => 'in progress',
                'Testing' => 'testing',
                'Done' => 'done',
                ]])
            ->add('assigned', EntityType::class, [
              'class' => User::class,
              'choice_label' => 'username',
              'choice_value' => 'id',
                ])
            ->add('description')
            ->add('file', FileType::class, [
                'label' => 'Brochure (PDF file)',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
