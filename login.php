<?php
session_start();

// DB config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['number'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($phone) || empty($password)) {
        header("Location: login_form.php?error=empty_fields");
        exit();
    }

    // Fetch user
    $stmt = $conn->prepare("SELECT id, password FROM user WHERE phone_number = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Debug logs (optional - remove in production)
        // error_log("Phone: $phone");
        // error_log("Hashed password: $hashed_password");
        // error_log("Entered password: $password");

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit();
        } else {
            header("Location: login_form.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: login_form.php?error=user_not_found");
        exit();
    }

    $stmt->close();
} else {
    header("Location: login_form.php");
    exit();
}
?>
