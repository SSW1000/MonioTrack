<?php
include 'connection.php'; // Include the database connection file
error_reporting(0);
session_start();
if (empty($_SESSION["user_id"])) {
    header('location:../index.php');
    exit;
}

$user_id = $_SESSION["user_id"];

// Get start and end dates from the POST request
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

// Fetch expense records within the specified date range for the current user
$expenses_query = "SELECT expenses.*, bank_accounts.bank_name, bank_accounts.account_number FROM expenses LEFT JOIN bank_accounts ON expenses.bank_account_id = bank_accounts.id WHERE expenses.user_id = ? AND expenses.date BETWEEN ? AND ?";
$expenses_stmt = mysqli_prepare($db, $expenses_query);
mysqli_stmt_bind_param($expenses_stmt, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($expenses_stmt);
$expenses_result = mysqli_stmt_get_result($expenses_stmt);

// Check if there are expense records within the date range
if ($expenses_result && mysqli_num_rows($expenses_result) > 0) {
    while ($row = mysqli_fetch_assoc($expenses_result)) {
        // Display each expense record as HTML rows
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td class='text-right'>"   . $row['amount'] . "</td>";
        echo "<td>" . $row['category'] . "</td>";
        echo "<td>" . $row['spent_using'] . "</td>";
        echo "<td>" . $row['bank_name'] . " - " . $row['account_number'] . "</td>"; 
        echo "<td>" . $row['remarks'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td>
        <button class='btn btn-danger btn-sm delete-btn-expense' data-id='" . $row['id'] . "'>
        <i class='fas fa-trash'></i> 
    </button>
    
              </td>";
        echo "</tr>";
    }
} else {
    // If no expense records found within the date range
    echo 'no_records_found'; // Or any string you prefer
}

// Close prepared statement
mysqli_stmt_close($expenses_stmt);

// Close the database connection
mysqli_close($db);
?>
