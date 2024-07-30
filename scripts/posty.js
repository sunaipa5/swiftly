
function posty(event, action, type, response, reload) {

    var data = new FormData(document.getElementById(event.target.id));
    data.append('requestType', event.target.id);
    fetch(action, { method: "post", body: data })
        .then(res => res.text())
        .then(txt => {
            event.preventDefault();
            if (type == 'sale') {
                var number = document.getElementById('number').value;
                priceCalculate(txt, number)
                document.getElementById(event.target.id).reset();
            } else if (type == 'product') {

                if (txt.trim() != "Ürün bulunamadı!") {

                    changeProduct(txt);

                } else {
                    document.getElementById(response).innerText = txt.trim();
                    document.getElementById(event.target.id).reset();
                }

            } else if (type == 'staff') {

                if (txt.trim() != "Personel bulunamadı!") {

                    changeStaff(txt);

                } else {
                    document.getElementById(response).innerText = txt.trim();
                    document.getElementById(event.target.id).reset();
                }

            } else if (type == 'login') {
                if (txt.trim() != "Kullanıcı bulunamadı!") {
                    eval(txt);
                } else {
                    document.getElementById(response).innerText = txt.trim();
                    document.getElementById(event.target.id).reset();
                }

            } else {
                if (response != 'none') {
                    document.getElementById(response).innerText = txt.trim();
                }

                if (reload == txt.trim()) {
                    document.getElementById(event.target.id).reset();
                }

            }

        })
        .catch(err => console.log(err));

    return false;
}
