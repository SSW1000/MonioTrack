<?php
include 'connect.php';
session_start();

if (empty($_SESSION["user_id"])) {
    header('location:index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountId = $_POST['accountId'];
    $newBalance = $_POST['newBalance'];

    // Update the account balance in the database
    $update_query = "UPDATE bank_accounts SET account_balance = ? WHERE id = ? AND user_id = ?";
    $update_stmt = mysqli_prepare($db, $update_query);
    mysqli_stmt_bind_param($update_stmt, "dii", $newBalance, $accountId, $_SESSION["user_id"]);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "success"; // Sending success response back to JavaScript
    } else {
        echo "error"; // Sending error response back to JavaScript
    }

    mysqli_stmt_close($update_stmt);
    mysqli_close($db);
}
?>
