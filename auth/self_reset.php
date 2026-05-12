<?php
session_start();
require_once __DIR__ . '/../components/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($email === '' || $password === '' || $confirm === '') {
    $_SESSION['reset_error'] = 'All fields are required.';
    header('Location: ../index.php');
    exit();
}

$stmt = $conn->prepare('SELECT id FROM user WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['reset_error'] = 'No account found with that email.';
    $stmt->close();
    header('Location: ../index.php');
    exit();
}

if ($password !== $confirm) {
    $_SESSION['reset_error'] = 'Passwords do not match.';
    $stmt->close();
    header('Location: ../index.php');
    exit();
}

if (strlen($password) < 8
    || !preg_match('/[a-z]/', $password)
    || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[0-9]/', $password)) {
    $_SESSION['reset_error'] = 'Password must be at least 8 chars and include upper, lower, and a number.';
    $stmt->close();
    header('Location: ../index.php');
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$update_stmt = $conn->prepare('UPDATE user SET password = ? WHERE email = ?');
$update_stmt->bind_param('ss', $hash, $email);
$update_stmt->execute();
$update_stmt->close();

$_SESSION['reset_success'] = 'Password reset successfully. You can now log in.';
header('Location: ../index.php');
exit();
?>