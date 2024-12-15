<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = new mysqli("localhost", "root", "", "expense_tracker");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Delete
if (isset($_POST['delete'])) {
    // Retrieve and sanitize the expense ID
    $expense_id = intval($_POST['expense_id']);

    // Start a database transaction to ensure consistency
    $conn->begin_transaction();

    try {
        // Check if the expense exists in the shared_expenses table
        $check_sql = "SELECT COUNT(*) AS count FROM shared_expenses WHERE expense_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $expense_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();

        if ($row['count'] > 0) {
            // Delete the expense from the shared_expenses table
            $delete_shared_sql = "DELETE FROM shared_expenses WHERE expense_id = ?";
            $delete_shared_stmt = $conn->prepare($delete_shared_sql);
            $delete_shared_stmt->bind_param("i", $expense_id);
            $delete_shared_stmt->execute();
        }

        // Delete the expense from the expenses table
        $delete_expense_sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
        $delete_expense_stmt = $conn->prepare($delete_expense_sql);
        $delete_expense_stmt->bind_param("ii", $expense_id, $user_id);
        $delete_expense_stmt->execute();

        // Commit the transaction
        $conn->commit();

        echo "Expense successfully deleted.";
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "An error occurred: " . $e->getMessage();
    }
}


// Build filter query
$where_conditions = ["user_id = ?"]; 
$params = [$user_id];
$types = "i";

if (!empty($_GET['category'])) {
    $where_conditions[] = "category = ?";
    $params[] = $_GET['category'];
    $types .= "s";
}

if (!empty($_GET['start_date'])) {
    $where_conditions[] = "date >= ?";
    $params[] = $_GET['start_date'];
    $types .= "s";
}

if (!empty($_GET['end_date'])) {
    $where_conditions[] = "date <= ?";
    $params[] = $_GET['end_date'];
    $types .= "s";
}

$where_clause = implode(" AND ", $where_conditions);
$sql = "SELECT * FROM expenses WHERE $where_clause ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get total and count
$count = $result->num_rows;
$total = 0;
$expenses = [];
while ($row = $result->fetch_assoc()) {
    $total += $row['amount'];
    $expenses[] = $row;
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
        <!-- Filter Section -->
        <div class="filter-container">
        <form method="GET" class="filter-form" action="list_expenses.php">
                <select name="category">
                    <option value="">All Categories</option>
                    <option value="Food" <?php echo isset($_GET['category']) && $_GET['category'] == 'Food' ? 'selected' : ''; ?>>Food</option>
                    <option value="Transport" <?php echo isset($_GET['category']) && $_GET['category'] == 'Transport' ? 'selected' : ''; ?>>Transport</option>
                    <option value="Entertainment" <?php echo isset($_GET['category']) && $_GET['category'] == 'Entertainment' ? 'selected' : ''; ?>>Entertainment</option>
                    <option value="Bills" <?php echo isset($_GET['category']) && $_GET['category'] == 'Bills' ? 'selected' : ''; ?>>Bills</option>
                    <option value="Other" <?php echo isset($_GET['category']) && $_GET['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
                
                <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>" placeholder="Start Date">
                <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>" placeholder="End Date">
                
                <button type="submit" class="btn" style="background-color: #f06292; color: white;">Filter</button>
            </form>
        </div>

        <!-- Expense Table -->
        <table class="expense-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Title</th>
                    <th>Controls</th>
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

        <!-- Summary -->
        <div class="summary">
            <div>Number of Expenses: <strong><?php echo $count; ?></strong></div>
            <div>Total Amount: <strong>$<?php echo number_format($total, 2); ?></strong></div>
        </div>
    </div>
</body>
</html>