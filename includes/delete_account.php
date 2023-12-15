<?php
include 'connection.php'; // Include the database connection file
session_start();
if (empty($_SESSION["user_id"])) {
    header('location:../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['account_id'])) {
    $user_id = $_SESSION["user_id"];
    $account_id = $_POST['account_id'];

    // Delete bank account based on the user ID and account ID
    $delete_query = "DELETE FROM bank_accounts WHERE user_id = ? AND id = ?";
    $delete_stmt = mysqli_prepare($db, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $account_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_stmt_close($delete_stmt);
    mysqli_close($db);
}
?>
