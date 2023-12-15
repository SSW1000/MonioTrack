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

// Fetch income records within the specified date range for the current user with bank details
$income_query = "SELECT income.*, bank_accounts.bank_name, bank_accounts.account_number FROM income LEFT JOIN bank_accounts ON income.bank_account_id = bank_accounts.id WHERE income.user_id = ? AND income.date BETWEEN ? AND ?";
$income_stmt = mysqli_prepare($db, $income_query);
mysqli_stmt_bind_param($income_stmt, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($income_stmt);
$income_result = mysqli_stmt_get_result($income_stmt);

// Check if there are income records within the date range
if ($income_result && mysqli_num_rows($income_result) > 0) {
    while ($row = mysqli_fetch_assoc($income_result)) {
        // Display each income record as HTML rows
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td class='text-right'>" . $row['amount'] . "</td>";
        echo "<td>" . $row['category'] . "</td>";
        echo "<td>" . $row['received_as'] . "</td>";
        echo "<td>" . $row['bank_name'] . " - " . $row['account_number'] . "</td>"; 
        echo "<td>" . $row['remarks'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td><button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'>
        <i class='fas fa-trash'></i> 
    </button></td>";
        echo "</tr>";
    }
} else {
    // If no income records found within the date range
    echo 'no_records_found'; // Or any string you prefer
}

// Close prepared statement
mysqli_stmt_close($income_stmt);

// Close the database connection
mysqli_close($db);
?>
