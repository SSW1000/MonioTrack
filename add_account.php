<?php
include 'connect.php';
session_start();

if (empty($_SESSION["user_id"])) {
    header('location:index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $bankName = $_POST['bankName'];
    $accountNumber = $_POST['accountNumber'];
    $initialBalance = $_POST['initialBalance'];

    
    // Insert new bank account into the database
    $insert_query = "INSERT INTO bank_accounts (user_id, bank_name, account_number, account_balance, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_stmt = mysqli_prepare($db, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "isdi", $user_id, $bankName, $accountNumber, $initialBalance);

    if (mysqli_stmt_execute($insert_stmt)) {
        // Account added successfully
        mysqli_stmt_close($insert_stmt);
        mysqli_close($db);
        // Redirect back to bank_accounts.php or any other page
        header("Location: bank_accounts.php");
        exit();
    } else {
        // Error adding account
        echo "Error: " . mysqli_error($db);
    }
}
?>
