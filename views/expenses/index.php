<?php
// Get the module and action from the query string
require_once __DIR__ . '/../../models/Expense.php';
require_once __DIR__ . '/../../models/BaseModel.php';



// Initialize the data array
$data = [
    'expenses' => [] // Ensure expenses is an empty array by default
];
$expenseModel = new Expense();
$data['expenses'] = $expenseModel->getAll(); // Call the instance method


?>
<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Expense Tracker</h1>
    <a href="http://localhost/Lab_10_models_blabla/views/expenses/add.php?module=Expense&action=add">Add Expense</a>
    <ul>
        <?php if (!empty($data['expenses'])): ?>
            <?php foreach ($data['expenses'] as $expense): ?>
                <li>
                    <?= htmlspecialchars($expense['title']) ?> - $<?= htmlspecialchars($expense['amount']) ?>
                    <a href="?module=expense&action=delete&id=<?= $expense['id'] ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No expenses found.</p>
        <?php endif; ?>
    </ul>
</body>
</html>
