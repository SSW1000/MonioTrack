<?php
include 'connect.php'; // Include the database connection file

session_start();

if (empty($_SESSION["user_id"])) {
    header('location: index.php');
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch bank accounts associated with the user
$query = "SELECT id, bank_name, account_number FROM bank_accounts WHERE user_id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bank_accounts = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Concatenate bank name and account number
        $account_details = $row['bank_name'] . ' - ' . $row['account_number'];

        // Populate an associative array with ID as value and concatenated details as text
        $bank_accounts[] = [
            'id' => $row['id'],
            'account_details' => $account_details
        ];
    }
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($db);

// Output bank accounts as JSON
echo json_encode($bank_accounts);
?>
