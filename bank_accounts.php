<?php
include 'includes/connection.php'; // Include the database connection file
error_reporting(0);
session_start();
if (empty($_SESSION["user_id"])) {
    header('location:index.php');
    exit;
}

$user_id = $_SESSION["user_id"];
$user_query = "SELECT username, preferred_currency FROM users WHERE id = ?";
$user_stmt = mysqli_prepare($db, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_row = mysqli_fetch_assoc($user_result);
$user_name = $user_row['username'];
$preferred_currency = $user_row['preferred_currency'];
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Bank Accounts</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap">
    <!-- Custom CSS -->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/bank.css" rel="stylesheet">
    <link rel="icon" href="images/mt.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>

<body class="dark-mode">
    <div class="container-fuild">
           <!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="dashboard.php">
    <img src="images/mt.png" alt="MonioTrack" width="30" height="30" class="d-inline-block align-top" style="margin-right: 10px;">
        <?php echo $user_name; ?>'s MonioTrack 
    </a>
    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="income.php">
                    <i class="fas fa-coins"></i> Income
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="expenses.php">
                    <i class="fas fa-shopping-cart"></i> Expenses
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="bank_accounts.php">
                    <i class="fas fa-university"></i> Bank Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
    </div>
    <div class="container">
        <h2>Bank Accounts</h2>
        
        <?php
        include 'connect.php'; // Include the database connection file
        
            $user_id = $_SESSION["user_id"];

            // Retrieve bank account details from the database
            $accounts_query = "SELECT * FROM bank_accounts WHERE user_id = ?";
            $accounts_stmt = mysqli_prepare($db, $accounts_query);
            mysqli_stmt_bind_param($accounts_stmt, "i", $user_id);
            mysqli_stmt_execute($accounts_stmt);
            $accounts_result = mysqli_stmt_get_result($accounts_stmt);

            while ($row = mysqli_fetch_assoc($accounts_result)) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title"><i class="fas fa-university"></i> ' . $row['bank_name'] . '</h5>';
                echo '<p class="card-text">';
                echo '<strong><i class="fas fa-credit-card"></i> Account Number:</strong> ' . $row['account_number'] . '<br>';
                echo '<strong><i class="fas fa-dollar-sign"></i> Account Balance:</strong> ' . $preferred_currency . ' ' . $row['account_balance'];
                echo '</p>';
                echo '<div class="btn-group" role="group" aria-label="Account Actions">';
                echo '<button type="button" class="btn btn-primary" onclick="openEditBalanceModal(' . $row['id'] . ')"> <i class="fas fa-edit mr-1"></i> Update Balance</button>';
                echo '<button type="button" class="btn btn-danger ml-2" onclick="deleteAccount(' . $row['id'] . ')"> <i class="fas fa-trash-alt mr-1"></i> Delete Account</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            
            // Close statement and database connection
            mysqli_stmt_close($accounts_stmt);
            mysqli_close($db);
        
        ?>
   
        <!-- Add new account button -->
        <button class="btn btn-add" data-toggle="modal" data-target="#addAccountModal"><i class="fas fa-plus"></i> Add Account</button>
        <!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Add New Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add Account Form -->
                <form id="addAccountForm">
                    <div class="form-group">
                        <label for="bankName">Bank Name</label>
                        <input type="text" class="form-control" id="bankName" name="bankName" required>
                    </div>
                    <div class="form-group">
                        <label for="accountNumber">Account Number</label>
                        <input type="text" class="form-control" id="accountNumber" name="accountNumber" required>
                    </div>
                    <div class="form-group">
    <label for="initialBalance">Initial Balance</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $preferred_currency; ?></span>
        </div>
        <input type="number" class="form-control" id="initialBalance" name="initialBalance" required>
    </div>
</div>

                    <button type="submit" class="btn btn-primary">Add Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add an edit balance modal structure -->
<div class="modal fade" id="editBalanceModal" tabindex="-1" role="dialog" aria-labelledby="editBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBalanceModalLabel">Edit Account Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Balance Form -->
                <form id="editBalanceForm">
                    <input type="hidden" id="accountId" name="accountId" value="">
                    <div class="form-group">
    <label for="newBalance">New Balance</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $preferred_currency; ?></span>
        </div>
        <input type="number" class="form-control" id="newBalance" name="newBalance" required>
    </div>
</div>

                    <button type="submit" class="btn btn-primary">Update Balance</button>
                </form>
            </div>
        </div>
    </div>
</div>


    </div>
   
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   
<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<!-- DataTables Responsive JavaScript -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/bank_accounts.js"></script>

</body>

</html>