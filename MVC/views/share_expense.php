<?php
// Start session if it's not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the necessary files
require_once('./../controllers/ExpenseController.php');
require_once('./../models/Expense.php');


// Database connection
$conn = new mysqli("localhost", "root", "", "expense_tracker");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create controller instance
$expenseController = new ExpenseController($conn);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$expense_id = intval($_GET['id'] ?? 0);

// Fetch expense details using controller method
$expense = $expenseController->getExpenseById($expense_id, $user_id);
if (!$expense) {
    die("Expense not found or unauthorized access.");
}

$message = "";

// Handle form submission for sharing the expense
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    // Share the expense via the controller method
    list($message, $expense) = $expenseController->shareExpense($expense_id, $user_id, $username);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Expense</title>
    <style>
        /* Style the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe6f0; /* Light pink background */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff; /* White container background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #ff69b4; /* Hot pink for headings */
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ff69b4; /* Pink border */
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #ff1493; /* Bright pink button */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #ff69b4; /* Slightly lighter pink on hover */
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda; /* Light green for success */
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da; /* Light red for error */
            color: #721c24;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Share Expense</h1>
        <p><strong>Expense:</strong> <?php echo htmlspecialchars($expense['title']); ?> ($<?php echo number_format($expense['amount'], 2); ?>)</p>

        <form method="POST">
            <div class="form-group">
                <label for="username">Enter Email to Share With:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <button type="submit" style="background-color: #f06292; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 80%; text-align: center;">
                    Share
                </button>

                <a href="home.php?page=shared_with" 
                   style="text-decoration: none; background-color: #f06292; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; cursor: pointer; width: 80%; display: block;">
                    Return to Shared List
                </a>
            </div>
        </form>

        <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
