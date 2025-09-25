<?php
session_start();

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("location: ../public/dashboard.php");
            exit;
        } else {
            header("location: ../public/login.php?error=Invalid email or password.");
            exit;
        }
    } else {
        header("location: ../public/login.php?error=Invalid email or password.");
        exit;
    }
}
?>