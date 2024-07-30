/*Add Product*/
if (document.getElementById('addProductBtn')) {
    var addProductBtn = document.getElementById('addProductBtn');
    addProductBtn.addEventListener('click', function () {
        var body = `
<div>
<form enctype="multipart/form-data" id="addProduct" onsubmit="return posty(event,'product.php','none','response','Ürün eklendi.')">
<input class="fit" type="text" placeholder="Ürün Adı" name="productName" required>
<input class="fit" type="text" placeholder="Ürün sayısı/kg" name="number" oninput='formatNumber(this)' required>
<input class="fit" type="text" pattern="[0-9,.]+" placeholder="Alış Fiyatı" name="arrivalPrice" oninput='formatNumber(this)' required>
<input class="fit" type="text" pattern="[0-9,.]+" placeholder="Satış Fiyatı" name="salePrice" oninput='formatNumber(this)' required>
<input class="fit" type="text" pattern="[0-9,.]+" placeholder="KDV oranı" name="kdv" oninput='formatNumber(this)' required>
<input class="fit" type="text" placeholder="ÖTV oranı" name="otv" oninput='formatNumber(this)' required>
<input class="fit" type="text" placeholder="Barkod numarası" name="barcode" oninput='formatInt(this)' required>
<span>Fotoğraf ekle</span>
<input class="fit" type="file" name="productImage" required>
<div class="">

<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button type="submit" class="btn-md btb flr">Ekle</button>
</div>
</form>
</div>
`;
        popup('Yeni Ürün Ekleme', body,'reload');
    });
}

/*Delete Product*/
if (document.getElementById('deleteProductBtn')) {
    var deleteProductBtn = document.getElementById('deleteProductBtn');
    deleteProductBtn.addEventListener('click', function () {
        var body = `
<div>
<form id="deleteProduct" onsubmit="return posty(event,'product.php','none','response','Ürün Kaldırıldı.')">
<input class="fit" type="text" placeholder="Ürün barkod numarası" name="barcode" oninput='formatInt(this)' required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button name="deleteProduct" type="submit" class="btn-md btr flr">Kaldır</button>
</form>
</div>
`;
        popup('Ürün kaldırma', body,'reload');
    });
}

/*Change Product*/
if (document.getElementById('getProduct')) {
    var button = document.getElementById('getProduct');
    button.addEventListener('click', function () {
        var body = `
<form id="getProduct" onsubmit="return posty(event,'product.php','product','response')">
<input class="fit" type="text" placeholder="Değiştirmek istediğiniz ürünün barkodu" name="barcode" oninput='formatInt(this)' required>
<div class="fll response">
<span id="response" class="fll"></span>
</div>
<button type="submit" class="btn-md btb flr">AL</button>
</form>
`;
        popup('Ürün bilgisi değiştirme', body,'reload');
    });
}

function changeProduct(jsonData){
    if(document.getElementById('popup')){document.getElementById('popup').innerHTML = "";}
    const json = JSON.parse(jsonData);

    var body = `
    
    <form enctype="multipart/form-data" id="changeProduct" onsubmit="return posty(event,'product.php','none','response','none')">

    <div class="center">
    <img width='100px' height='100px' src='../productImages/${json.image}' style="border-radius:7px;">
    </div>

    <div class="center">
    <input style="display:none;" type="text" name="oldBarcode" value="${json.id}">
    <span>Ürün Adı:</span>
    <input class="fit" type="text" placeholder="Ürün Adı" name="productName" value="${json.name}">
    <span>Ürün Sayısı:</span>
    <input class="fit" type="text" placeholder="Ürün sayısı/kg" name="number" value="${formatPrice(json.number,"tr")}">
    <span>Alış Fiyatı:</span>
    <input  class="fit" type="text" pattern="[0-9,.]+" placeholder="Alış Fiyatı" name="arrivalPrice" value="${formatPrice(json.arrival_price,"tr")}">
    <span>Satış Fiyatı:</span>
    <input class="fit" type="text" pattern="[0-9,.]+" placeholder="Satış Fiyatı" name="salePrice" value="${formatPrice(json.sale_price,"tr")}">
    <span>KDV Oranı:</span>
    <input class="fit" type="text" pattern="[0-9,.]+" placeholder="KDV oranı" name="kdv" value="${formatPrice(json.kdv,"tr")}">
    <span>ÖTV Oranı:</span>
    <input class="fit" type="text" placeholder="ÖTV oranı" name="otv" value="${formatPrice(json.otv,"tr")}">
    <span>Barkod numarası:</span>
    <input class="fit" type="text" placeholder="Barkod numarası" name="barcode" value="${json.id}">
    <span>Fotoğraf ekle</span>
    <input type="file" class="fit" name="productImage">
    <div class="">
    
    <div class="fll response">
    <span id="response" class="fll"></span>
    </div>
    <button type="submit" class="btn-md btb flr">Ekle</button>
    </div>
    </div>
    </form>
    `;
            popup('Ürün bilgisi değiştirme', body,'reload');
}

function quickEdit(id){

  
        var data = new FormData();
        data.append('requestType', 'getProduct');
        data.append('barcode', id); 
        fetch('product.php', { method: "post", body: data })
            .then(res => res.text())
            .then(txt => {
     
                    if (txt.trim() != "Ürün bulunamadı!") {
    
                        changeProduct(txt.trim());
    
                    } 
    
            
    
            })
            .catch(err => console.log(err));
    
        return false;
    
    
}