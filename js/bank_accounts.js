
 $(document).ready(function() {
                
    // Function to handle the form submission
$('#addAccountForm').submit(function(event) {
    event.preventDefault(); // Prevent default form submission

    // Get form data
    var formData = $(this).serialize();

    // AJAX call to submit form data
    $.ajax({
        type: 'POST',
        url: 'add_account.php', // Replace with your PHP file handling the form data
        data: formData,
        success: function(response) { 
            // Display success message or perform further actions
            $('#addAccountModal').modal('hide'); // Hide the modal
            // You can update the bank accounts list or display a success message here
            showAlert('Account added successfully.', 'success'); // Show success message

            // Reload the page after a delay of 500ms
            setTimeout(function() {
                location.reload(); // Reload the page
            }, 500);
        },
        error: function(xhr, status, error) {
            // Display error message or handle errors
            showAlert('Error adding account.', 'error'); // Show error message
            // You can display an error message or perform other error handling here
        }
    });
});

$('#editBalanceForm').submit(function(event) {
    event.preventDefault(); // Prevent default form submission

    // Get form data
    var formData = $(this).serialize();

    // AJAX call to submit form data
    $.ajax({
        type: 'POST',
        url: 'edit_balance.php', // PHP script to handle balance update
        data: formData,
        success: function(response) {
            if (response === 'success') {
                $('#editBalanceModal').modal('hide'); // Hide the modal on success
                showAlert('Account balance updated successfully.', 'success'); // Show success message

                // Reload the page after a delay of 500ms
                setTimeout(function() {
                    location.reload(); // Reload the page
                }, 500);
            } else {
                // Handle error case or display error message
                showAlert('Error updating account balance.', 'danger'); // Show error message
            }
        },
        error: function(xhr, status, error) {
            // Display error message or handle errors
            showAlert('Error: ' + error, 'danger'); // Show error message              
        }
    });
});
 // Function to display alerts
 function showAlert(message, alertType) {
            var alertBox = '<div class="custom-alert custom-alert-' + alertType + '" role="alert">' +
                '<strong>' + message + '</strong>' +
                '</div>';

            $('body').append(alertBox);

            setTimeout(function () {
                $('.custom-alert').fadeOut(500, function () {
                    $(this).remove();
                });
            }, 5000);
        }

      
   
});
$('.btn-add').click(function() {
                $('#addAccountModal').modal('show');
            });
// JavaScript function to open the edit balance modal with the specific account ID
function openEditBalanceModal(accountId) {
    $('#accountId').val(accountId); // Set the account ID in the hidden input field
    $('#newBalance').val(''); // Clear the new balance input field
    $('#editBalanceModal').modal('show'); // Show the edit balance modal
}
function deleteAccount(accountId) {
    // Use SweetAlert2 for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed, proceed with account deletion
            $.ajax({
                type: 'POST',
                url: 'delete_account.php',
                data: {
                    account_id: accountId
                },
                success: function(response) {
                    console.log(response);
                    if (response === 'success') {
                        showAlertD('Account deleted successfully.', 'success');
                    } else {
                        showAlertD('Error deleting account.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    showAlertD('Error: ' + error, 'danger');
                }
            });

            // Reload the page after the AJAX request is complete
            setTimeout(function() {
                location.reload(); // Reload the page
            }, 500);
        }
    });
   
}
 // Function to display alerts
 function showAlertD(message, alertType) {
            var alertBox = '<div class="custom-alert custom-alert-' + alertType + '" role="alert">' +
                '<strong>' + message + '</strong>' +
                '</div>';

            $('body').append(alertBox);

            setTimeout(function () {
                $('.custom-alert').fadeOut(500, function () {
                    $(this).remove();
                });
            }, 5000);
        }



    
