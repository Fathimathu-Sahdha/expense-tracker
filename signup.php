<?php
session_start();

// DB config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['number'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Basic validation
    if (empty($name) || empty($phone) || empty($password) || empty($password_confirm)) {
        header("Location: signup_form.php?error=empty_fields");
        exit();
    }

    if ($password !== $password_confirm) {
        header("Location: signup_form.php?error=password_mismatch");
        exit();
    }

    // Check if phone number already exists
    $stmt = $conn->prepare("SELECT id FROM user WHERE phone_number = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Phone number already registered
        $stmt->close();
        header("Location: signup_form.php?error=phone_exists");
        exit();
    }
    $stmt->close();

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into DB
    $stmt = $conn->prepare("INSERT INTO user (name, phone_number, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $hashed_password);

    if ($stmt->execute()) {
        // Success: redirect to login page
        $stmt->close();
        $conn->close();
        header("Location: login_form.php?signup=success");
        exit();
    } else {
        // DB insert error
        $stmt->close();
        $conn->close();
        header("Location: signup_form.php?error=unknown");
        exit();
    }
} else {
    // Not a POST request, redirect to signup form
    header("Location: signup_form.php");
    exit();
}
?>
