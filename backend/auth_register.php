<?php
// backend/auth_register.php
require_once 'db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        header("Location: ../frontend/signup.php?msg=Please fill in all fields&type=error");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../frontend/signup.php?msg=Invalid email format&type=error");
        exit();
    }

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        header("Location: ../frontend/signup.php?msg=Email already registered&type=error");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $stmt->execute([':email' => $email, ':password' => $hashed_password]);

        header("Location: ../frontend/login.php?msg=Registration successful! Please login.&type=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../frontend/signup.php?msg=Registration failed: " . $e->getMessage() . "&type=error");
        exit();
    }
}
?>
