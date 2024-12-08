<?php
// Assuming you have the correct database connection set up in the Model class

// Include the Expense model
require_once __DIR__ . '/../../models/Expense.php'; 

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'] ?? '';
    $amount = $_POST['amount'] ?? '';

    // Validation (simple validation, you can improve this)
    if ($title && $amount > 0) {
        // Create an instance of the Expense model
        $expense = new Expense();
        
        // Add the expense to the database
        $expense->add($title, $amount);
        
        // Redirect to the index page after adding
        header("Location: http://localhost/Lab_10_models_blabla/views/expenses/index.php");
        exit();
    } else {
        $error_message = "Please fill in both the title and amount fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Add Expense</h1>
    
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>
        
        <button type="submit">Add Expense</button>
    </form>
    
    <a href="http://localhost/Lab_10_models_blabla/views/expenses/index.php">Back to Expenses List</a>
</body>
</html>
