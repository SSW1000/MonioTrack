<?php
include 'connection.php'; // Include the database connection file

session_start();

if (empty($_SESSION["user_id"])) {
    header('location:../index.php');
    exit;
}

// Check if the ID is received via POST request
if(isset($_POST['id'])) {
    $expense_id = $_POST['id'];

    // Retrieve the spent_using value for the expenses record
    $spent_using_query = "SELECT spent_using, amount, bank_account_id FROM expenses WHERE id = ?";
    $spent_using_stmt = mysqli_prepare($db, $spent_using_query);
    mysqli_stmt_bind_param($spent_using_stmt, "i", $expense_id);
    mysqli_stmt_execute($spent_using_stmt);
    mysqli_stmt_store_result($spent_using_stmt);
    mysqli_stmt_bind_result($spent_using_stmt, $spent_using, $amount, $bank_account_id);

    if (mysqli_stmt_fetch($spent_using_stmt)) {
        if ($spent_using === 'Cash') {
            // Delete the record and deduct the amount from cash table cash_balance
            $delete_query = "DELETE FROM expenses WHERE id = ?";
            $delete_stmt = mysqli_prepare($db, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $expense_id);

            if(mysqli_stmt_execute($delete_stmt)) {
                echo "expenses record deleted successfully!";
                
                // Update the cash table cash_balance where user id matches
                $update_cash_query = "UPDATE cash SET cash_balance = cash_balance + ? WHERE user_id = ?";
                $update_cash_stmt = mysqli_prepare($db, $update_cash_query);
                mysqli_stmt_bind_param($update_cash_stmt, "di", $amount, $_SESSION['user_id']);
                mysqli_stmt_execute($update_cash_stmt);
                mysqli_stmt_close($update_cash_stmt);
            } else {
                echo "Error deleting expenses record";
            }

            // Close the prepared statement
            mysqli_stmt_close($delete_stmt);
        } elseif ($spent_using === 'Bank Balance' && !empty($bank_account_id)) {
            // Delete the record and deduct the amount from bank accounts where bank_account_id matches
            $delete_query = "DELETE FROM expenses WHERE id = ?";
            $delete_stmt = mysqli_prepare($db, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $expense_id);

            if(mysqli_stmt_execute($delete_stmt)) {
                echo "expenses record deleted successfully!";
                
                // Update the bank_accounts table account_balance where user_id and bank_account_id match
                $update_bank_query = "UPDATE bank_accounts SET account_balance = account_balance + ? WHERE user_id = ? AND id = ?";
                $update_bank_stmt = mysqli_prepare($db, $update_bank_query);
                mysqli_stmt_bind_param($update_bank_stmt, "dii", $amount, $_SESSION['user_id'], $bank_account_id);
                mysqli_stmt_execute($update_bank_stmt);
                mysqli_stmt_close($update_bank_stmt);
            } else {
                echo "Error deleting expenses record";
            }

            // Close the prepared statement
            mysqli_stmt_close($delete_stmt);
        } else {
            echo "Error: Invalid spent_using value or bank account ID is empty.";
        }
    } else {
        echo "Error: Unable to fetch spent_using value.";
    }

    // Close the database connection
    mysqli_close($db);
} else {
    echo "Invalid request!";
}
?>
