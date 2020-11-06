<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaksFormType;
use App\Services\TaskService;
use App\Services\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('list');
    }

    /**
     * @param Request $request
     * @Route("/add-task", name="add")
     * @return Response
     */
    public function add(Request $request): Response
    {
        $task = new Task();
        $task->setStatus(TaskService::STATUS_PENDING);
        $form = $this->createForm(TaksFormType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Task successfully created'
            );

            return $this->redirectToRoute('add');
        }

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $userId
     * @param UserService $userService
     * @param TaskService $taskService
     * @Route("/list/{userId}", name="list", defaults={"userId"=1})
     * @return Response
     */
    public function list(int $userId, UserService $userService, TaskService $taskService) : Response {
        $isAdmin = $userService->isAdmin($userId);
        $tasksList = $taskService->getTasksForList($userId, $isAdmin);

        return $this->render('task/list.html.twig', ['tasks_list' => $tasksList, 'is_admin' => $isAdmin]);
    }

    /**
     * @param int $taskId
     * @param int $newStatus
     * @param ValidatorInterface $validator
     * @param TaskService $taskService
     * @return JsonResponse
     * @Route("/update/task/{taskId}/status/{newStatus}", name="status-update")
     */
    public function ajaxUpdateStatus(int $taskId, int $newStatus, ValidatorInterface $validator, TaskService $taskService): Response {
        $errorData = array();
        try {
            $this->validateAjaxTaskUpdate($validator, "status", $newStatus, "Could not update task status", $errorData);
            $taskService->updateTaskStatus($taskId, $newStatus, $errorData);
        } catch(Exception $e) {
            return new JsonResponse(['error' => 1, "message" => $e->getMessage(), "error_data" => $errorData]);
        }

        return new JsonResponse(['error' => 0, "message" => "Status updated"]);
    }

    /**
     * @param int $taskId
     * @param int $newAssign
     * @param ValidatorInterface $validator
     * @param TaskService $taskService
     * @return JsonResponse
     * @Route("/update/task/{taskId}/assign/{newAssign}", name="assign-update")
     */
    public function ajaxUpdateAssign(int $taskId, int $newAssign, ValidatorInterface $validator, TaskService $taskService): Response {
        $errorData = array();
        try {
            $this->validateAjaxTaskUpdate($validator, "assigned_to", $newAssign, "Could not update task assigned to", $errorData);
            $taskService->updateTaskAssign($taskId, $newAssign);
        } catch(Exception $e) {
            return new JsonResponse(['error' => 1, "message" => $e->getMessage(), "error_data" => $errorData]);
        }

        return new JsonResponse(['error' => 0, "message" => "Assign to updated"]);
    }

    private function validateAjaxTaskUpdate(ValidatorInterface $validator, string $propertyName, int $propertyValue, string $errorMessage, array &$error_data) {
        $failed = $validator->validatePropertyValue((new Task()), $propertyName, $propertyValue);
        if(count($failed) > 0) {
            $error_data = $failed;
            throw new Exception($errorMessage);
        }
    }
}
