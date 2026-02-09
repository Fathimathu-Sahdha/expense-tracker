<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($phone) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE phone_number = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Phone number not registered.";
        } else {
            // Update password (hashed)
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE phone_number = ?");
            $update_stmt->bind_param("ss", $hashed_password, $phone);

            if ($update_stmt->execute()) {
                $success = "Password updated successfully! You can now <a href='login_form.php'>login</a>.";
            } else {
                $error = "Failed to update password. Please try again.";
            }
            $update_stmt->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Forget Password - Expense Tracker</title>
<link rel="stylesheet" href="style.css" />
<script>
    function togglePassword(id, checkboxId) {
        const input = document.getElementById(id);
        const checkbox = document.getElementById(checkboxId);
        if (checkbox.checked) {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
</script>
</head>
<body>
<header>
    <h1>🔑 Reset Your Password</h1>
</header>

<div class="container">
    <div class="left">
        <?php if ($error): ?>
            <p style="color: #ff4c4c; font-weight: bold; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: #2ecc71; font-weight: bold; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="phone">📱 Phone Number</label>
                <input type="text" id="phone" name="phone" required placeholder="Enter your registered phone number" />
            </div>

            <div class="form-group">
                <label for="new_password">🔒 New Password</label>
                <input type="password" id="new_password" name="new_password" required />
                <br />
                <input type="checkbox" id="showNewPassword" onclick="togglePassword('new_password', 'showNewPassword')" />
                <label for="showNewPassword" style="font-weight: normal; font-size: 0.9em;">Show Password</label>
            </div>

            <div class="form-group">
                <label for="confirm_password">🔒 Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required />
                <br />
                <input type="checkbox" id="showConfirmPassword" onclick="togglePassword('confirm_password', 'showConfirmPassword')" />
                <label for="showConfirmPassword" style="font-weight: normal; font-size: 0.9em;">Show Password</label>
            </div>

            <button type="submit" class="button">Reset Password</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Remembered? <a href="login_form.php">Login here</a>
        </p>
    </div>
</div>
</body>
</html>
