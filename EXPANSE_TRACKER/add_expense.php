<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = new mysqli("localhost", "root", "", "expense_tracker");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    if ($category == 'Other') {
        $category = mysqli_real_escape_string($conn, $_POST['other_category']);
    }
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $amount = floatval($_POST['amount']);
    $date = $_POST['date'];
    
    $sql = "INSERT INTO expenses (user_id, category, title, amount, date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issds", $user_id, $category, $title, $amount, $date);
    
    if ($stmt->execute()) {
        $message = 'Expense added successfully!';
    } else {
        $message = 'Error adding expense: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
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
 <script>
        function toggleOtherCategory() {
            const categorySelect = document.getElementById("category");
            const otherCategoryInput = document.getElementById("otherCategoryInput");
            if (categorySelect.value === "Other") {
                otherCategoryInput.style.display = "block";
            } else {
                otherCategoryInput.style.display = "none";
            }
        }
    </script>
</head>
<body>
    
    
    <div class="container">
        <div class="form-container">
            <h2>Add New Expense</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" required onchange="toggleOtherCategory()">
                        <option value="">Select Category</option>
                        <option value="Food">Food</option>
                        <option value="Transport">Transport</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Bills">Bills</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <!-- Input for custom category when "Other" is selected -->
                <div class="form-group" id="otherCategoryInput" style="display: none;">
                    <label for="other_category">Enter Custom Category</label>
                    <input type="text" id="other_category" name="other_category" placeholder="Enter custom category" />
                </div>
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                
                <!-- <div style="display: flex; justify-content: center; align-items: center;">
    <form action="home.php?page=list_expenses" method="post">
        <a href="home.php?page=list_expenses">
        <button type="submit" 
                style="text-decoration: none; background-color: #f06292; color: white; border: none; 
                       padding: 10px 20px; text-align: center; border-radius: 5px; cursor: pointer; 
                       display: block; width: 80%;">
            Add Expense
        </button>
        </a>
    </form>
    </div> -->

            


    <div style="display: flex; justify-content: center; align-items: center">
    <a href="home.php?page=list_expenses" 
       style="text-decoration: none; background-color: #f06292; color: white; border: none; 
              padding: 10px 20px; text-align: center; border-radius: 5px; cursor: pointer; 
              display: block; width: 80%;">
              <button type="submit" style="background-color:#f06292;"> Add Expense</button>
       
    </a>
</div>









            </form>
        </div>
    </div>
</body>
</html>