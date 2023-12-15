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

// Fetch all relevant incomes for the current user along with bank details
$income_query = "SELECT income.*, bank_accounts.bank_name, bank_accounts.account_number FROM income LEFT JOIN bank_accounts ON income.bank_account_id = bank_accounts.id WHERE income.user_id = ?";
$income_stmt = mysqli_prepare($db, $income_query);
mysqli_stmt_bind_param($income_stmt, "i", $user_id);
mysqli_stmt_execute($income_stmt);
$income_result = mysqli_stmt_get_result($income_stmt);

// Fetch all income categories from the 'incomecategories' table
$category_query = "SELECT IncomeCategoryName FROM incomecategories";
$category_result = mysqli_query($db, $category_query);

// Close prepared statement
mysqli_stmt_close($income_stmt);

// Close the database connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Income</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap">

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
            <li class="nav-item active">
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
        <!-- Income Table -->
        <div class="row mt-4">
            
            <div class="col-md-12">
            <h3>Filter Income Records</h3>
        <div class="row">
            <div class="col-sm-2">
                <label for="startDate" class="form-label">Start Date:</label>
                <input type="date" class="form-control" id="startDate" placeholder="Start Date" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-sm-2">
                <label for="endDate" class="form-label">End Date:</label>
                <input type="date" class="form-control" id="endDate" placeholder="End Date" value="<?php echo date('Y-m-t'); ?>">
            </div>
            <div class="col-sm-2">
    <label class="invisible">Filter</label>
    <div class="input-group">
        <button class="btn btn-success" type="button" id="filterByDateRangeBtn">
            <i class="fas fa-filter"></i> Filter
        </button>  
    </div>
</div>
<div class="col-sm-3">
    <label class="invisible">Clear Filter</label>
    <div class="input-group">
        <button type="button" class="btn btn-danger" id="clearFiltersBtn" style="display: none;">
            <i class="fas fa-times"></i> Clear Filters
        </button>
    </div>
        </div>
</div>
        <button type="button" class="btn btn-primary mt-3" id="addIncomeBtn">
    <i class="fas fa-plus"></i> Add New Income Record
</button>

<br>
<br>
                <div class="card">
                <div class="card-header">
                    <h2 class="float-left">Income Records</h2>
                    <div class="clearfix"></div> 
                </div>
                    <div class="card-body">
    <table class="table table-dark table-bordered" id="incomeTable">
        <thead>
            <tr>
            <th>ID</th>
            <th>Amount (<?php echo $preferred_currency; ?>)</th>
            <th>Category</th>
            <th>Received As</th>
            <th>Bank Details</th> 
            <th>Remarks</th>
            <th>Date</th>
            <th>Created At</th>
            <th>Action</th> 
            </tr>
        </thead>
        <tbody>
            <!-- PHP loop to populate table rows -->
            <?php
            $totalIncome = 0; // Initialize the variable to hold the total income

            while ($row = mysqli_fetch_assoc($income_result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td class='text-right'>"   . $row['amount'] . "</td>";
                echo "<td>" . $row['category'] . "</td>";
                echo "<td>" . $row['received_as'] . "</td>";
                echo "<td>" . $row['bank_name'] . " - " . $row['account_number'] . "</td>"; 
                echo "<td>" . $row['remarks'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "<td>
            <button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'>
                <i class='fas fa-trash'></i> 
            </button>
          </td>";

                echo "</tr>";
                 // Accumulate the income values to calculate the total income
    $totalIncome += $row['amount'];
            }
            ?>
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <h3>Total Income: <span id="totalIncome"><?php echo $preferred_currency . " " . number_format($totalIncome, 2); ?></span></h3>
        
        <br>
 
    <!-- Modal for adding new income record -->
    <div class="modal" id="addIncomeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Add New Income</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Form to add new income record -->
                    <form id="addIncomeForm">
                    <div class="input-group mb-3">
    <div class="input-group-prepend">
        <span class="input-group-text"><?php echo $preferred_currency; ?></span>
    </div>
    <input type="text" class="form-control" id="amount" name="amount">
</div>

<div class="form-group">
            <label for="category">Category:</label>
            <select class="form-control" id="category" name="category">
                <?php
                
 
// Check if there are categories fetched
if ($category_result && mysqli_num_rows($category_result) > 0) {
    while ($category_row = mysqli_fetch_assoc($category_result)) {
        echo "<option value='" . $category_row['IncomeCategoryName'] . "'>" . $category_row['IncomeCategoryName'] . "</option>";
    }
} else {
    // If no categories are found, display a default option or a message
    echo "<option value=''>No categories found</option>";
}
                ?>
            </select>
        </div>
        <div class="form-group">
                        <label for="receivedAs">Received As:</label>
                        <select class="form-control" id="receivedAs" name="receivedAs">
                            <option value="Cash">Cash</option>
                            <option value="Bank Deposit">Bank Deposit</option>
                        </select>
                    </div>
                    <div id="bankAccountField" class="form-group" style="display: none;">
    <label for="bankAccount">Bank Account:</label>
    <select class="form-control" id="bankAccount" name="bankAccount"></select>
</div>

                        <div class="form-group">
                            <label for="remarks">Remarks:</label>
                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                        </div>
                        <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                        <button type="submit" class="btn btn-success">Submit</button>
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
<script> 
var userID = <?php echo $user_id; ?>;
var preferredCurrency = '<?php echo $preferred_currency; ?>';
</script>
<script src="js/income.js"></script>



</body>

</html>