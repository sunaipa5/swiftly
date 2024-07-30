if (document.getElementById('addStaffBtn')) {
    var addStaffBtn = document.getElementById('addStaffBtn');
    addStaffBtn.addEventListener('click', function () {
        var body = `
<div>
<form id="addStaff" onsubmit="return posty(event,'staff.php','','response')">
<input class="fit" type="text" placeholder="Personel Adı" name="name" required>
<input class="fit" type="text" placeholder="Personel Soyadı" name="surname" required>
<input class="fit" type="text" placeholder="Personel Kullanıcı adı" name="username" required>
<input class="fit" type="password" placeholder="Personel parolası" name="password" required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button name="addStaff" type="submit" class="btn-md btb flr">Ekle</button>
</form>
</div>
`;
        popup('Yeni Personel Ekleme', body,'reload');
    });
}

if (document.getElementById('deleteStaffBtn')) {
    var deleteStaffBtn = document.getElementById('deleteStaffBtn');
    deleteStaffBtn.addEventListener('click', function () {
        var body = `
<div>
<form id="deleteStaff" onsubmit="return posty(event,'staff.php','','response')">
<input class="fit" type="text" placeholder="Personel Kullanıcı adı" name="username">
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button name="deleteStaff" type="submit" class="btn-md btr flr">Kaldır</button>
</form>
</div>
`;
        popup('Personel kaldırma', body,'reload');
    });
}

if (document.getElementById('changeStaff')) {
    var button = document.getElementById('changeStaff');
    button.addEventListener('click', function () {
        var body = `
<form id="getStaff" onsubmit="return posty(event,'staff.php','staff','response','yes')">
<input class="fit" type="text" placeholder="Personelin kullanıcı adı" name="username" required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button type="submit" class="btn-md btb flr">AL</button>
</form>
`;
        popup('Personel bilgisi değiştirme', body,'reload');
    });
}


function changeStaff(jsonData) {
    document.getElementById('popup').innerHTML = "";
    const json = JSON.parse(jsonData);

    var body = `
    
    <form id="changeStaff" onsubmit="return posty(event,'staff.php','none','response','yes')">

    <div class="center">
    <input class="fit" type="text" placeholder="Kullanıcı adı" name="oldusername" style="display:none;" value="${json.username}">
    <span>Personel Adı:</span>
    <input class="fit" type="text" placeholder="Adı" name="staffname" value="${json.name}">
    <span>Personel Soyadı:</span>
    <input class="fit" type="text" placeholder="Soyadı" name="staffsurname" value="${json.surname}">
    <span>Peronsel Kullanıcı Adı:</span>
    <input class="fit" type="text" placeholder="Kullanıcı adı" name="staffusername" value="${json.username}">
    <span>Yeni Parola:</span>
    <input  class="fit" type="password" placeholder="Yeni Parola" name="staffpassword">
    <div class="">
    <div class="fll response">
    <span id="response" class="fll"></span>
    </div>
    <button type="submit" class="btn-md btb flr">Güncelle</button>
    </div>
    </div>
    </form>
    `;
    popup('Personel bilgisi değiştirme', body,'reload');
}


if (document.getElementById('getBill')) {
    var button = document.getElementById('getBill');
    button.addEventListener('click', function () {
        var body = `
<form id="getBill" onsubmit="return viewBill(event,'main.php','response','none')">
<input class="fit" type="text" placeholder="Satış Numarası" name="salenumber" required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button type="submit" class="btn-md btb flr">Görüntüle</button>
</form>
`;
        popup('Fatura Görüntüleme', body,'reload');
    });
}

function viewBill(event, action, response) {
    var data = new FormData(document.getElementById(event.target.id));
    data.append('requestType', event.target.id);
    fetch(action, { method: "post", body: data })
        .then(res => res.text())
        .then(txt => {

            if (txt.trim() == "Fatura Bulunamadı!") {
                document.getElementById(response).innerText = txt.trim();
            } else {
                document.getElementById('popup').innerHTML = "";

                var body = `
                <div>
                <iframe src='${txt}.pdf'>
                </iframe>
                <br>
                <div>
                <a href="${txt}.pdf" target="_blank"><button class="btb btn-md flr">Dosyaya git</button></a>
                </div>
                </div>
                `;
                popupIframe('Fatura Görüntüleme', body,'reload');
            }
            document.getElementById(event.target.id).reset();



        })
        .catch(err => console.log(err));

    return false;
}