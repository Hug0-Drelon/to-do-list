<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Return all tasks with associated subtasks and categories with one query
     * 
     * @return Task[]|null Returns an array of Task objects
     */
    public function findWithCategoryAndSubtasks()
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.category', 'u')
            ->addSelect('u')
            ->leftJoin('t.subtasks', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Return a task with associated subtasks and category with one query
     *
     * @param int $id
     * @return Task|null
     */
    public function findOneWithCategoryAndSubtasks(int $id): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('t.category', 'u')
            ->addSelect('u')
            ->leftJoin('t.subtasks', 's')
            ->addSelect('s')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
