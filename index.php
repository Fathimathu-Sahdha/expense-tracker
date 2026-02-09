<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['clear_expenses'])) {
        $stmt = $conn->prepare("DELETE FROM expenses WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $message = "<div class='success'>✅ All your expenses have been cleared successfully.</div>";
        } else {
            $message = "<div class='error'>❌ Error clearing expenses: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        $date = $_POST["date"] ?? null;
        $spendon = $_POST["spendon"] ?? null;
        $amount = $_POST["amount"] ?? null;
        $paymentmethod = $_POST["paymentmethod"] ?? null;

        if (!empty($date) && !empty($spendon) && !empty($amount)) {
            $stmt = $conn->prepare("INSERT INTO expenses (Date, Spended_on, Amount, Payment_Method, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $date, $spendon, $amount, $paymentmethod, $user_id);

            if ($stmt->execute()) {
                $message = "<div class='success'>✅ ₹" . htmlspecialchars($amount) . " added to " . htmlspecialchars($spendon) . " category</div>";
            } else {
                $message = "<div class='error'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .success { background: #2ecc71; color: white; padding: 10px; margin: 10px 0; border-radius: 8px; text-align: center; }
        .error { background: #e74c3c; color: white; padding: 10px; margin: 10px 0; border-radius: 8px; text-align: center; }
        .link { display: block; margin: 10px 0; color: #2c3e50; text-decoration: none; font-weight: bold; }
        .container { display: flex; justify-content: space-between; padding: 20px; }
        .left, .right-panel { width: 48%; }
        .form-group { margin-bottom: 15px; }
        .form-group input[type="text"], .form-group input[type="number"], .form-group input[type="date"] {
            width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;
        }
        .button { padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .radio-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .radio-item label { margin-left: 5px; }
    </style>
</head>
<body>
<header style="position: relative;">
    <h1>💰 MONTHLY EXPENSE TRACKER</h1>
    <div style="position: absolute; top: 10px; right: 20px;">
        <a href="logout.php" style="color: #555; font-weight: bold; text-decoration: none;background-color:white;">Logout</a>
    </div>
</header>

<div class="container">
    <div class="left">
        <h2>📝 Add New Expense</h2>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="date">📅 Date</label>
                <input type="date" name="date" id="date" required />
            </div>

            <div class="form-group">
                <label>🏷️ Spend On</label>
                <div class="radio-group">
                    <?php
                    $categories = [
                        'Food' => '🍔 Food',
                        'Travel' => '✈️ Travel',
                        'Savings' => '💸 Savings',
                        'Health' => '💊 Health',
                        'Purchase' => '🛒 Purchase',
                        'Academic' => '📚 Academic',
                        'Others' => '📦 Others'
                    ];
                    foreach ($categories as $cat => $label) {
                        echo '<div class="radio-item">
                            <input type="radio" name="spendon" value="' . $cat . '" id="' . strtolower($cat) . '" required />
                            <label for="' . strtolower($cat) . '">' . $label . '</label>
                        </div>';
                    }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="amount">💵 Amount</label>
                <input type="number" name="amount" id="amount" placeholder="₹ 0.00" required />
            </div>

            <div class="form-group">
                <label for="paymentmethod">💳 Payment Method</label>
                <input type="text" name="paymentmethod" id="paymentmethod" placeholder="e.g., Cash, UPI, Card" />
            </div>

            <button class="button" type="submit">✓ Add Expense</button>
        </form>
    </div>

    <div class="right-panel">
        <?php if (!empty($message)) echo $message; ?>

        <h2>📊 View Your Expenses</h2>
        <a href='back.php' class='link'>📈 View Expense Chart</a>
        <a href='table.php' class='link'>📋 View Detailed Table</a>

        <form method="POST" onsubmit="return confirm('Are you sure you want to clear ALL your expenses? This cannot be undone.');">
            <input type="hidden" name="clear_expenses" value="1" />
            <button type="submit" class="button" style="background-color: #e74c3c;">🗑️ Clear All Expenses</button>
        </form>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
