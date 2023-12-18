<?php
include 'includes/connection.php'; // Include the database connection file
error_reporting(0);
session_start();
if (empty($_SESSION["user_id"])) {
    header('location:index.php');
} else {
    $user_id = $_SESSION["user_id"];
    $user_query = "SELECT username FROM users WHERE id = ?";
    $user_stmt = mysqli_prepare($db, $user_query);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user_row = mysqli_fetch_assoc($user_result);
    $user_name = $user_row['username'];
    mysqli_stmt_close($user_stmt); // Close the statement for fetching username

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userID = $_SESSION['user_id'];
        $currentPassword = mysqli_real_escape_string($db, $_POST['current_password']);
        $newPassword = mysqli_real_escape_string($db, $_POST['new_password']);
        $confirmNewPassword = mysqli_real_escape_string($db, $_POST['confirm_new_password']);
        $mobile = mysqli_real_escape_string($db, $_POST['mobile']);
        $currency = mysqli_real_escape_string($db, $_POST['currency']);

        $selectUserDetailsSQL = "SELECT password, mobile_number, preferred_currency FROM users WHERE id = ?";
        $stmt = mysqli_prepare($db, $selectUserDetailsSQL);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($currentPassword, $row['password'])) {
                if ($newPassword === $confirmNewPassword) {
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    $updateUserDetailsSQL = "UPDATE users SET password = ?, mobile_number = ?, preferred_currency = ? WHERE id = ?";
                    $updateStmt = mysqli_prepare($db, $updateUserDetailsSQL);
                    mysqli_stmt_bind_param($updateStmt, "sssi", $hashedNewPassword, $mobile, $currency, $userID);

                    if (mysqli_stmt_execute($updateStmt)) {
                        $message = "User details updated successfully.";
                    } else {
                        $message = "Error updating user details: " . mysqli_error($db);
                    }

                    mysqli_stmt_close($updateStmt);
                } else {
                    $message = "New passwords do not match. Please re-enter the new password.";
                }
            } else {
                $message = "Current password is incorrect.";
            }
        } else {
            $message = "Error fetching user details.";
        }

        mysqli_stmt_close($stmt);
    }

    $userID = $_SESSION['user_id'];
    $getUserDetailsSQL = "SELECT mobile_number, preferred_currency FROM users WHERE id = ?";
    $stmt = mysqli_prepare($db, $getUserDetailsSQL);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $currentMobile = $row['mobile_number'];
        $currentCurrency = $row['preferred_currency'];
    } else {
        $message = "Error fetching user details.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MonioTrack | User Details </title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/styles.css" rel="stylesheet">
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
            <li class="nav-item">
                <a class="nav-link" href="bank_accounts.php">
                    <i class="fas fa-university"></i> Bank Accounts
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="update_details.php">
                    <i class="fas fa-user"></i> User Details
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
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">MonioTrack User Details</h3>
                    </div>
                    <div class="card-body">
                       
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validatePassword()">
                                <!-- Mobile Number field -->
                                <div class="form-group">
                                    <label for="mobile">Mobile Number:</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Your 10 Digit Mobile Number" pattern="[0-9]{10}" title="Please enter a 10-digit mobile number." value="<?php echo isset($currentMobile) ? htmlspecialchars($currentMobile) : ''; ?>" required>
                                </div>

                                <!-- Preferred Currency field -->
                                <div class="form-group">
                                    <label for="currency">Preferred Currency:</label>
                                    <input type="text" class="form-control" id="currency" name="currency" placeholder="Enter Your Preferred Currency Code" pattern="[A-Za-z]{3}" title="Please enter a valid 3-letter currency code (e.g.,USD, EUR, LKR)." value="<?php echo isset($currentCurrency) ? htmlspecialchars($currentCurrency) : ''; ?>" required>
                                </div>

                                <!-- Password fields -->
                                <div class="form-group">
                                    <label for="current_password">Current Password:</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your current password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New Password:</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter your new password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm New Password:</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm your new password" required>
                                </div>

                                <!-- Update button -->
                                <button type="submit" class="btn btn-primary">Update Details</button>

                                <!-- Display success or error message -->
                                <?php if (!empty($message)) { ?>
                                    <br>
                                    <div class="alert <?php echo (strpos($message, 'successful') !== false ? 'alert-success' : 'alert-danger'); ?>" role="alert">
                                        <?php echo $message; ?>
                                    </div>
                                <?php } ?>
                            </form>

                        <!-- End of registration form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <!-- Bootstrap JS -->
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script src="js/update_user.js"></script>
</body>

</html>
