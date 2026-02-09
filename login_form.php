<?php
session_start();

// If already logged in, redirect to dashboard
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
    <meta name="description" content="Login to your Monthly Expense Tracker using your phone number and password." />
    <title>Login - Expense Tracker</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>Login to Expense Tracker</h1>
    </header>

    <div class="container">
        <div class="left">
            <form action="login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="number">Phone Number</label>
                    <input type="text" id="number" name="number" pattern="\d{10}" title="Enter a 10-digit phone number" autocomplete="tel" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" />

                    <div style="margin-top: 8px;">
                        <input type="checkbox" id="showPassword" onclick="togglePassword()" />
                        <label for="showPassword" style="font-weight: normal; font-size: 0.9em;">Show Password</label>
                    </div>
                </div>

                <button type="submit" class="button">Login</button>
            </form>

            <?php if (isset($_GET['error'])): ?>
                <div style="color: red; text-align: center; margin-top: 15px;">
                    <?php
                    if ($_GET['error'] === 'empty_fields') {
                        echo "Please fill in all fields.";
                    } elseif ($_GET['error'] === 'invalid_password') {
                        echo "Incorrect password.";
                    } elseif ($_GET['error'] === 'user_not_found') {
                        echo "Phone number not registered.";
                    } else {
                        echo "An unknown error occurred.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
                <div style="color: green; text-align: center; margin-top: 15px;">
                    Signup successful! You can now log in.
                </div>
            <?php endif; ?>

            <p style="text-align: center; margin-top: 20px;">
                <a href="forget.php">Forgot Password?</a><br>
                Don't have an account? <a href="signup_form.php">Sign up</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwdField = document.getElementById('password');
            pwdField.type = pwdField.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
