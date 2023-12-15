
$(document).ready(function () {
   
$('#addIncomeBtn').click(function () {
    $('#addIncomeModal').modal('show');
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


    // Perform actions upon clicking the delete button
    $(document).on('click', '.delete-btn', function () {
        var incomeId = $(this).data('id');
        
        $.ajax({
            url: 'includes/delete_income.php',
            method: 'POST',
            data: { id: incomeId },
            success: function (response) {
                showAlert('Income record deleted successfully!', 'success');
        
                // Show the alert for 1 second
                setTimeout(function () {
                    location.reload(); // Reload the page after 1 second
                }, 500);
            },
            error: function (xhr, status, error) {
                showAlert('Error deleting income record. Please try again.', 'error');
            }
        });
        });
             
   $('#filterByDateRangeBtn').click(function () {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

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
        url: 'includes/get_filtered_income.php',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate },
        success: function (response) {
            incomeTable.clear().rows.add($(response)).draw();
            updateTotalIncome();
        },
        error: function (xhr, status, error) {
            showAlert('Error fetching filtered income records. Please try again.', 'error');
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
    function updateTotalIncome() {
        var totalIncome = 0;
        var currencySymbol = preferredCurrency + ' '; // Set currency symbol
    
        incomeTable.column(1, { search: 'applied' }).data().each(function (value) {
            totalIncome += parseFloat(value.replace(/[^\d.-]/g, ''));
        });
    
        $('#totalIncome').text(currencySymbol + totalIncome.toFixed(2));
    }
    updateTotalIncome(preferredCurrency);
   
});

 // DataTable initialization
var incomeTable = $('#incomeTable').DataTable({
  responsive: true
});



   

    


       
    
