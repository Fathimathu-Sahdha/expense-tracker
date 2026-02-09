<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['phone'])) {
    header("Location: forget.php");
    exit();
}

$phone = $_GET['phone'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = "Please fill in both password fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Update password in database (hash it!)
        $conn = new mysqli("localhost", "root", "", "expense_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE phone_number = ?");
        $stmt->bind_param("ss", $hashed_password, $phone);

        if ($stmt->execute()) {
            $success = "✅ Password reset successfully. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error updating password: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Reset Password - Expense Tracker</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
    <h1>🔄 Reset Password</h1>
</header>
<div class="container">
    <?php if ($success): ?>
        <div style="color: green; text-align: center; margin: 20px 0;"><?php echo $success; ?></div>
    <?php else: ?>
        <form action="" method="post" class="reset-form">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required autocomplete="new-password" minlength="6" />
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required autocomplete="new-password" minlength="6" />
            </div>
            <button type="submit" class="button">Reset Password</button>
        </form>
        <?php if ($error): ?>
            <div role="alert" style="color: red; margin-top: 15px; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
