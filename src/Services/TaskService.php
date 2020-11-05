<?php
declare(strict_types = 1);

namespace App\Services;

use App\Dto\ViewTask;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TaskService
{
    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_CANCELED = 4;
    const STATUS_CLOSED = 3;
    const STATUS_COMPLETED = 5;

    private $entityManager;
    private $userRepository;
    private $repository;

    public function __construct(EntityManagerInterface $manager) {
        $this->entityManager = $manager;
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->repository = $this->entityManager->getRepository(Task::class);
    }

    private function getAvailableStatuses() {
        return array(
            self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_CANCELED, self::STATUS_CLOSED, self::STATUS_COMPLETED,
        );
    }

    private function getAdminOnlyStatuses() {
        return array(
            self::STATUS_CANCELED, self::STATUS_CLOSED
        );
    }

    private function getStatusToDbMapping() {
        return array(
            self::STATUS_PENDING => "PENDING",
            self::STATUS_IN_PROGRESS => "IN PROGRESS",
            self::STATUS_COMPLETED => "COMPLETED",
            self::STATUS_CLOSED => "CLOSED",
            self::STATUS_CANCELED => "CANCELED",
        );
    }

    public function makeFormAssignOptionsArray() {
        $allUsers = $this->userRepository->findAll();
        $choices = array();
        array_walk($allUsers, function(User $user) use(&$choices) {
            $choices[$user->getName()] = $user->getId();
        });

        return $choices;
    }

    public function getTasksForList(int $userId, bool $isAdmin) {
        $tasks = $this->repository->loadTasksForList($userId, $isAdmin);
        $modifiedTasks = $this->modifyTasksForDisplay($tasks, $isAdmin);
        return $modifiedTasks;
    }

    private function modifyTasksForDisplay(array $tasks, bool $isAdmin) {
        $modifiedTasks = array();
        array_walk($tasks, function(Task $task) use (&$modifiedTasks, $isAdmin) {
            $viewDto = new ViewTask();
            $viewDto->setName($task->getName());
            $viewDto->setDescription($task->getDescription());
            $viewDto->setStatus($this->addStatusOptions($task, $isAdmin));
            $viewDto->setAssigned($this->addAssignOptions($task, $isAdmin));
            $modifiedTasks[] = $viewDto;
        });

        return $modifiedTasks;
    }

    private function addAssignOptions(Task $task, bool $isAdmin) {
        if($isAdmin === false) {
            $user = $this->userRepository->find($task->getAssignedTo());
            return $user->getName();
        }

        $allUsers = $this->userRepository->findAll();
        $select = "<SELECT id='assign-select-{$task->getId()}' class='form-control' onchange='updateTaskAssign(this.id)'>";
        array_walk($allUsers, function (User $user) use(&$select, $task) {
            if((int)$task->getAssignedTo() === (int)$user->getId()) {
                $select .= sprintf("<option value='%d' selected>%s</option>", $user->getId(), $user->getName());
            } else {
                $select .= sprintf("<option value='%d'>%s</option>", $user->getId(), $user->getName());
            }
        });
        $select .= "</SELECT>";

        return $select;
    }

    private function addStatusOptions(Task $task, bool $isAdmin) {
        $availableStatuses = $this->getAvailableStatuses();
        $select = $this->disableStatusChangeIfNeeded($task, "<SELECT id='status-select-{$task->getId()}' class='form-control' onchange='updateTaskStatus(this.id)'", $isAdmin) . ">";
        $select .= ">";

        array_walk($availableStatuses, function (int $numStatus) use(&$select, $task, $isAdmin) {
            if((int)$task->getStatus() === $numStatus) {
                $select .= sprintf("<option value='%d' selected>%s</option>", $numStatus, $this->getStatusToDbMapping()[$numStatus]);
            } else {
                $addOption = true;
                if($isAdmin !== true) {
                    if(in_array($numStatus, $this->getAdminOnlyStatuses())) {
                        $addOption = false;
                    }
                }
                if($addOption === true) {
                    $select .= sprintf("<option value='%d'>%s</option>", $numStatus, $this->getStatusToDbMapping()[$numStatus]);
                }
            }
        });
        $select .= "</SELECT>";

        return $select;
    }

    private function disableStatusChangeIfNeeded(Task $task, string $select, bool $isAdmin) {
        if($isAdmin !== true && in_array($task->getStatus(), $this->getAdminOnlyStatuses())) {
            $select .= " disabled";
        }

        return $select;
    }

    public function updateTaskStatus(int $taskId, int $newStatus) {
        $task = $this->findTaskForUpdate($taskId);
        $task->setStatus($newStatus);
        $this->entityManager->flush();
    }

    public function updateTaskAssign(int $taskId, int $newAssign) {
        $task = $this->findTaskForUpdate($taskId);
        $task->setAssignedTo($newAssign);
        $this->entityManager->flush();
    }

    private function findTaskForUpdate(int $taskId) {
        $task = $this->repository->find($taskId);
        if(empty($task)) {
            throw new Exception("Could not find task with id " . $taskId);
        }

        return $task;
    }
}