
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
    url: 'get_bank_accounts.php',
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
    
    // Serialize the form data
    var formData = $(this).serialize();
    
    // Send AJAX request to add_income.php for inserting a new income record
    $.ajax({
        url: 'add_income.php',
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
    });  
    
// Show the modal when the "Add New Expense" button is clicked
$('#addExpenseBtn').click(function () {
    $('#addExpenseModal').modal('show');
});

    // Handle the form submission to add a new expense record
    $('#addExpenseForm').submit(function (event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'add_expense.php',
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
    });

    // Perform actions upon clicking the delete button
    $(document).on('click', '.delete-btn', function () {
        var incomeId = $(this).data('id');
        
        $.ajax({
            url: 'delete_income.php',
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
        
    // Handle filter button click event
    $('#filterByDateRangeBtn').click(function () {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
    
        if (startDate === '' || endDate === '') {
            showAlert('Please select both start and end dates.', 'error');
            return;
        }
    
        if (new Date(startDate) > new Date(endDate)) {
            showAlert('Invalid date range. Start date should be before or the same as the end date.', 'error');
            return;
        }
    
        $.ajax({
            url: 'get_filtered_income.php',
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

    var ctxIncome = document.getElementById('incomeByCategoryChart').getContext('2d');
    var myChartIncome = new Chart(ctxIncome, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Income by Categories',
                data: categoryAmounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(0, 128, 0, 0.7)',
                    'rgba(255, 0, 255, 0.7)',
                    'rgba(255, 0, 0, 0.7)',
                    'rgba(0, 0, 255, 0.7)',
                    'rgba(128, 0, 128, 0.7)',
                    'rgba(0, 255, 0, 0.7)',
                    'rgba(128, 128, 0, 0.7)',
                    'rgba(0, 255, 255, 0.7)',
                    'rgba(192, 192, 192, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 128, 0, 1)',
                    'rgba(255, 0, 255, 1)',
                    'rgba(255, 0, 0, 1)',
                    'rgba(0, 0, 255, 1)',
                    'rgba(128, 0, 128, 1)',
                    'rgba(0, 255, 0, 1)',
                    'rgba(128, 128, 0, 1)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(192, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true,
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value;
                    },
                    anchor: 'end',
                    align: 'start',
                    offset: 4,
                }
            }
        }
    });

    // Chart for expenses breakdown
    var ctxExpenses = document.getElementById('expensesByCategoryChart').getContext('2d');
    var myChartExpenses = new Chart(ctxExpenses, {
        type: 'pie',
        data: {
            labels: categoryExpensesLabels,
            datasets: [{
                label: 'Expenses by Categories',
                data: categoryExpensesAmounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(0, 128, 0, 0.7)',
                    'rgba(255, 0, 255, 0.7)',
                    'rgba(255, 0, 0, 0.7)',
                    'rgba(0, 0, 255, 0.7)',
                    'rgba(128, 0, 128, 0.7)',
                    'rgba(0, 255, 0, 0.7)',
                    'rgba(128, 128, 0, 0.7)',
                    'rgba(0, 255, 255, 0.7)',
                    'rgba(192, 192, 192, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 128, 0, 1)',
                    'rgba(255, 0, 255, 1)',
                    'rgba(255, 0, 0, 1)',
                    'rgba(0, 0, 255, 1)',
                    'rgba(128, 0, 128, 1)',
                    'rgba(0, 255, 0, 1)',
                    'rgba(128, 128, 0, 1)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(192, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true,
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value;
                    },
                    anchor: 'end',
                    align: 'start',
                    offset: 4,
                }
            }
        }
    });
    var ctxIncomeVsExpensesDonut = document.getElementById('incomeVsExpensesDonutChart').getContext('2d');
    var myChartIncomeVsExpensesDonut = new Chart(ctxIncomeVsExpensesDonut, {
        type: 'doughnut',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                label: 'Income vs Expenses',
                data: [totalIncomeMonth,totalExpensesMonth],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)', // Income color with transparency
                    'rgba(255, 99, 132, 0.5)', // Expenses color with transparency
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)', // Income border color
                    'rgba(255, 99, 132, 1)', // Expenses border color
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%', // Adjust the cutout percentage to control the size of the donut hole
        }
    });

    myChartIncome.options.plugins.legend.display = false;
    myChartIncome.update();

    myChartExpenses.options.plugins.legend.display = false;
    myChartExpenses.update();

    myChartIncomeVsExpensesDonut.options.plugins.legend.display = false;
    myChartIncomeVsExpensesDonut.update();


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
function validatePassword() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;
    var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()])[A-Za-z\d!@#$%^&*()]{8,}$/;

    if (password !== confirmPassword) {
        showAlert('Passwords do not match!', 'error');
        return false;
    } else if (!passwordPattern.test(password)) {
        showAlert('Password must contain at least 8 characters, including one uppercase letter, one lowercase letter, one digit, and one special character (!@#$%^&*()).', 'error');
        return false;
    }
    
    return true;
}


   

    


       
    
