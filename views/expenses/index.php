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
    <style>
        .button {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Expense Tracker</h1>
    <a href="http://localhost/Lab_10_models_blabla/views/expenses/add.php?module=Expense&action=add" class="button">Add Expense</a>
    <a href="http://localhost/Lab_10_models_blabla/views/expenses/categories.php" class="button">Manage Categories</a>
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
