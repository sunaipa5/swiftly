posty();
function posty() {
    var data = new FormData();
    data.append('requestType', 'username');
    var stusername = localStorage.getItem('username');
    data.append('username', stusername);
    fetch("main.php", { method: "post", body: data })
        .then(res => res.text())
        .then(txt => {
            const json = JSON.parse(txt);
            document.getElementById("userview").innerHTML = `
        <h3 class='tx-cen'>${json.name} ${json.surname}</h3>

        `;
        })
        .catch(err => console.log(err));

    return false;

}

var logoutButton = document.getElementById("logoutButton");
logoutButton.addEventListener("click", function () {
    var confirmation = confirm(logoutButton.getAttribute("data-confirm"));
    if (confirmation) {
        var redirectURL = logoutButton.getAttribute("data-redirect");
        if (redirectURL) {
            // Kullanıcı onaylarsa, belirtilen URL'ye yönlendirme yapabilirsiniz.
            window.location.href = redirectURL;
        }
    }
});

