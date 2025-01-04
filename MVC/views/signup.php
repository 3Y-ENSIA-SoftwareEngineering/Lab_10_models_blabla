<?php

// Start a session only if it hasn’t been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Database connection
    $conn = new mysqli("localhost", "root", "", "expense_tracker");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    require_once('../controllers/AuthController.php');

    // Instantiate the controller and handle the request
    try {
        $controller = new AuthController($conn);
        $controller->handleRequest();
        
    } catch (Exception $e) {
        // Handle any exceptions and log them (for production use)
        error_log("Error: " . $e->getMessage());
        $error = "An unexpected error occurred. Please try again.";
    } finally {
        // Ensure the connection is closed to prevent resource leaks
        if (isset($conn) && $conn->ping()) {
            $conn->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #ffe6f2; 
            color: #4a4a4a; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .login-container { 
            background: #fff0f5; 
            padding: 30px 40px; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            width: 350px; 
            text-align: center; 
        }
        h2 { 
            color: #d63384; 
            margin-bottom: 20px; 
        }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #d63384; 
            border-radius: 5px; 
            outline: none; 
            box-sizing: border-box; 
        }
        .submit-btn { 
            background-color: #ff69b4; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
            margin-top: 10px; 
        }
        .submit-btn:hover { 
            background-color: #d63384; 
        }
        .error { 
            color: #ff0000; 
            margin-bottom: 15px; 
            font-weight: bold; 
        }
        .toggle-mode { 
            margin-top: 15px; 
            font-size: 0.9em; 
            color: #666; 
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 id="form-title">Login/Signup</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="login-form" method="POST" action="signup.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
        <div class="toggle-mode">
            This page will automatically create an account if one doesn't exist.
        </div>
    </div>
</body>
</html>
