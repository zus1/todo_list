<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaksFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    /**
     * @Route("/add-task", name="add")
     */
    public function add(): Response
    {
        $form = $this->createForm(TaksFormType::class, new Task());

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
