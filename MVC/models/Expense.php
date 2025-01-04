<?php
class ExpenseModel {
    public $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Add new expense
    public function addExpense($user_id, $category, $title, $amount, $date) {
        $query = "INSERT INTO expenses (user_id, category, title, amount, date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issds", $user_id, $category, $title, $amount, $date);

        return $stmt->execute();
    }

    // Fetch expenses based on filters
    public function getExpenses($user_id, $category = null, $start_date = null, $end_date = null) {
        $where_conditions = ["user_id = ?"];
        $params = [$user_id];
        $types = "i";

        if ($category) {
            $where_conditions[] = "category = ?";
            $params[] = $category;
            $types .= "s";
        }

        if ($start_date) {
            $where_conditions[] = "date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        if ($end_date) {
            $where_conditions[] = "date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }

        $where_clause = implode(" AND ", $where_conditions);
        $sql = "SELECT * FROM expenses WHERE $where_clause ORDER BY date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Delete expense by ID
    public function deleteExpense($user_id, $expense_id) {
        $this->conn->begin_transaction();

        try {
            // Check if the expense exists in the shared_expenses table
            $check_sql = "SELECT COUNT(*) AS count FROM shared_expenses WHERE expense_id = ?";
            $check_stmt = $this->conn->prepare($check_sql);
            $check_stmt->bind_param("i", $expense_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $row = $check_result->fetch_assoc();

            if ($row['count'] > 0) {
                // Delete the expense from the shared_expenses table
                $delete_shared_sql = "DELETE FROM shared_expenses WHERE expense_id = ?";
                $delete_shared_stmt = $this->conn->prepare($delete_shared_sql);
                $delete_shared_stmt->bind_param("i", $expense_id);
                $delete_shared_stmt->execute();
            }

            // Delete the expense from the expenses table
            $delete_expense_sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
            $delete_expense_stmt = $this->conn->prepare($delete_expense_sql);
            $delete_expense_stmt->bind_param("ii", $expense_id, $user_id);
            $delete_expense_stmt->execute();

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction if any error occurs
            $this->conn->rollback();
            return false;
        }
    }

    // Get the expense details
    public function getExpenseById($expense_id, $user_id) {
        $sql = "SELECT * FROM expenses WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $expense_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update the expense details
    public function updateExpense($expense_id, $user_id, $category, $title, $amount, $date) {
        $sql = "UPDATE expenses SET category = ?, title = ?, amount = ?, date = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdssi", $category, $title, $amount, $date, $expense_id, $user_id);
        return $stmt->execute();
    }


    // Check if the username exists
    public function getUserByUsername($username) {
        $sql = "SELECT id FROM login WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Share the expense with another user
    public function shareExpense($expense_id, $user_id, $shared_user_id) {
        $sql = "INSERT INTO shared_expenses (expense_id, owner_id, shared_with) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $expense_id, $user_id, $shared_user_id);
        return $stmt->execute();
    }


    // Fetch shared expenses by a specific user
public function getSharedExpenses($user_id) {
    $sql = "
        SELECT 
            e.id AS expense_id,
            e.title,
            e.category,
            e.date,
            e.amount,
            p.full_name AS shared_with_name,
            p.email AS shared_with_email
        FROM 
            shared_expenses se
        JOIN 
            expenses e ON se.expense_id = e.id
        JOIN 
            profile p ON se.shared_with = p.user_id
        WHERE 
            se.owner_id = ?
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $sharedExpenses = [];
    while ($row = $result->fetch_assoc()) {
        $sharedExpenses[] = $row;
    }

    return $sharedExpenses;
}

    // Fetch shared expenses with a specific user
    public function getSharedWithExpenses($user_id) {
        $sql = "
            SELECT 
                e.id AS expense_id,
                e.title,
                e.category,
                e.date,
                e.amount,
                p.full_name AS owner_name,
                p.email AS owner_email
            FROM 
                shared_expenses se
            JOIN 
                expenses e ON se.expense_id = e.id
            JOIN 
                profile p ON se.owner_id = p.user_id
            WHERE 
                se.shared_with = ?
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $sharedExpenses = [];
        while ($row = $result->fetch_assoc()) {
            $sharedExpenses[] = $row;
        }
    
        return $sharedExpenses;
    }
    

    // Fetch distinct categories
    public function getCategories($user_id) {
        $sql = "SELECT DISTINCT category FROM expenses WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }

        return $categories;
    }
    
}
?>
