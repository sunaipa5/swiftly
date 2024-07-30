var logoutButton = document.getElementById("logoutButton");
logoutButton.addEventListener("click", function () {
    var confirmation = confirm(logoutButton.getAttribute("data-confirm"));
    if (confirmation) {
        var redirectURL = logoutButton.getAttribute("data-redirect");
        if (redirectURL) {
            window.location.href = redirectURL;
        }
    }
});