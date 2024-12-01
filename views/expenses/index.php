<?php
// Get the module and action from the query string
$module = $_GET['module'] ?? null;
$action = $_GET['action'] ?? null;

// Define the base directory for models
$modelsDirectory = __DIR__ . '/../../models/';

// Check if the requested module exists in the models directory
if ($module && file_exists($modelsDirectory . ucfirst($module) . '.php')) {
    require_once $modelsDirectory . ucfirst($module) . '.php'; // Load the model (e.g., Expense.php)

    // Check if the requested action exists as a method in the model
    if ($action && method_exists($module, $action)) {
        // Call the action method (e.g., add())
        $module::$action(); 
    } else {
        echo "Action '$action' not found in module '$module'.";
    }
} else {
    echo "Module '$module' not found.";
}

$data=[];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Expense Tracker</h1>
    <a href="add.php?module=expense&action=add">Add Expense</a>
    <ul>
        <?php foreach ($data['expenses'] as $expense): ?>
            <li>
                <?= htmlspecialchars($expense['title']) ?> - $<?= htmlspecialchars($expense['amount']) ?>
                <a href="?module=expense&action=delete&id=<?= $expense['id'] ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
