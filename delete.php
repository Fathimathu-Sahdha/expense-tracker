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

// Check if 'no' parameter is provided and is numeric
if (isset($_GET['no']) && is_numeric($_GET['no'])) {
    $no = intval($_GET['no']);

    // Prepare DELETE query
    $stmt = $conn->prepare("DELETE FROM expenses WHERE No = ?");
    $stmt->bind_param("i", $no);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();

        // ✅ Correct redirect with absolute path
        header("Location: /new_expense/expenses.php?status=deleted");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
        $stmt->close();
        $conn->close();
    }

} else {
    echo "Invalid request.";
    $conn->close();
}
?>
