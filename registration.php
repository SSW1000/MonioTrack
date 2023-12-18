<?php
include 'includes/connection.php'; // Include the database connection file

$message = ""; // Initialize a variable to store messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mobile = $_POST['mobile'];
    $currency = $_POST['currency'];

    // Check if the username already exists
    $check_username_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($db, $check_username_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $message = "Username already exists. Please choose a different username.";
    } else {
        // Validate mobile number (10 digits)
        if (strlen($mobile) !== 10 || !ctype_digit($mobile)) {
            $message = "Invalid mobile number. Please enter a 10-digit mobile number.";
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute SQL statement to insert data
            $insert_sql = "INSERT INTO users (username, password, mobile_number, preferred_currency) VALUES (?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_sql);

            // Bind parameters and execute query
            mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $hashed_password, $mobile, $currency);

            if (mysqli_stmt_execute($insert_stmt)) {
                $message = "Registration successful.";
            } else {
                $message = "Error: " . mysqli_error($db);
            }

            mysqli_stmt_close($insert_stmt);
        }
    }

    mysqli_stmt_close($check_stmt);
}

mysqli_close($db); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Registration </title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap">
    <!-- Custom CSS -->
    <link href="css/styles.css" rel="stylesheet">
    <link rel="icon" href="images/mt.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="dark-mode">
<div class="container-fuild">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="#">
        <img src="images/mt.png" alt="MonioTrack" width="30" height="30" class="d-inline-block align-top" style="margin-right: 10px;">
        MonioTrack 
    </a>
    
</nav>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">MonioTrack Registration</h3>
                    </div>
                    <div class="card-body">
                       
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validatePassword()">
    <!-- Username field -->
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your desired Username" pattern="[a-zA-Z0-9_]{3,}" title="Username must contain at least 3 characters, including letters, digits, and underscores." required>
    </div>

    <!-- Password field -->
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your desired password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()])[A-Za-z\d!@#$%^&*()]{8,}$" title="Password must contain at least 8 characters, including one uppercase letter, one lowercase letter, one digit, and one special character (!@#$%^&*())." required>
    </div>

    <!-- Confirm Password field -->
    <div class="form-group">
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()])[A-Za-z\d!@#$%^&*()]{8,}$" title="Password must contain at least 8 characters, including one uppercase letter, one lowercase letter, one digit, and one special character (!@#$%^&*())."  required>
    </div>

    <!-- Mobile Number field -->
    <div class="form-group">
        <label for="mobile">Mobile Number:</label>
        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Your 10 Digit Mobile Number" pattern="[0-9]{10}" title="Please enter a 10-digit mobile number." required>
    </div>

    <!-- Preferred Currency field -->
    <div class="form-group">
        <label for="currency">Preferred Currency:</label>
        <input type="text" class="form-control" id="currency" name="currency" placeholder="Enter Your Preferred Currency Code" pattern="[A-Za-z]{3}" title="Please enter a valid 3-letter currency code (e.g.,USD, EUR , LKR)." required>
    </div>

    <!-- Register button -->
    <button type="submit" class="btn btn-primary">Register</button>

    <!-- Display success or error message -->
    <?php if (!empty($message)) { ?>
        <br>
        <div class="alert <?php echo(strpos($message, 'successful') !== false ? 'alert-success' : 'alert-danger'); ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php } ?>
    <p>Already registered? Please <a href="index.php">login</a> then.</p>
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
   <script src="js/registration.js"></script>
</body>

</html>
