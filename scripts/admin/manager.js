if (document.getElementById('addStaffBtn')) {
    var addStaffBtn = document.getElementById('addStaffBtn');
    addStaffBtn.addEventListener('click', function () {
        var body = `
<div>
<form id="addStaff" onsubmit="return posty(event,'manager.php','','response')">
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
        popup('Yeni Yönetici Ekleme', body,'reload');
    });
}

if (document.getElementById('deleteStaffBtn')) {
    var deleteStaffBtn = document.getElementById('deleteStaffBtn');
    deleteStaffBtn.addEventListener('click', function () {
        var body = `
<div>
<form id="deleteStaff" onsubmit="return posty(event,'manager.php','','response')">
<input class="fit" type="text" placeholder="Personel Kullanıcı adı" name="username">
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button name="deleteStaff" type="submit" class="btn-md btr flr">Kaldır</button>
</form>
</div>
`;
        popup('Yönetici kaldırma', body,'reload');
    });
}

if (document.getElementById('changeStaff')) {
    var button = document.getElementById('changeStaff');
    button.addEventListener('click', function () {
        var body = `
<form id="getStaff" onsubmit="return posty(event,'manager.php','staff','response')">
<input class="fit" type="text" placeholder="Personelin kullanıcı adı" name="username" required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button type="submit" class="btn-md btb flr">AL</button>
</form>
`;
        popup('Yönetici bilgisi değiştirme', body,'reload');
    });
}


function changeStaff(jsonData){
    document.getElementById('popup').innerHTML = "";
    const json = JSON.parse(jsonData);

    var body = `
    
    <form id="changeStaff" onsubmit="return posty(event,'manager.php','none','response')">

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
            popup('Yönetici bilgisi değiştirme', body,'reload');
}