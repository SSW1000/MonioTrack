<?php
include 'includes/connection.php'; // Include the database connection file
error_reporting(0);
session_start();
if(empty($_SESSION["user_id"]))
{
	header('location:index.php');
}
else
{
    $user_id = $_SESSION["user_id"];
    $user_query = "SELECT username, preferred_currency FROM users WHERE id = ?";
    $user_stmt = mysqli_prepare($db, $user_query);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user_row = mysqli_fetch_assoc($user_result);
    $user_name = $user_row['username'];
    $preferred_currency = $user_row['preferred_currency'];

    $current_month = date('m');
    $current_year = date('Y');

    // Fetch income data for the current month
    $income_query = "SELECT * FROM income WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?";
    $income_stmt = mysqli_prepare($db, $income_query);
    mysqli_stmt_bind_param($income_stmt, "iii", $user_id, $current_month, $current_year);
    mysqli_stmt_execute($income_stmt);
    $income_result = mysqli_stmt_get_result($income_stmt);

    // Calculate total income for the current month
    $total_income_month = 0;
    while ($row = mysqli_fetch_assoc($income_result)) {
        $total_income_month += $row['amount'];
    }

// Fetch expenses data for the current month
$expenses_query = "SELECT * FROM expenses WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?";
$expenses_stmt = mysqli_prepare($db, $expenses_query);
mysqli_stmt_bind_param($expenses_stmt, "iii", $user_id, $current_month, $current_year);
mysqli_stmt_execute($expenses_stmt);
$expenses_result = mysqli_stmt_get_result($expenses_stmt);

// Calculate total expenses for the current month
$total_expenses_month = 0;
while ($row = mysqli_fetch_assoc($expenses_result)) {
    $total_expenses_month += $row['amount'];
}

$category_income_query = "SELECT category, SUM(amount) AS total_amount FROM income WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ? GROUP BY category";
$category_income_stmt = mysqli_prepare($db, $category_income_query);
mysqli_stmt_bind_param($category_income_stmt, "iii", $user_id, $current_month, $current_year);
mysqli_stmt_execute($category_income_stmt);
$category_income_result = mysqli_stmt_get_result($category_income_stmt);

// Prepare data for Chart.js
$category_labels = [];
$category_amounts = [];
while ($category_row = mysqli_fetch_assoc($category_income_result)) {
    $category_labels[] = $category_row['category'];
    $category_amounts[] = $category_row['total_amount'];
}

// Calculate total expenses for the current month
$total_expenses_month = 0;
$category_expenses_labels = [];
$category_expenses_amounts = [];

$category_expenses_query = "SELECT category, SUM(amount) AS total_amount FROM expenses WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ? GROUP BY category";
$category_expenses_stmt = mysqli_prepare($db, $category_expenses_query);
mysqli_stmt_bind_param($category_expenses_stmt, "iii", $user_id, $current_month, $current_year);
mysqli_stmt_execute($category_expenses_stmt);
$category_expenses_result = mysqli_stmt_get_result($category_expenses_stmt);

while ($row = mysqli_fetch_assoc($category_expenses_result)) {
    $category_expenses_labels[] = $row['category'];
    $category_expenses_amounts[] = $row['total_amount'];
    $total_expenses_month += $row['total_amount'];
}
$bank_balance_query = "SELECT SUM(account_balance) AS total_balance FROM bank_accounts WHERE user_id = ?";
$bank_balance_stmt = mysqli_prepare($db, $bank_balance_query);
mysqli_stmt_bind_param($bank_balance_stmt, "i", $user_id);
mysqli_stmt_execute($bank_balance_stmt);
$bank_balance_result = mysqli_stmt_get_result($bank_balance_stmt);
$bank_balance_row = mysqli_fetch_assoc($bank_balance_result);
$total_bank_balance = $bank_balance_row['total_balance'];

$cash_balance_query = "SELECT cash_balance FROM cash WHERE user_id = ?";
$cash_balance_stmt = mysqli_prepare($db, $cash_balance_query);
mysqli_stmt_bind_param($cash_balance_stmt, "i", $user_id);
mysqli_stmt_execute($cash_balance_stmt);
$cash_balance_result = mysqli_stmt_get_result($cash_balance_stmt);
$cash_balance_row = mysqli_fetch_assoc($cash_balance_result);
$cash_in_hand = $cash_balance_row['cash_balance'];
// Calculate total wealth
$total_wealth = ($total_bank_balance + $cash_in_hand);
  // Fetch all income categories from the 'incomecategories' table
  $category_query = "SELECT IncomeCategoryName FROM incomecategories";
  $category_result = mysqli_query($db, $category_query);
  
  // Fetch all expense categories from the 'expensecategories' table
$ecategory_query = "SELECT ExpenseCategoryName FROM expensecategories";
$ecategory_result = mysqli_query($db, $ecategory_query);

// Close prepared statement
mysqli_stmt_close($bank_balance_stmt);
// Close prepared statement
mysqli_stmt_close($category_expenses_stmt);

// Close prepared statement
mysqli_stmt_close($category_income_stmt);

mysqli_stmt_close($income_stmt);
mysqli_stmt_close($expenses_stmt);


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MonioTrack | Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/dashboard.css" rel="stylesheet">
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
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

</div>
        <div class="container-sm">
    
        <!-- Bootstrap Row for Income and Expenses Cards -->
        <div class="row mt-4">
<!-- Total Income Card -->
<div class="col-md-4">
<div class="card mb-3">
            <div class="card-header">
            <h2><i class="fas fa-arrow-down" style="color: green;"></i> Monthly Income <button type="button" class="btn btn-primary btn-sm" id="addIncomeBtn">
    <i class="fas fa-plus"></i> 
</button></h2>
        </div>
        <div class="card-body p-2">
        <h4><?php echo $preferred_currency . " " . number_format($total_income_month, 2); ?></h4>
        </div>
    </div>
    <!-- Total Expenses Card -->

    <div class="card mb-3">
        <div class="card-header">
            <h2><i class="fas fa-arrow-up" style="color: red;"></i> Monthly Expenses  <button type="button" class="btn btn-primary btn-sm" id="addExpenseBtn">
    <i class="fas fa-plus"></i> 
</button></h2>
        </div>
        <div class="card-body p-2">
        <h4><?php echo $preferred_currency . " " . number_format($total_expenses_month, 2); ?></h4>
        </div>
    </div>
    <!-- Display Total Bank Balance Card -->

    <div class="card mb-3">
            <div class="card-header">
                <h2><i class="fas fa-university"></i> Bank Balance</h2>
            </div>
            <div class="card-body p-2">
            <h4><?php echo $preferred_currency . " " . number_format($total_bank_balance, 2); ?></h4>
            </div>
        </div>
    
          <!-- Display Cash In Hand Card -->
    
          <div class="card mb-3">
            <div class="card-header">
                <h2><i class="fas fa-money-bill"></i> Cash In Hand</h2>
            </div>
            <div class="card-body p-2">
            <h4><?php echo $preferred_currency . " " . number_format($cash_in_hand, 2); ?></h4>
            </div>
        </div>
         <!-- Display Total Wealth Card -->
    
         <div class="card mb-3">
            <div class="card-header">
                <h2><i class="fas fa-wallet"></i> Total Wealth</h2>
            </div>
            <div class="card-body p-2">
            <h4><?php echo $preferred_currency . " " . number_format($total_wealth, 2); ?></h4>
            </div>
        </div>
</div>
    <div class="col-md-4">
    <div class="card mb-3">
    <div class="card-header">
        <h2><i class="fas fa-hand-holding-usd"></i> Monthly Income</h2>
    </div>
    <div class="card-body p-2">
        <canvas id="incomeByCategoryChart" width="110" height="110"></canvas>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h2><i class="fas fa-shopping-cart"></i> Monthly Expenses</h2>
    </div>
    <div class="card-body p-2">
        <canvas id="expensesByCategoryChart" width="110" height="110"></canvas>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">
        <h2><i class="fas fa-chart-pie"></i> Income vs Expenses</h2>
    </div>
    <div class="card-body p-2">
        <canvas id="incomeVsExpensesDonutChart" width="110" height="110"></canvas>
    </div>
</div>

        </div>
        <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-header">
            <h2><i class="fas fa-history"></i> Recent Transactions</h2>
            </div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush list-font-size-18">
                    <?php
                    // Fetch most recent income and expenses transactions
                    $recent_transactions_query = "(SELECT id, date, category, amount, 'income' AS type FROM income WHERE user_id = ?)
                    UNION ALL
                    (SELECT id, date, category, amount, 'expense' AS type FROM expenses WHERE user_id = ?)
                    ORDER BY date DESC LIMIT 8";
$recent_transactions_stmt = mysqli_prepare($db, $recent_transactions_query);
mysqli_stmt_bind_param($recent_transactions_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($recent_transactions_stmt);
$recent_transactions_result = mysqli_stmt_get_result($recent_transactions_stmt);

                    while ($row = mysqli_fetch_assoc($recent_transactions_result)) {
                        $amount = $row['amount'];
                        $arrow = ($row['type'] === 'income') ? '<i class="fas fa-arrow-down" style="color: green;"></i>' : '<i class="fas fa-arrow-up" style="color: red;"></i>';
                        echo '<li class="list-group-item">' . $row['date'] . ' - ' . $row['category'] . ' - ' . $preferred_currency . ' ' . $amount . ' ' . $arrow . '</li>';
                    }
                    ?>
                </ul>
                
            </div>
        </div>
       
    </div>    
           
    


</div>


    
    


    
   
   


    
</div>
    
    
      
      

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
                            if ($ecategory_result && mysqli_num_rows($ecategory_result) > 0) {
                                while ($ecategory_row = mysqli_fetch_assoc($ecategory_result)) {
                                    echo "<option value='" . $ecategory_row['ExpenseCategoryName'] . "'>" . $ecategory_row['ExpenseCategoryName'] . "</option>";
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


<br>
    
    
   <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- Include Bootstrap's JavaScript after jQuery -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
   
    var categoryLabels = <?php echo json_encode($category_labels); ?>;
    var categoryAmounts = <?php echo json_encode($category_amounts); ?>;
    var categoryExpensesLabels = <?php echo json_encode($category_expenses_labels); ?>;
    var categoryExpensesAmounts = <?php echo json_encode($category_expenses_amounts); ?>;
    var totalIncomeMonth = <?php echo $total_income_month; ?>;
    var totalExpensesMonth = <?php echo $total_expenses_month; ?>;
    var userID = <?php echo $user_id; ?>;
    var preferredCurrency = '<?php echo $preferred_currency; ?>';

</script>
<script src="js/dashboard.js"></script>
<script src="js/charts.js"></script>
<?php
// Closing the database connection
mysqli_close($db);
?>
</body>
</html>