<?php
require_once('./../models/Auth.php');

class AuthController {
    private $authModel;

    public function __construct($dbConnection) {
        $this->authModel = new Auth($dbConnection);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = mysqli_real_escape_string($this->authModel->conn, $_POST['username']);
            $password = $_POST['password'];

            // Process login/signup
            $result = $this->authModel->loginOrSignup($username, $password);

            if ($result['status'] === 'login') {
                $_SESSION['user_id'] = $result['user_id'];
                header("Location: home.php");
                exit();
            } elseif ($result['status'] === 'signup') {
                $_SESSION['user_id'] = $result['user_id'];
                header("Location: home.php");
                exit();
            } else {
                $error = $result['message'];
                include('./../views/signup.php'); // Display the login page with the error message
                exit;
            }
        } else {
            include('./../views/signup.php'); // Just show the login page if no POST data
            exit;
        }
    }

        // Fetch user profile through the Auth model
    public function getUserProfile($user_id) {
            return $this->authModel->getUserProfile($user_id);
        }
    
}
?>
