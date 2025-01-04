<?php
session_start();
require_once('./../controllers/AuthController.php');

    // Database connection
$conn = new mysqli("localhost", "root", "", "expense_tracker");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Assuming you already have the database connection $dbConnection
$authController = new AuthController($conn);

// Check if the user is logged in by verifying session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Use the controller to get the user profile
$profile = $authController->getUserProfile($_SESSION['user_id']);

if ($profile === null) {
    echo "Error: Profile not found!";
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Expense Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe6f0; /* Light pink background */
            color: #333; /* Dark text for readability */
        }
        nav {
            background-color: #ff69b4; /* Hot pink */
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav span {
            font-size: 1.2em;
            font-weight: bold;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 1em;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #ff1493; /* Deeper pink for hover effect */
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff; /* White background for contrast */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #ff69b4;
        }
    </style>
</head>
<body>
    <nav>
        <span>Welcome, <?= htmlspecialchars($profile['full_name']) ?></span>
        <div>
            <a href="home.php?page=add_expense">Add Expense</a>
            <a href="home.php?page=list_expenses">List Expenses</a>
            <a href="home.php?page=shared_with">Shared With</a>
            <a href="home.php?page=shared_with_me">Shared With me </a>
            <a href="home.php?page=profile">Profile</a>
        </div>
    </nav>

    <div class="container">
        <?php
        // Include content based on selected page
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            switch ($page) {
                case 'add_expense':
                    include 'add_expense.php';
                    break;
                case 'list_expenses':
                    include 'list_expenses.php';
                    break;
                case 'shared_with':
                    include 'shared_with.php';
                    break;
                case 'profile':
                    include 'profile.php';
                    break;
                case 'shared_with_me':
                    include 'shared_with_me.php';
                    break;
                default:
                    echo "<h2>Welcome to the Expense Tracker!</h2>";
            }
        } else {
            echo "<h2>Welcome to the Expense Tracker!</h2>";
        }
        ?>
    </div>
</body>
</html>
