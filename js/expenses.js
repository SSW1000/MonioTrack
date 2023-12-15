
$(document).ready(function () {
// Show the modal when the "Add New Expense" button is clicked
$('#addExpenseBtn').click(function () {
    $('#addExpenseModal').modal('show');
});
$('#spentUsing').change(function () {
        if ($(this).val() === 'Bank Balance') {
            // Show the bank account field
            $('#bankAccountFieldE').show();
        
        // Fetch and populate bank accounts for the user
        $.ajax({
        url: 'includes/get_bank_accounts.php',
        method: 'POST',
        data: { user_id: userID },
        success: function (response) {
            // Parse the JSON data containing bank accounts
            var bankAccounts = JSON.parse(response);
        
            // Clear the dropdown and add new options
            $('#bankAccountE').empty();
            bankAccounts.forEach(function (account) {
                // Construct combined bank name and account number option
                var option = $('<option>', {
                    value: account.id, // Set account ID as value
                    text: account.account_details // Set concatenated bank name and account number as text
                });
        
                // Append the option to the dropdown
                $('#bankAccountE').append(option);
            });
        },
        error: function (xhr, status, error) {
            showAlert('Error fetching bank accounts. Please try again.', 'error');
        }
        });
        
        } else {
            // Hide the bank account field
            $('#bankAccountFieldE').hide();
        }
        });    
        $('#addExpenseForm').submit(function (event) {
            event.preventDefault();
        
            // Validate the amount input
            var amountValue = $('#expenseAmount').val();
            var isValidAmount = /^\d*\.?\d+$/.test(amountValue); // Positive number validation
        
            // Validate the date input
            var dateValue = $('#expenseDate').val();
            var isValidDate = /^\d{4}-\d{2}-\d{2}$/.test(dateValue); // YYYY-MM-DD format validation
        
            if (!isValidAmount) {
                showAlert('Please enter a valid positive number for the amount.', 'error');
            } else if (!isValidDate) {
                showAlert('Please enter a valid date in the format YYYY-MM-DD.', 'error');
            } else {
                var formData = $(this).serialize();
        
                $.ajax({
                    url: 'includes/add_expense.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        showAlert('Expense record added successfully!', 'success');
                        $('#addExpenseModal').modal('hide');
        
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    },
                    error: function (xhr, status, error) {
                        showAlert('Error adding expense record. Please try again.', 'error');
                    }
                });
            }
        });
        
    // Perform actions upon clicking the delete Expenses button
        $(document).on('click', '.delete-btn-expense', function () {
            var expenseId = $(this).data('id');

            $.ajax({
                url: 'includes/delete_expense.php',
                method: 'POST',
                data: { id: expenseId },
                success: function (response) {
                    showAlert('Expense record deleted successfully!', 'success');

                    // Show the alert for 1 second
                    setTimeout(function () {
                        location.reload(); // Reload the page after 0.5 second
                    }, 500);
                },
                error: function (xhr, status, error) {
                    showAlert('Error deleting expense record. Please try again.', 'error');
                }
            });
        });
     // Handle filter button click event for expenses (similar to income)
     $('#filterByDateRangeExpenseBtn').click(function () {
        var startDate = $('#startDateExpense').val();
        var endDate = $('#endDateExpense').val();
    
        // Regular expression to match the date format (YYYY-MM-DD)
        var dateFormat = /^\d{4}-\d{2}-\d{2}$/;
    
        if (startDate === '' || endDate === '') {
            showAlert('Please select both start and end dates.', 'error');
            return;
        }
    
        // Check if the dates match the expected format
        if (!startDate.match(dateFormat) || !endDate.match(dateFormat)) {
            showAlert('Invalid date format. Please use YYYY-MM-DD format.', 'error');
            return;
        }
    
        if (new Date(startDate) > new Date(endDate)) {
            showAlert('Invalid date range. Start date should be before or the same as the end date.', 'error');
            return;
        }
    
        $.ajax({
            url: 'includes/get_filtered_expenses.php',
            method: 'POST',
            data: { startDate: startDate, endDate: endDate },
            success: function (response) {
                expensesTable.clear().rows.add($(response)).draw();
                updateTotalExpenses();
            },
            error: function (xhr, status, error) {
                showAlert('Error fetching filtered expense records. Please try again.', 'error');
            }
        });
        $('#clearFiltersBtn').show();
    });
    $('#clearFiltersBtn').click(function () {
        // Reload the page
        location.reload();
    });
    
    
function showAlert(message, alertType) {
// Create the alert HTML with custom classes
var alertBox = '<div class="custom-alert custom-alert-' + alertType + '" role="alert">' +
    '<strong>' + message + '</strong>' +
    '</div>';

// Append the alert to the body
$('body').append(alertBox);

// Automatically close the alert after a certain duration (optional)
setTimeout(function () {
    $('.custom-alert').fadeOut(500, function () {
        $(this).remove();
    });
}, 5000); // Change 5000 to the duration in milliseconds
}
// Function to update total expenses similar to total income
         function updateTotalExpenses() {
        var totalExpenses = 0;
        var currencySymbol = preferredCurrency + ' '; // Set currency symbol

        expensesTable.column(1, { search: 'applied' }).data().each(function (value) {
            totalExpenses += parseFloat(value.replace(/[^\d.-]/g, ''));
        });

        $('#totalExpenses').text(currencySymbol + totalExpenses.toFixed(2));
    }
   
    updateTotalExpenses(preferredCurrency);

});

 // DataTable initialization
var expensesTable = $('#expensesTable').DataTable({
    responsive: true
});


   

    


       
    
