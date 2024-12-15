<?php
require_once 'session.php';
$conn = new mysqli("localhost", "root", "", "expense_tracker");

// Get the current user's ID
$current_user_id = $_SESSION['user_id'];

// Query to fetch shared expenses with owner details
$sql = "
    SELECT 
        e.id AS expense_id, 
        e.category, 
        e.title, 
        e.amount, 
        e.date, 
        p.full_name AS owner_name,
        l.username AS owner_username
    FROM shared_expenses se
    JOIN expenses e ON se.expense_id = e.id
    JOIN login l ON se.owner_id = l.id
    LEFT JOIN profile p ON se.owner_id = p.user_id
    WHERE se.shared_with = ?
    ORDER BY e.date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$total_shared_expenses = 0;
$shared_expenses = [];
while ($row = $result->fetch_assoc()) {
    $total_shared_expenses += $row['amount'];
    $shared_expenses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Shared With Me</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fce4ec;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .shared-expenses-table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .shared-expenses-table th,
        .shared-expenses-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .shared-expenses-table th {
            background-color: #f8bbd0;
            color: white;
        }

        .summary {
            background-color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
        }

        .empty-state {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <h1>Expenses Shared With Me</h1>
        
        <?php if (empty($shared_expenses)): ?>
            <div class="empty-state">
                <p>No expenses have been shared with you yet.</p>
            </div>
        <?php else: ?>
            <table class="shared-expenses-table">
                <thead>
                    <tr>
                        <th>Owner Name</th>
                        <th>Owner Username</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shared_expenses as $expense): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($expense['owner_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($expense['owner_username']); ?></td>
                        <td><?php echo htmlspecialchars($expense['category']); ?></td>
                        <td><?php echo htmlspecialchars($expense['title']); ?></td>
                        <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($expense['date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary">
                <div>Total Shared Expenses: <strong><?php echo count($shared_expenses); ?></strong></div>
                <div>Total Amount: <strong>$<?php echo number_format($total_shared_expenses, 2); ?></strong></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>