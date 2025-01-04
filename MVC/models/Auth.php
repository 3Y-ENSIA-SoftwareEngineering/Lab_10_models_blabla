<?php
class Auth {
    public $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Login or signup user
    public function loginOrSignup($username, $password) {
        // Check if the user exists
        $login_sql = "SELECT id, password FROM login WHERE username = ?";
        $stmt = $this->conn->prepare($login_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, attempt login
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return ['status' => 'login', 'user_id' => $user['id']];
            } else {
                return ['status' => 'error', 'message' => 'Invalid username or password'];
            }
        } else {
            // User doesn't exist, create a new account
            return $this->signup($username, $password);
        }
    }

    // Create a new account
    private function signup($username, $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $signup_sql = "INSERT INTO login (username, password) VALUES (?, ?)";
        $stmt = $this->conn->prepare($signup_sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            // Create default profile entry
            $profile_sql = "INSERT INTO profile (user_id, full_name) VALUES (?, ?)";
            $profile_stmt = $this->conn->prepare($profile_sql);
            $default_name = $username;
            $profile_stmt->bind_param("is", $user_id, $default_name);
            $profile_stmt->execute();

            return ['status' => 'signup', 'user_id' => $user_id];
        } else {
            return ['status' => 'error', 'message' => 'Error creating account'];
        }
    }

    // Get user profile based on user_id
    public function getUserProfile($user_id) {
        $query = "SELECT full_name, email FROM profile WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null; // Profile not found
        }
    }
}
?>
