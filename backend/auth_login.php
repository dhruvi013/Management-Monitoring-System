<?php
// backend/auth_login.php
require_once 'db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../frontend/login.php?msg=Please fill in all fields&type=error");
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        header("Location: ../frontend/index.php");
        exit();
    } else {
        header("Location: ../frontend/login.php?msg=Invalid email or password&type=error");
        exit();
    }
}
?>
