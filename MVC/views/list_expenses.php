<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './../controllers/ExpenseController.php';


// Database connection
$conn = new mysqli("localhost", "root", "", "expense_tracker");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Create controller instance
$expenseController = new ExpenseController($conn);

// Handle delete request
if (isset($_POST['delete'])) {
    $expense_id = intval($_POST['expense_id']);
    $deleted = $expenseController->deleteExpense($user_id, $expense_id);

    if ($deleted) {
        $message = "Expense successfully deleted.";
    } else {
        $message = "An error occurred during deletion.";
    }
}

// Fetch filtered expenses
$category = $_GET['category'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

$expenses = $expenseController->getExpenses($user_id, $category, $start_date, $end_date);

// Get the total and count
$total = 0;
$count = count($expenses);  
foreach ($expenses as $expense) {
    $total += $expense['amount'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Expenses</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #fce4ec; /* Light pink background */
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.filter-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.expense-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.expense-table th,
.expense-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.expense-table th {
    background-color: #f8bbd0; /* Light pink header */
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 5px;
}

.btn-edit {
    background-color: #f06292; /* Pink */
    color: white;
}

.btn-delete {
    background-color: #e91e63; /* Darker pink */
    color: white;
}

.btn-share {
    background-color: #c2185b; /* Strong pink */
    color: white;
}

.summary {
    margin-top: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
}

    </style>
</head>
<body>

<div class="container">
    <div class="filter-container">
        <form method="GET" class="filter-form" action="list_expenses.php">
            <?php
            // Fetch categories
            $categories = $expenseController->getCategories($user_id);
            ?>

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="start_date" value="<?php echo $start_date ?? ''; ?>" placeholder="Start Date">
            <input type="date" name="end_date" value="<?php echo $end_date ?? ''; ?>" placeholder="End Date">
            <button type="submit" class="btn">Filter</button>
        </form>
    </div>

    <div class="message"><?php echo isset($message) ? $message : ''; ?></div>

    <table class="expense-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?php echo htmlspecialchars($expense['id']); ?></td>
                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($expense['title']); ?></td>
                <td>
                <a href="edit_expense.php?expense_id=<?php echo $expense['id']; ?>" class="btn btn-edit">Edit</a>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <a href="share_expense.php?id=<?php echo $expense['id']; ?>" class="btn btn-share">Share</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="summary">
    <div>Number of Expenses: <strong><?php echo $count; ?></strong></div>
    <div>Total Amount: <strong>$<?php echo number_format($total, 2); ?></strong></div>
</div>
</div>

</body>

</html>