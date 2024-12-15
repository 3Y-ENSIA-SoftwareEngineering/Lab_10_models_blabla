<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense_tracker");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Check if it's a login or signup attempt
    $login_sql = "SELECT id, password FROM login WHERE username = ?";
    $login_stmt = $conn->prepare($login_sql);
    $login_stmt->bind_param("s", $username);
    $login_stmt->execute();
    $result = $login_stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists - attempt login
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        // User doesn't exist - create new account
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $signup_sql = "INSERT INTO login (username, password) VALUES (?, ?)";
        $signup_stmt = $conn->prepare($signup_sql);
        $signup_stmt->bind_param("ss", $username, $hashed_password);
        
        if ($signup_stmt->execute()) {
            // Get the newly inserted user's ID
            $user_id = $signup_stmt->insert_id;
            
            // Create default profile entry
            $profile_sql = "INSERT INTO profile (user_id, full_name) VALUES (?, ?)";
            $profile_stmt = $conn->prepare($profile_sql);
            $default_name = $username;
            $profile_stmt->bind_param("is", $user_id, $default_name);
            $profile_stmt->execute();
            
            // Set session
            $_SESSION['user_id'] = $user_id;
            
            // Redirect
            header("Location: home.php");
            exit();
        } else {
            $error = "Error creating account: " . $signup_stmt->error;
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

        input[type="text"],
        input[type="password"] {
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