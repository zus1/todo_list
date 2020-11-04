<?php


namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    const STATUS_PENDING = "PENDING";
    const STATUS_IN_PROGRESS = "IN PROGRESS";
    const STATUS_CANCELED = "CANCELED";
    const STATUS_CLOSED = "CLOSED";
    const STATUS_COMPLETED = "COMPLETED";

    private $userRepository;

    public function __construct(EntityManagerInterface $manager) {
        $this->userRepository = $manager->getRepository(User::class);
    }

    private function getAvailableStatuses() {
        return array(
            self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_CANCELED, self::STATUS_CLOSED, self::STATUS_COMPLETED,
        );
    }

    private function getStatusToDbMapping() {
        return array(
            self::STATUS_PENDING => 1,
            self::STATUS_IN_PROGRESS => 2,
            self::STATUS_COMPLETED => 3,
            self::STATUS_CLOSED => 4,
            self::STATUS_CANCELED => 5,
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

    public function makeFormStatusOptionsArray() {
        $choices = array();
        $availableStatuses = $this->getAvailableStatuses();
        array_walk($availableStatuses, function(string $choice) use(&$choices) {
           $choices[$choice] = $this->getStatusToDbMapping()[$choice];
        });

        return $choices;
    }
}