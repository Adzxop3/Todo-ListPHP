<?php
namespace App\Controller;
use App\Repository\TaskRepository;
use App\Model\Task;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
class TaskController
{
    private TaskRepository $repository;
    private Environment $twig;
    public function __construct()
{
    $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
        $this->repository = new TaskRepository();
}
public function index()
{
    $filter = $_GET['filter'] ?? 'all';
    $tasks = $this->repository->findAll();

    if ($filter === 'completed') {
        $tasks = array_filter($tasks, fn($task) => $task['completed']);
    } elseif ($filter === 'active') {
        $tasks = array_filter($tasks, fn($task) => !$task['completed']);
    }

    echo $this->twig->render('task_list.html.twig', [
        'tasks' => $tasks,
        'filter' => $filter,
    ]);
}

public function add()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $priority = $_POST['priority'] ?? 'medium'; 

        if (!empty($title)) {
            $task = new Task();
            $task->setTitle($title);
            $task->setPriority($priority); 
            $this->repository->save($task); 
            header('Location: /projet-php/public/task/list'); 
            exit;
        }
    }

    echo $this->twig->render('task_add.html.twig');
}


public function edit()
{
    $id = $_GET['id'] ?? null;
    $task = $this->repository->findById($id);

    if (!$task) {
        echo "Tâche introuvable";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $priority = $_POST['priority'] ?? 'medium';
        $recurrence = $_POST['recurrence'] ?? 'none';

        $task->setTitle($title);
        $task->setPriority($priority);
        $task->setRecurrence($recurrence);

        $this->repository->update($task);

        header('Location: /projet-php/public/task/list');
        exit;
    }

    echo $this->twig->render('task_edit.html.twig', [
        'task' => $task
    ]);
}


public function delete()
{
    $id = $_GET['id'] ?? null;
    if ($id) {
        $this->repository->delete($id); 
        header('Location: /projet-php/public/task/list'); 
        exit;
    } else {
        echo "ID de tâche manquant.";
    }
}
public function toggleCompletion()
{
    $id = $_GET['id'] ?? null;

    if ($id) {
        $task = $this->repository->findById($id);

        if ($task) {
            $task->setCompleted(!$task->isCompleted()); 
            $this->repository->update($task); 
        }
    }

    header('Location: /projet-php/public/task/list'); 
    exit;
}



}
