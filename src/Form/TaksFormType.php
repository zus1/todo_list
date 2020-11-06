<?php

namespace App\Form;

use App\Entity\Task;

use App\Entity\User;
use App\Services\TaskService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaksFormType extends AbstractType
{
    private $taskService;

    public function __construct(TaskService $service) {
        $this->taskService = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $assignedToChoices  = $this->taskService->makeFormAssignOptionsArray();

        $builder
            ->add('name', TextType::class)
            ->add('assigned_to', ChoiceType::class, [
              'choices' => $assignedToChoices
            ])
            ->add('description', TextareaType::class)
            ->add('status', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Create']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
