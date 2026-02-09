<?php
session_start();

// If already logged in, redirect to dashboard/index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign Up - Expense Tracker</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>Sign Up - Expense Tracker</h1>
    </header>
    <div class="container">
        <div class="lefts">
        <form action="signup.php" method="post" class="signup-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required />
            </div>
            <div class="form-group">
                <label for="number">Phone Number</label>
                <input type="text" id="number" name="number" required pattern="\d{10}" title="Enter a valid 10-digit phone number" />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6" />
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6" />
            </div>

            <div class="form-group" style="margin-top: -10px;">
                <input type="checkbox" id="showPassword" onclick="togglePassword()" />
                <label for="showPassword" style="font-size: 0.9em;">Show Password</label>
            </div>

            <button type="submit" class="button">Sign Up</button>
        </form>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; text-align:center; margin-top: 15px;">
                <?php
                if ($_GET['error'] == 'phone_exists') echo "Phone number is already registered.";
                elseif ($_GET['error'] == 'password_mismatch') echo "Passwords do not match.";
                elseif ($_GET['error'] == 'empty_fields') echo "Please fill in all fields.";
                else echo "An error occurred. Please try again.";
                ?>
            </p>
        <?php endif; ?>

        <p style="text-align:center; margin-top: 20px;">
            Already have an account? <a href="login_form.php">Login here</a>
        </p>
        </div>
    </div>

    <script>
    function togglePassword() {
        const pwd1 = document.getElementById('password');
        const pwd2 = document.getElementById('password_confirm');
        const type = pwd1.type === 'password' ? 'text' : 'password';
        pwd1.type = type;
        pwd2.type = type;
    }
    </script>
</body>
</html>
