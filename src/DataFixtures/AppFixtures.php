<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Subtask;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $categoryNameProvider = [
        'personal',
        'work',
        'study',
        'chores',
    ];

    public function load(ObjectManager $manager)
    {
        //Create some categories
        $categoriesCollection = [];
        foreach ($this->categoryNameProvider as  $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categoriesCollection[] = $category;
        }

        //Create some tasks
        $firstTask = new Task();
        $firstTask->setName('make tax form');
        $firstTask->setDeadline(new \DateTime("2021-06-01T00:00:00+00:00"));
        $firstTask->setCategory($categoriesCollection[0]);
        $manager->persist($firstTask);

        $secondTask = new Task();
        $secondTask->setName('find a job');
        $secondTaskSubtasksNames = [
            'write my resume',
            'find interesting companies',
        ];
        foreach ($secondTaskSubtasksNames as $subtaskName) {
            $subtask = new Subtask();
            $subtask->setName($subtaskName);
            $secondTask->addSubtask($subtask);
        }
        $manager->persist($secondTask);

        $manager->flush();
    }
}
