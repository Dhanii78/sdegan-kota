// File: add_user.js

function togglePasswordVisibility() {
    var passwordField = document.getElementById("userPassword");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
