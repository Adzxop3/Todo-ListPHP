<?php

namespace App\Repository;

use App\Model\Task;
use PDO;

class TaskRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=todolist', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function findAll(): array
{
    $stmt = $this->db->query('SELECT * FROM tasks ORDER BY priority DESC, id ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function findById($id): ?Task
{
    $stmt = $this->db->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $task = new Task();
        $task->setId($result['id']);
        $task->setTitle($result['title']);
        $task->setCompleted((bool)$result['completed']);
        $task->setPriority($result['priority']);
        return $task;
    }

    return null;
}


public function save(Task $task): void
{
    $stmt = $this->db->prepare('INSERT INTO tasks (title, completed, priority, recurrence, last_occurrence) VALUES (:title, :completed, :priority, :recurrence, :last_occurrence)');
    $stmt->bindValue(':title', $task->getTitle());
    $stmt->bindValue(':completed', $task->isCompleted(), PDO::PARAM_BOOL);
    $stmt->bindValue(':priority', $task->getPriority());
    $stmt->bindValue(':recurrence', $task->getRecurrence());
    $stmt->bindValue(':last_occurrence', $task->getLastOccurrence()?->format('Y-m-d H:i:s'));
    $stmt->execute();
}




public function update(Task $task): void
{
    $stmt = $this->db->prepare('UPDATE tasks SET title = :title, completed = :completed, priority = :priority, recurrence = :recurrence, last_occurrence = :last_occurrence WHERE id = :id');
    $stmt->bindValue(':title', $task->getTitle());
    $stmt->bindValue(':completed', $task->isCompleted(), PDO::PARAM_BOOL);
    $stmt->bindValue(':priority', $task->getPriority());
    $stmt->bindValue(':recurrence', $task->getRecurrence());
    $stmt->bindValue(':last_occurrence', $task->getLastOccurrence()?->format('Y-m-d H:i:s'));
    $stmt->bindValue(':id', $task->getId(), PDO::PARAM_INT);
    $stmt->execute();
}



    public function delete($id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function handleRecurringTasks(): void
{
    $tasks = $this->db->query("SELECT * FROM tasks WHERE recurrence != 'none'")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tasks as $taskData) {
        $task = new Task();
        $task->setId($taskData['id']);
        $task->setTitle($taskData['title']);
        $task->setPriority($taskData['priority']);
        $task->setRecurrence($taskData['recurrence']);
        $task->setLastOccurrence(new \DateTime($taskData['last_occurrence']));

        $now = new \DateTime();
        $nextOccurrence = $task->getLastOccurrence();

        if ($task->getRecurrence() === 'daily') {
            $nextOccurrence->modify('+1 day');
        } elseif ($task->getRecurrence() === 'weekly') {
            $nextOccurrence->modify('+1 week');
        } elseif ($task->getRecurrence() === 'monthly') {
            $nextOccurrence->modify('+1 month');
        }

        if ($now >= $nextOccurrence) {
            $newTask = new Task();
            $newTask->setTitle($task->getTitle());
            $newTask->setPriority($task->getPriority());
            $this->save($newTask);

            $task->setLastOccurrence($now);
            $this->update($task);
        }
    }
}

}
?>