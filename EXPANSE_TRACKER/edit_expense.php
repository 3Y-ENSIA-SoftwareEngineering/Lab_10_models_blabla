<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense_tracker");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if (!isset($_GET['expense_id'])) {
    header("Location: dashboard.php");
    exit();
}

$expense_id = intval($_GET['expense_id']);
$user_id = $_SESSION['user_id'];

// Fetch existing expense details
$sql = "SELECT * FROM expenses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$expense = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $amount = floatval($_POST['amount']);
    $date = $_POST['date'];

    $sql = "UPDATE expenses SET category = ?, title = ?, amount = ?, date = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $category, $title, $amount, $date, $expense_id, $user_id);

    if ($stmt->execute()) {
        $message = 'Expense updated successfully!';
        // Refresh the data to reflect changes
        $expense['category'] = $category;
        $expense['title'] = $title;
        $expense['amount'] = $amount;
        $expense['date'] = $date;
    } else {
        $message = 'Error updating expense: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<nav>
        <span>Welcome, <?= htmlspecialchars($profile['full_name']) ?></span>
        <a href="home.php?page=add_expense">Add Expense</a>
        <a href="home.php?page=list_expenses">List Expenses</a>
        <a href="home.php?page=shared_with">Shared With</a>
        <a href="home.php?page=profile">Profile</a>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Edit Expense</h2>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option value="">Select Category</option>
                        <option value="Food" <?php echo $expense['category'] === 'Food' ? 'selected' : ''; ?>>Food</option>
                        <option value="Transport" <?php echo $expense['category'] === 'Transport' ? 'selected' : ''; ?>>Transport</option>
                        <option value="Entertainment" <?php echo $expense['category'] === 'Entertainment' ? 'selected' : ''; ?>>Entertainment</option>
                        <option value="Bills" <?php echo $expense['category'] === 'Bills' ? 'selected' : ''; ?>>Bills</option>
                        <option value="Other" <?php echo $expense['category'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($expense['title']); ?>">
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required value="<?php echo htmlspecialchars($expense['amount']); ?>">
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required value="<?php echo htmlspecialchars($expense['date']); ?>">
                </div>

                <button type="submit">Update Expense</button>
            </form>
        </div>
    </div>
</body>
</html>
