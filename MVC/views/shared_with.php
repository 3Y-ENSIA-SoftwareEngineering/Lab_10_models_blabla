<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('./../controllers/ExpenseController.php');

$conn = new mysqli("localhost", "root", "", "expense_tracker");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$expenseController = new ExpenseController($conn);

// Redirect to signup if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sharedExpenses = $expenseController->getSharedExpenses($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Expenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffe6f0; /* Light pink background */
            color: #333; /* Dark text for readability */
        }
        h3 {
            color: #ff69b4; /* Hot pink for headings */
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff; /* White table background */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ff69b4; /* Pink border */
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff1493; /* Deeper pink for table headers */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #ffe6f0; /* Light pink for alternating rows */
        }
        tr:hover {
            background-color: #ffccd5; /* Lighter pink on hover */
        }
    </style>
</head>
<body>
    <h3>Shared Expenses</h3>
    <?php if (!empty($sharedExpenses)): ?>
        <table>
            <thead>
                <tr>
                    <th>Expense ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Shared With (Email)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sharedExpenses as $expense): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['expense_id']) ?></td>
                        <td><?= htmlspecialchars($expense['title']) ?></td>
                        <td><?= htmlspecialchars($expense['category']) ?></td>
                        <td><?= htmlspecialchars($expense['date']) ?></td>
                        <td><?= htmlspecialchars($expense['amount']) ?></td>
                        <td><?= htmlspecialchars($expense['shared_with_name'] . ' (' . $expense['shared_with_email'] . ')') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No expenses have been shared yet.</p>
    <?php endif; ?>
</body>
</html>
