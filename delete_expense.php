<?php
include 'connect.php'; // Include the database connection file
error_reporting(0);
session_start();

if (empty($_SESSION["user_id"])) {
    header('location:index.php');
    exit;
}

// Check if the expense ID is received via POST
if (isset($_POST["id"])) {
    $user_id = $_SESSION["user_id"];
    $expense_id = $_POST["id"];

    // Prepare and execute the query to delete the expense record from the 'expenses' table
    $delete_query = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
    $delete_stmt = mysqli_prepare($db, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $expense_id, $user_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        // If deletion is successful, send a success response
        echo "Expense record deleted successfully!";
    } else {
        // If there's an error, send an error response
        echo "Error deleting expense record. Please try again.";
    }

    // Close prepared statement
    mysqli_stmt_close($delete_stmt);
}

// Close the database connection
mysqli_close($db);
?>
