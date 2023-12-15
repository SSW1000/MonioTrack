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