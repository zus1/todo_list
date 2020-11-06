<?php


namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    const ADMIN_ROLE = 1;
    const USER_ROLE = 2;

    private $repository;

    public function __construct(EntityManagerInterface $manager) {
        $this->repository = $manager->getRepository(User::class);
    }

    public function isAdmin(int $userId) {
        $admin = $this->repository->findOneBy(array(
            'id' => $userId,
            'role' => self::ADMIN_ROLE
        ));

        if($admin) {
            return true;
        }

        return false;
    }
}