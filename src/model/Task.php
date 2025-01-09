<?php

namespace App\Model;

class Task
{
    private ?int $id = null;
    private string $title;
    private bool $completed = false; 
    private string $priority = 'medium'; 
    private string $recurrence = 'none'; 
    private ?\DateTime $lastOccurrence = null; 

    public function getRecurrence(): string
    {
        return $this->recurrence;
    }

    public function setRecurrence(string $recurrence): void
    {
        $this->recurrence = $recurrence;
    }

    public function getLastOccurrence(): ?\DateTime
    {
        return $this->lastOccurrence;
    }

    public function setLastOccurrence(?\DateTime $lastOccurrence): void
    {
        $this->lastOccurrence = $lastOccurrence;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }
}
