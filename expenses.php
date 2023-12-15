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

// Fetch all relevant expenses for the current user
$expenses_query = "SELECT expenses.*, bank_accounts.bank_name, bank_accounts.account_number FROM expenses LEFT JOIN bank_accounts ON expenses.bank_account_id = bank_accounts.id WHERE expenses.user_id = ?";
$expenses_stmt = mysqli_prepare($db, $expenses_query);
mysqli_stmt_bind_param($expenses_stmt, "i", $user_id);
mysqli_stmt_execute($expenses_stmt);
$expenses_result = mysqli_stmt_get_result($expenses_stmt);

// Fetch all expense categories from the 'expensecategories' table
$category_query = "SELECT ExpenseCategoryName FROM expensecategories";
$category_result = mysqli_query($db, $category_query);

// Close prepared statement
mysqli_stmt_close($expenses_stmt);

// Close the database connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Expenses</title>
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
            <li class="nav-item">
                <a class="nav-link" href="income.php">
                    <i class="fas fa-coins"></i> Income
                </a>
            </li>
            <li class="nav-item active">
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
            <h3>Filter Expenses Records</h3>
        <div class="row">
            <div class="col-sm-2">
                <label for="startDateExpense" class="form-label">Start Date:</label>
                <input type="date" class="form-control" id="startDateExpense" placeholder="Start Date" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-sm-2">
                <label for="endDateExpense" class="form-label">End Date:</label>
                <input type="date" class="form-control" id="endDateExpense" placeholder="End Date" value="<?php echo date('Y-m-t'); ?>">
            </div>
            <div class="col-sm-2">
                <label class="invisible">Filter</label>
                <div class="input-group">
                <button class="btn btn-success" type="button" id="filterByDateRangeExpenseBtn">
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
        
        <button type="button" class="btn btn-primary mt-3" id="addExpenseBtn">
    <i class="fas fa-plus"></i> Add New Expense Record
</button>

<br><br>
                <div class="card">
                <div class="card-header">
                    <h2 class="float-left">Expenses Records</h2>
                   
                    <div class="clearfix"></div> 
                </div>
                    <div class="card-body">
    <!-- Expenses Table -->
    <table class="table table-dark table-bordered" id="expensesTable">
                            <thead>
                                <tr>
                                    <!-- Adjust the table headers according to your expenses table columns -->
                                    <th>ID</th>
                                    <th>Amount (<?php echo $preferred_currency; ?>)</th>
                                    <th>Category</th>
                                    <th>Spent Using</th>
                                    <th>Bank Details</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- PHP loop to populate table rows with expense records -->
                                <?php
                                $totalExpenses = 0; // Initialize the variable to hold the total expenses

                                while ($row = mysqli_fetch_assoc($expenses_result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td class='text-right'>"   . $row['amount'] . "</td>";
                                    echo "<td>" . $row['category'] . "</td>";
                                    echo "<td>" . $row['spent_using'] . "</td>";
                                    echo "<td>" . $row['bank_name'] . " - " . $row['account_number'] . "</td>"; 
                                    echo "<td>" . $row['remarks'] . "</td>";
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td>" . $row['created_at'] . "</td>";
                                    echo "<td>
                                    <button class='btn btn-danger btn-sm delete-btn-expense' data-id='" . $row['id'] . "'>
                                    <i class='fas fa-trash'></i> 
                                </button>
                                
                                          </td>";
                                    echo "</tr>";
                                    // Accumulate the expense values to calculate the total expenses
                                    $totalExpenses += $row['amount'];
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <h3>Total Expenses: <span id="totalExpenses"><?php echo $preferred_currency . " " . number_format($totalExpenses, 2); ?></span></h3>        
        <br>
 
   
<!-- Modal for adding new expense record -->
<div class="modal" id="addExpenseModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add New Expense</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Form to add new expense record -->
                <form id="addExpenseForm">
                <div class="input-group mb-3">
    <div class="input-group-prepend">
        <span class="input-group-text"><?php echo $preferred_currency; ?></span>
    </div>
    <input type="text" class="form-control" id="expenseAmount" name="expenseAmount">
</div>

                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select class="form-control" id="category" name="category">
                            <!-- Expense category options will be populated dynamically -->
                            <?php
                            if ($category_result && mysqli_num_rows($category_result) > 0) {
                                while ($category_row = mysqli_fetch_assoc($category_result)) {
                                    echo "<option value='" . $category_row['ExpenseCategoryName'] . "'>" . $category_row['ExpenseCategoryName'] . "</option>";
                                }
                            } else {
                                echo "<option value=''>No categories found</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="spentUsing">Spent Using:</label>
                        <select class="form-control" id="spentUsing" name="spentUsing">
                            <option value="Cash">Cash</option>
                            <option value="Bank Balance">Bank Balance</option>
                        </select>
                    </div>
                    <div id="bankAccountFieldE" class="form-group" style="display: none;">
    <label for="bankAccountE">Bank Account:</label>
    <select class="form-control" id="bankAccountE" name="bankAccountE"></select>
</div>
                    <div class="form-group">
                            <label for="remarks">Remarks:</label>
                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                        </div>
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="expenseDate" name="expenseDate" value="<?php echo date('Y-m-d'); ?>">
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
<script src="js/expenses.js"></script>
</body>

</html>