<?php
session_start(); // Start the session

include 'includes/connection.php'; // Include the database connection file
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = ""; // Initialize a variable to store messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize input to prevent SQL injection
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Prepare and execute SQL statement to retrieve user data
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify the entered password with the hashed password in the database
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id']; // Assuming 'id' is the user's unique identifier in the 'users' table

            // Redirect to a welcome or dashboard page after successful login
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($db); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Login</title>
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
    </div>
<div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">MonioTrack Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <?php if (!empty($message)) { ?>
                                <br>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $message; ?>
                                </div>
                            <?php } ?>
                            <p>Not registered yet? Please <a href="registration.php">register</a> here.</p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <!-- Bootstrap JS -->
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
