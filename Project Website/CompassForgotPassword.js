function togglePassword() {
    const passwordField = document.getElementById("new_password");
    const toggleButton = document.querySelector(".show-password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleButton.textContent = "Hide";
    } else {
        passwordField.type = "password";
        toggleButton.textContent = "Show";
    }
}
