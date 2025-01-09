<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Repository\TaskRepository;

$repository = new TaskRepository();
$repository->handleRecurringTasks();
if (!in_array('mysql', PDO::getAvailableDrivers())) {
    die('Le driver PDO MySQL n’est pas disponible.');
}

?>