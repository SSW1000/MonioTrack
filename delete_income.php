<?php
include 'connect.php'; // Include the database connection file

session_start();

if (empty($_SESSION["user_id"])) {
    header('location: index.php');
    exit;
}

// Check if the ID is received via POST request
if(isset($_POST['id'])) {
    $income_id = $_POST['id'];

    // Retrieve the received_as value for the income record
    $retrieve_received_as_query = "SELECT received_as, amount, bank_account_id FROM income WHERE id = ?";
    $retrieve_received_as_stmt = mysqli_prepare($db, $retrieve_received_as_query);
    mysqli_stmt_bind_param($retrieve_received_as_stmt, "i", $income_id);
    mysqli_stmt_execute($retrieve_received_as_stmt);
    mysqli_stmt_store_result($retrieve_received_as_stmt);
    mysqli_stmt_bind_result($retrieve_received_as_stmt, $received_as, $amount, $bank_account_id);

    if (mysqli_stmt_fetch($retrieve_received_as_stmt)) {
        if ($received_as === 'Cash') {
            // Delete the record and deduct the amount from cash table cash_balance
            $delete_query = "DELETE FROM income WHERE id = ?";
            $delete_stmt = mysqli_prepare($db, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $income_id);

            if(mysqli_stmt_execute($delete_stmt)) {
                echo "Income record deleted successfully!";
                
                // Update the cash table cash_balance where user id matches
                $update_cash_query = "UPDATE cash SET cash_balance = cash_balance - ? WHERE user_id = ?";
                $update_cash_stmt = mysqli_prepare($db, $update_cash_query);
                mysqli_stmt_bind_param($update_cash_stmt, "di", $amount, $_SESSION['user_id']);
                mysqli_stmt_execute($update_cash_stmt);
                mysqli_stmt_close($update_cash_stmt);
            } else {
                echo "Error deleting income record";
            }

            // Close the prepared statement
            mysqli_stmt_close($delete_stmt);
        } elseif ($received_as === 'Bank Deposit' && !empty($bank_account_id)) {
            // Delete the record and deduct the amount from bank accounts where bank_account_id matches
            $delete_query = "DELETE FROM income WHERE id = ?";
            $delete_stmt = mysqli_prepare($db, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $income_id);

            if(mysqli_stmt_execute($delete_stmt)) {
                echo "Income record deleted successfully!";
                
                // Update the bank_accounts table account_balance where user_id and bank_account_id match
                $update_bank_query = "UPDATE bank_accounts SET account_balance = account_balance - ? WHERE user_id = ? AND id = ?";
                $update_bank_stmt = mysqli_prepare($db, $update_bank_query);
                mysqli_stmt_bind_param($update_bank_stmt, "dii", $amount, $_SESSION['user_id'], $bank_account_id);
                mysqli_stmt_execute($update_bank_stmt);
                mysqli_stmt_close($update_bank_stmt);
            } else {
                echo "Error deleting income record";
            }

            // Close the prepared statement
            mysqli_stmt_close($delete_stmt);
        } else {
            echo "Error: Invalid received_as value or bank account ID is empty.";
        }
    } else {
        echo "Error: Unable to fetch received_as value.";
    }

    // Close the database connection
    mysqli_close($db);
} else {
    echo "Invalid request!";
}
?>
