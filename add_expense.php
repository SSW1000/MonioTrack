<?php
include 'connect.php'; // Include the database connection file

session_start();

if (empty($_SESSION["user_id"])) {
    header('location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];

    // Retrieve form data
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $remarks = $_POST['remarks'];
    $date = $_POST['date'];

    // Prepare and execute the SQL statement to insert a new expense record
    $insert_query = "INSERT INTO expenses (user_id, amount, category, remarks, date) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($db, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "idsss", $user_id, $amount, $category, $remarks, $date);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "Expense record added successfully!";
    } else {
        echo "Error adding expense record: " . mysqli_error($db);
    }

    // Close statement and database connection
    mysqli_stmt_close($insert_stmt);
    mysqli_close($db);
}
?>
