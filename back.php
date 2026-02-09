<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "expense_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT Spended_on, SUM(Amount) as total_amount FROM expenses WHERE user_id = ? GROUP BY Spended_on";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$fire = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Expense Chart | Expense Tracker</title>
    <link rel="stylesheet" href="style.css" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Category', 'Amount'],
          <?php 
            while($result = mysqli_fetch_assoc($fire)) {
                $category = addslashes($result['Spended_on']);
                $amount = (float) $result['total_amount'];
                echo "['{$category}', {$amount}],";
            }
          ?>
        ]);

        var options = {
          title: 'Expenses by Category',
          titleTextStyle: {
            fontSize: 24,
            bold: true,
            color: '#2a5298'
          },
          pieHole: 0.4,
          is3D: false,
          colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'],
          legend: {
            position: 'right',
            textStyle: {
              fontSize: 14,
              color: '#333'
            }
          },
          pieSliceTextStyle: {
            fontSize: 14,
            bold: true
          },
          chartArea: {
            width: '90%',
            height: '80%'
          },
          animation: {
            startup: true,
            duration: 1000,
            easing: 'out'
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
      
      window.addEventListener('resize', drawChart);
    </script>
</head>
<body>
    <header>
        <h1>📊 EXPENSE ANALYTICS</h1>
    </header>
    
    <div style="max-width: 1400px; margin: 0 auto;">
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 35px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);">
            <a href="index.php" class="link" style="display: inline-block; width: auto; margin-bottom: 20px;">⬅️ Back to Dashboard</a>
            
            <div id="piechart" style="width: 100%; height: 500px; border-radius: 15px;"></div>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="table.php" class="link" style="display: inline-block; width: auto;">📋 View Detailed Table</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
