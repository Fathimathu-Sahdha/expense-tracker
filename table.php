<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// DB setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch expenses for the logged-in user
$stmt = $conn->prepare("SELECT No, Date, Spended_on, Amount, Payment_Method FROM expenses WHERE user_id = ? ORDER BY Date DESC, No DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get total expenses
$total_stmt = $conn->prepare("SELECT SUM(Amount) as total_amount FROM expenses WHERE user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_amount = $total_row['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Expense Table | Expense Tracker</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>📋 EXPENSE DETAILS TABLE</h1>
    </header>

    <div style="max-width: 1400px; margin: 0 auto;">
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 35px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); margin-bottom: 20px;">
            <a href="index.php" class="link" style="display: inline-block; width: auto; margin-bottom: 20px;">⬅️ Back to Dashboard</a>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>📅 Date</th>
                        <th>🏷️ Category</th>
                        <th>💵 Amount</th>
                        <th>💳 Payment</th>
                        <th>⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $category_icons = [
                            'Food' => '🍔',
                            'Travel' => '✈️',
                            'Savings' => '💸',
                            'Health' => '💊',
                            'Purchase' => '🛒',
                            'Academic' => '📚',
                            'Others' => '📦'
                        ];

                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            $icon = $category_icons[$row['Spended_on']] ?? '📦';

                            echo "<tr>";
                            echo "<td><b>" . $counter . "</b></td>";
                            echo "<td>" . date('d M Y', strtotime($row['Date'])) . "</td>";
                            echo "<td>" . $icon . " " . htmlspecialchars($row['Spended_on']) . "</td>";
                            echo "<td><b>₹ " . number_format($row['Amount'], 2) . "</b></td>";
                            echo "<td>" . htmlspecialchars($row['Payment_Method']) . "</td>";
                            echo "<td>
                                    <a href='edit.php?no=" . $row['No'] . "' class='link' style='margin-right: 10px;'>Edit</a>
                                    <a href='delete.php?no=" . $row['No'] . "' class='link' onclick=\"return confirm('Are you sure you want to delete this expense?');\">Delete</a>
                                  </td>";
                            echo "</tr>";

                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 40px; text-align: center; color: #999;'>No expenses recorded yet. Start tracking your expenses!</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">💰 <b>TOTAL EXPENSES</b></td>
                        <td><b>₹ <?php echo number_format($total_amount, 2); ?></b></td>
                        <td>-</td>
                    </tr>
                </tfoot>
            </table>

            <a href="back.php" class="link" style="margin-top: 20px;">📈 View Chart Visualization</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$total_stmt->close();
$conn->close();
?>
