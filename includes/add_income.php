<?php
include 'connection.php'; // Include the database connection file

session_start();

if (empty($_SESSION["user_id"])) {
    header('location:../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];

    // Retrieve form data
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $remarks = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $received_as = filter_input(INPUT_POST, 'receivedAs', FILTER_SANITIZE_STRING);
    
    // Additional variable for bank_account_id
    $bank_account_id = null;

    if ($received_as === 'Bank Deposit') {
        // Retrieve the bank account ID from the form data
        $bank_account_id = filter_input(INPUT_POST, 'bankAccount', FILTER_SANITIZE_NUMBER_INT);

        // Verify if the bank account ID is not null and valid
        if ($bank_account_id !== null && $bank_account_id !== false) {
            // Fetch account number based on bank_account_id
            $fetch_account_query = "SELECT account_number FROM bank_accounts WHERE user_id = ? AND id = ?";
            $fetch_account_stmt = mysqli_prepare($db, $fetch_account_query);
            mysqli_stmt_bind_param($fetch_account_stmt, "ii", $user_id, $bank_account_id);
            mysqli_stmt_execute($fetch_account_stmt);
            $account_result = mysqli_stmt_get_result($fetch_account_stmt);
            $account_row = mysqli_fetch_assoc($account_result);
            $account_number = $account_row['account_number'];

            // Perform the insertion including bank_account_id and account_number
            $insert_query = "INSERT INTO income (amount, category, remarks, date, received_as, user_id, bank_account_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "dssssii", $amount, $category, $remarks, $date, $received_as, $user_id, $bank_account_id);
            mysqli_stmt_execute($insert_stmt);

            if (mysqli_stmt_affected_rows($insert_stmt) > 0) {
                echo "Income record added successfully!";

                // Update the bank_accounts table account_balance
                $update_bank_query = "UPDATE bank_accounts SET account_balance = account_balance + ? WHERE user_id = ? AND id = ?";
                $update_bank_stmt = mysqli_prepare($db, $update_bank_query);
                mysqli_stmt_bind_param($update_bank_stmt, "dii", $amount, $user_id, $bank_account_id);
                mysqli_stmt_execute($update_bank_stmt);
                mysqli_stmt_close($update_bank_stmt);
            } else {
                echo "Error adding income record: " . mysqli_error($db); // Display SQL error message
            }

            // Close statement and database connection
            mysqli_stmt_close($insert_stmt);
            mysqli_stmt_close($fetch_account_stmt);
            mysqli_close($db);
        } else {
            echo "Error: Invalid bank account ID for Bank Deposit.";
            exit;
        }
    } elseif ($received_as === 'Cash') {
        // Perform the insertion without bank_account_id for Cash
        $insert_query = "INSERT INTO income (amount, category, remarks, date, received_as, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($db, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "dssssi", $amount, $category, $remarks, $date, $received_as, $user_id);

        if (mysqli_stmt_execute($insert_stmt)) {
            echo "Income record added successfully!";

            // Update the cash table cash_balance where user_id matches
            $update_cash_query = "UPDATE cash SET cash_balance = cash_balance + ? WHERE user_id = ?";
            $update_cash_stmt = mysqli_prepare($db, $update_cash_query);
            mysqli_stmt_bind_param($update_cash_stmt, "di", $amount, $user_id);
            mysqli_stmt_execute($update_cash_stmt);
            mysqli_stmt_close($update_cash_stmt);
        } else {
            echo "Error adding income record: " . mysqli_error($db); // Display SQL error message
        }

        // Close statement and database connection
        mysqli_stmt_close($insert_stmt);
        mysqli_close($db);
    } else {
        echo "Error: Invalid 'received_as' value.";
        exit;
    }
} else {
    echo "Error: Please fill in all the required fields.";
}
?>
