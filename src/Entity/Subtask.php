<?php

namespace App\Entity;

use App\Repository\SubtaskRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubtaskRepository::class)
 */
class Subtask
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("task_get")
     * @Groups("subtask_get")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("task_get")
     * @Groups("subtask_get")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("task_get")
     * @Groups("subtask_get")
     */
    private $achieved;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="subtasks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("subtask_get")
     */
    private $task;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAchieved(): ?bool
    {
        return $this->achieved;
    }

    public function setAchieved(bool $achieved): self
    {
        $this->achieved = $achieved;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }
}
