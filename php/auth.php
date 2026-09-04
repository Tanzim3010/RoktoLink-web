<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if it's a Registration or Login request
    $action = $_POST['action']; 
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($action == 'register') {
        // --- SIGN UP LOGIC ---
        
        $name = trim($_POST['name']);
        $nid = trim($_POST['nid']);
        
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, nid, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $nid, $hashed_password);
        
        if ($stmt->execute()) {
            // SUCCESS: Send them back to the login page
            // We intentionally do NOT set $_SESSION variables here so they aren't auto-logged in
            header("Location: ../Html/index.html");
            exit();
        } else {
            echo "Error: Could not register. Email or NID might already exist.";
        }
        
    } elseif ($action == 'login') {
        // --- LOGIN LOGIC ---
        
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Verify the hashed password
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $email; 
                
                // Admin Routing Check
                if ($email === 'admin@roktolink.com') {
                    header("Location: admin.php");
                    exit();
                } else {
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
        }
    }
}
?>