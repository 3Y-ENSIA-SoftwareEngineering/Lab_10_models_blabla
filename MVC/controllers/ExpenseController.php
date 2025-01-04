<?php
require_once('./../models/Expense.php');

class ExpenseController {
    private $expenseModel;

    public function __construct($dbConnection) {
        $this->expenseModel = new ExpenseModel($dbConnection);
    }

    // Handle the Add Expense request
    public function handleAddExpense() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $category = mysqli_real_escape_string($this->expenseModel->conn, $_POST['category']);
            if ($category == 'Other') {
                $category = mysqli_real_escape_string($this->expenseModel->conn, $_POST['other_category']);
            }
            $title = mysqli_real_escape_string($this->expenseModel->conn, $_POST['title']);
            $amount = floatval($_POST['amount']);
            $date = $_POST['date'];

            $success = $this->expenseModel->addExpense($user_id, $category, $title, $amount, $date);

            if ($success) {
                return ['status' => 'success', 'message' => 'Expense added successfully!'];
            } else {
                return ['status' => 'error', 'message' => 'Error adding expense'];
            }
        }
        return null;
    }

    // Handle the deletion of an expense
    public function deleteExpense($user_id, $expense_id) {
        return $this->expenseModel->deleteExpense($user_id, $expense_id);
    }

    // Fetch the expenses with the provided filters
    public function getExpenses($user_id, $category = null, $start_date = null, $end_date = null) {
        $result = $this->expenseModel->getExpenses($user_id, $category, $start_date, $end_date);
        
        // Fetch all rows into an array
        $expenses = [];
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }
    
        return $expenses;
    }


    // Fetch the expense and handle the update process
    public function editExpense($expense_id, $user_id, $postData) {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve and sanitize form inputs
            $category = mysqli_real_escape_string($this->expenseModel->conn, $postData['category']);
            $title = mysqli_real_escape_string($this->expenseModel->conn, $postData['title']);
            $amount = floatval($postData['amount']);
            $date = $postData['date'];

            // Update the expense in the database
            if ($this->expenseModel->updateExpense($expense_id, $user_id, $category, $title, $amount, $date)) {
                $message = 'Expense updated successfully!';
            } else {
                $message = 'Error updating expense.';
            }
        }

        // Fetch the current expense details
        $expense = $this->expenseModel->getExpenseById($expense_id, $user_id);
        return [$message, $expense];
    }

   // Method to get expense details by ID
   public function getExpenseById($expense_id, $user_id) {
    return $this->expenseModel->getExpenseById($expense_id, $user_id); // Call model method
}

    // Handle the sharing of the expense
    public function shareExpense($expense_id, $user_id, $username) {
        // Get expense details
        $expense = $this->expenseModel->getExpenseById($expense_id, $user_id);
        if (!$expense) {
            return ["Expense not found or unauthorized access.", null];
        }

        // Check if the user exists
        $shared_user = $this->expenseModel->getUserByUsername($username);
        if (!$shared_user) {
            return ["User does not exist.", $expense];
        }

        // Share the expense
        $shared_user_id = $shared_user['id'];
        $result = $this->expenseModel->shareExpense($expense_id, $user_id, $shared_user_id);

        if ($result) {
            return ["Expense successfully shared with $username.", $expense];
        } else {
            return ["Failed to share the expense. Please try again.", $expense];
        }
    }


    // Fetch shared expenses by a user
public function getSharedExpenses($user_id) {
    return $this->expenseModel->getSharedExpenses($user_id);
}

    // Fetch shared expenses with a user
    public function getSharedWithExpenses($user_id) {
        return $this->expenseModel->getSharedWithExpenses($user_id);
    }
        

// Fetch categories
public function getCategories($user_id) {
    return $this->expenseModel->getCategories($user_id);
}
    
}
?>

