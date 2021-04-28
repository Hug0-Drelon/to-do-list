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
        'workout',
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
            $manager->persist($subtask);
            $secondTask->addSubtask($subtask);
        }
        $secondTask->setCategory($categoriesCollection[1]);
        $manager->persist($secondTask);
        
        $thirdTask = new Task();
        $thirdTask->setName('learn Vue.js');
        $thirdTask->setDeadline(new \DateTime("2021-07-01T00:00:00+00:00"));
        $thirdTaskSubtasksNames = [
            'go through documentation',
            'use it in a project',
        ];
        foreach ($thirdTaskSubtasksNames as $subtaskName) {
            $subtask = new Subtask();
            $subtask->setName($subtaskName);
            $manager->persist($subtask);
            $thirdTask->addSubtask($subtask);
        }
        $thirdTask->setCategory($categoriesCollection[2]);
        $manager->persist($thirdTask);

        $fourthTask = new Task();
        $fourthTask->setName('clean my keyboard');
        $fourthTask->setDeadline(new \DateTime("2021-05-01T10:00:00+00:00"));
        $fourthTask->setCategory($categoriesCollection[3]);
        $manager->persist($fourthTask);


        $manager->flush();
    }
}
