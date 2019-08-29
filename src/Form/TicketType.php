<?php

namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->add('assigned', ChoiceType::class, [
              'choices' => [
                'Sergey' => '4',
                ]])
            ->add('description')
            ->add('file', FileType::class, ['data_class' => null])
            ->add('orig_file_name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
