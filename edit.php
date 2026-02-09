<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the record to edit
if (isset($_GET['no'])) {
    $no = intval($_GET['no']);
    
    // Fetch data
    $sql = "SELECT * FROM expenses WHERE No = $no";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Expense not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $category = $_POST['spended_on'];
    $amount = floatval($_POST['amount']);
    $payment = $_POST['payment_method'];

    $update_sql = "UPDATE expenses 
                   SET Date = '$date', Spended_on = '$category', Amount = $amount, Payment_Method = '$payment'
                   WHERE No = $no";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: expenses.php?status=updated");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Expense</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2 style="text-align:center;">✏️ Edit Expense Entry</h2>
    <form method="POST" style="max-width:500px; margin:auto; background:#f8f8f8; padding:20px; border-radius:10px;">
        <label>Date:</label><br>
        <input type="date" name="date" value="<?php echo $row['Date']; ?>" required><br><br>

        <label>Category:</label><br>
        <select name="spended_on" required>
            <?php
            $categories = ['Food', 'Travel', 'Savings', 'Health', 'Purchase', 'Academic', 'Others'];
            foreach ($categories as $cat) {
                $selected = ($cat === $row['Spended_on']) ? "selected" : "";
                echo "<option value='$cat' $selected>$cat</option>";
            }
            ?>
        </select><br><br>

        <label>Amount:</label><br>
        <input type="number" name="amount" step="0.01" value="<?php echo $row['Amount']; ?>" required><br><br>

        <label>Payment Method:</label><br>
        <select name="payment_method" required>
            <?php
            $methods = ['Cash', 'Card', 'Online', 'UPI'];
            foreach ($methods as $method) {
                $selected = ($method === $row['Payment_Method']) ? "selected" : "";
                echo "<option value='$method' $selected>$method</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" class="link">💾 Update Expense</button>
        <a href="expenses.php" class="link" style="margin-left: 10px;">Cancel</a>
    </form>
</body>
</html>

<?php $conn->close(); ?>
