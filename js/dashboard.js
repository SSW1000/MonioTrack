$(document).ready(function () {
   // Show the modal when the "Add New Income" button is clicked

$('#addIncomeBtn').click(function () {
    $('#addIncomeModal').modal('show');
});
    
// Show the modal when the "Add New Expense" button is clicked
$('#addExpenseBtn').click(function () {
    $('#addExpenseModal').modal('show');
});

$('#receivedAs').change(function () {
    if ($(this).val() === 'Bank Deposit') {
        // Show the bank account field
        $('#bankAccountField').show();
    
    // Fetch and populate bank accounts for the user
    $.ajax({
    url: 'includes/get_bank_accounts.php',
    method: 'POST',
    data: { user_id: userID },
    success: function (response) {
        // Parse the JSON data containing bank accounts
        var bankAccounts = JSON.parse(response);
    
        // Clear the dropdown and add new options
        $('#bankAccount').empty();
        bankAccounts.forEach(function (account) {
            // Construct combined bank name and account number option
            var option = $('<option>', {
                value: account.id, // Set account ID as value
                text: account.account_details // Set concatenated bank name and account number as text
            });
    
            // Append the option to the dropdown
            $('#bankAccount').append(option);
        });
    },
    error: function (xhr, status, error) {
        showAlert('Error fetching bank accounts. Please try again.', 'error');
    }
    });
    
    } else {
        // Hide the bank account field
        $('#bankAccountField').hide();
    }
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
                    var bankAccounts = JSON.parse(response);

                    $('#bankAccountE').empty();
                    bankAccounts.forEach(function (account) {
                        var option = $('<option>', {
                            value: account.id,
                            text: account.account_details
                        });
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
 // Perform actions upon submitting the form to add a new income record
 $('#addIncomeForm').submit(function (event) {
    event.preventDefault();

    // Validate the amount input
    var amountValue = $('#amount').val();
    var isValidAmount = /^\d*\.?\d+$/.test(amountValue); // Positive number validation

    // Validate the date input
    var dateValue = $('#date').val();
    var isValidDate = /^\d{4}-\d{2}-\d{2}$/.test(dateValue); // YYYY-MM-DD format validation

    if (!isValidAmount) {
        showAlert('Please enter a valid positive number for the amount.', 'error');
    } else if (!isValidDate) {
        showAlert('Please enter a valid date in the format YYYY-MM-DD.', 'error');
    } else {
        // Serialize the form data
        var formData = $(this).serialize();

        // Send AJAX request to add_income.php for inserting a new income record
        $.ajax({
            url: 'includes/add_income.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                // Show a success message
                showAlert('Income record added successfully!', 'success');
                $('#addIncomeModal').modal('hide'); // Hide the modal after successful submission

                // Reload the page after 1 second
                setTimeout(function () {
                    location.reload();
                }, 500);
            },
            error: function (xhr, status, error) {
                // Show an error message
                showAlert('Error adding income record. Please try again.', 'error');
            }
        });
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
    });        