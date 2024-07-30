var finalPrice = 0;
var purePrice = 0;



/*Product Processes*/
if (document.getElementById('addProduct')) {
    var btn = document.getElementById('addProduct');
    btn.addEventListener('click', function () {
        var body = `
<div>
<form id="getProduct" onsubmit="return posty(event,'sale.php','sale')">
<input class="fit" type="text" placeholder="Barkod Numarası" name="barcode" required>
<input class="fit" type="text" id="number" placeholder="Adet/Kg" oninput='formatNumber(this)' required>
<button id="addBtn" type="submit" class="btn-md btb flr">Ekle</button>
<p id="response"></p>
</form>
</div>
`;
        popup('Ürün Ekleme', body,'noreload');
    });

}

function clearScript(str) {
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#39;');
}

function priceCalculate(jsonData, unumber) {
    var number = formatPrice(unumber, "uni");
    const json = JSON.parse(jsonData);


    if (json.number >= number) {
        const addPrice = json.sale_price * number;
        const pricekdv = (addPrice * json.kdv) / 100;
        const priceotv = (addPrice * json.otv) / 100;
        const addPriceF = addPrice + pricekdv + priceotv;
        const addPriceFormatted = formatPrice(addPriceF, 'tr');

        var tbody = document.getElementById('saleList');
        var response = document.getElementById('response');

        var match = false;
        for (var i = 0; i < tbody.rows.length; i++) {
            var row = tbody.rows[i];
            var rowBarkod = row.cells[1].innerText;

            if (json.id == rowBarkod) {
                var numberCell = row.cells[row.cells.length - 3].innerText;
                var newNumber = parseFloat(numberCell) + parseFloat(number);
                var oldPrice = formatPrice(row.cells[row.cells.length - 2].innerText, "uni");
                var newPrice = addPriceF + oldPrice;

                console.log(numberCell);

                if (newNumber <= json.number) {
                    row.cells[row.cells.length - 6].innerText = formatPrice(parseFloat(json.number) - parseFloat(newNumber), "tr");
                    row.cells[row.cells.length - 3].innerText = formatPrice(newNumber, "tr");
                    row.cells[row.cells.length - 2].innerText = formatPrice(newPrice, "tr");

                    finalPrice += addPriceF;
                    document.getElementById('finalPrice').innerHTML = formatPrice(finalPrice, 'tr');

                    match = true;
                    break;

                } else {
                    response.innerHTML = `Stok yetersiz, stokda ${parseFloat(json.number) - parseFloat(numberCell)} ürün bulunmakta.`;
                    match = true;
                    break;
                }
            }

        }


        if (!match) {
            var html = `
    <tr>
    <td><img class="stockImg" src='../productImages/${json.image}' ></td>
    <td>${json.id}</td>
    <td>${clearScript(json.name)}</td>
    <td>${formatPrice(json.sale_price, "tr")}</td>
    <td>${formatPrice((parseFloat(json.number) - parseFloat(number)), "tr")}</td>
    <td>%${json.kdv}</td>
    <td>%${json.otv}</td>
    <td>${formatPrice(number, "tr")}</td>
    <td>${addPriceFormatted}</td>
    <td onclick='reduceProduct(${json.id});'><svg class='center' style="color:#991111;" width="20" height="20">
    <use xlink:href="../img/all.svg#close"></use>
    </svg></td>
    </tr>
    `;


            finalPrice += addPriceF;
            document.getElementById('finalPrice').innerHTML = '';
            document.getElementById('finalPrice').innerHTML = formatPrice(finalPrice, 'tr');
            document.getElementById('saleList').innerHTML += html;
        }
    } else {
        if (json.number == undefined) {
            document.getElementById('response').innerHTML = `Ürün bulunamadı!`;
        } else {
            document.getElementById('response').innerHTML = `Stok yetersiz, stokda ${json.number} ürün bulunmakta.`;
        }

    }

}

if (document.getElementById('finishSale')) {
    var btn = document.getElementById('finishSale');

    btn.addEventListener('click', function () {

        var body = `
     <div>
     <h4>Ödemeyi aldığımı onaylıyorum ve alışverişi bitirmek istiyorum.</h4>
     <button id="finishBtn" type="submit" class="btn-lg btg flr">Bitir</button>
     <p id="response"></p>
     </div>
     `;
        popup('Alışverişi Bitir!', body,'noreload');

        document.getElementById('finishBtn').addEventListener('click', function () {

            var tbody = document.querySelector("tbody");
            var rows = tbody.getElementsByTagName("tr");

            if (rows.length === 0) {
                document.getElementById("response").innerText = "Hiç bir ürün eklemeden alışveriş bitirilemez!";
            } else {
                document.getElementById('popup').innerHTML = "";
                postSale();
            }
        });
    });



    function postSale() {

        var tbody = document.querySelector("tbody");
        var rows = tbody.getElementsByTagName("tr");
        
        var jsonDataArray = [];
        
        var headers = [];
        var headerRow = document.querySelector("thead tr");
        var headerCells = headerRow.getElementsByTagName("th");
        for (var k = 1; k < headerCells.length - 1; k++) {
            headers.push(headerCells[k].textContent.trim());
        }
        
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var rowData = {};
        
            var cells = row.getElementsByTagName("td");
        
            for (var j = 1; j < cells.length - 1; j++) {
                rowData[headers[j - 1]] = cells[j].textContent.trim();
            }
        
            jsonDataArray.push(rowData);
        }
        

        var jsonData = JSON.stringify(jsonDataArray);


        var data = new FormData();
        data.append('requestType', 'finishSale');
        data.append('finalPrice', document.getElementById('finalPrice').innerText);
        data.append('json', jsonData);
        fetch("sale.php", { method: "post", body: data })
            .then(res => res.text())
            .then(txt => {


                var body = `
                <div>
                <iframe src='${txt}'>
                </iframe>
                <br>
                <div>
                <a href="${txt}" target="_blank"><button class="btb btn-md flr"  >Dosyaya git</button></a>
                </div>
                </div>
                `;
                popupIframe('Fatura Görüntüleme', body, 'main.php');

            })
            .catch(err => console.log(err));

        return false;

    }

}

var cancelBtn = document.getElementById("cancelSale");

cancelBtn.addEventListener("click", function () {

    var body = `
    <div>
    <h4>Alışverişi iptal etmek istediğinize eminmisiniz!</h4>
    <div class="btna">
    <button id="cancelYes" class="btn-md btr ">Evet</button>
    <button id="cancelNo"  class="btn-md btb ">Hayır</button>
    </div>
    </div>
    `;
    popup('Alışverişi İptal Et!', body,'noreload');

    document.getElementById("cancelYes").addEventListener("click", function () {
        window.location.href = "main.php";
    });
    document.getElementById("cancelNo").addEventListener("click", function () {
        document.getElementById('popup').innerHTML = "";
    });
});

function addDyProduct(product) {
    var body = `
    <div>
    <form id="getProduct" onsubmit="return posty(event,'sale.php','sale')">
    <input class="fit" style="display:none;" type="text" name="barcode" value="${product}" required>
    <input class="fit" type="text" id="number" placeholder="Adet/Kg" oninput='formatNumber(this)'  required>
    <button id="addBtn" type="submit" class="btn-md btb flr">Ekle</button>
    <p id="response"></p>
    </form>
    </div>
    `;
    popup('Ürün Ekleme', body,'noreload');

}

function reduceProduct(product){
    var tbody = document.getElementById('saleList');

for (var i = 0; i < tbody.rows.length; i++) {
    var row = tbody.rows[i];
    var rowBarkod = row.cells[1].innerText;

    var match = false;
    if (rowBarkod == product) {
        match = true;
    }

    if (match) {
        var productPrice = formatPrice(row.cells[row.cells.length - 2].innerText,"uni");
        finalPrice -= productPrice;  
        document.getElementById("finalPrice").innerText = formatPrice(finalPrice,"TR");
        tbody.deleteRow(i);
        i--; 
    }
}

}

document.addEventListener("DOMContentLoaded", function () {
    getStockList(1);
});

function getStockList(page) {
    var data = new FormData();
    data.append('requestType', 'getStockList');
    data.append('page', page);
    fetch("sale.php", { method: "post", body: data })
        .then(res => res.text())
        .then(txt => {
            var response = JSON.parse(txt);
            var data = response.data;
            var totalPages = response.totalPages;

            var tableContent = "";
            data.forEach(function (row) {
                tableContent += `
                <tr>
                <td><img class='stockImg' src='../productImages/${row.image}'></td>
                <td class='tx-cen'>${row.id}</td>
                <td>${row.name}</td><td>${formatPrice(row.sale_price, 'TR')}₺</td>
                <td style='width:2%;' onclick='addDyProduct("${row.id}");' >
                <svg width="20" height="25" style="transform:rotate(135deg);color:#297130;">
                        <use xlink:href="../img/all.svg#close"></use>
                    </svg>
                    </td>
                </tr>`;
            });

            html = `
            <table>
            <thead class='tx-cen'>
            <tr>
            <td>Resim</td>
            <td>Barkod</td>
            <td>Adı</td>
            <td>Fiyatı</td>
            </tr>
            </thead>
            <tbody>
            ${tableContent}
            </tbody>
            </table>

            `;
            document.getElementById('stockList').innerHTML = html;


            var paginationContent = "";
            if (page > 1) {
                paginationContent += "<button class='pagei' onclick='getStockList(" + (page - 1) + ")'><svg class='' style='transform:rotate(90deg);' width='22' height='22'><use xlink:href='../img/all.svg#dropDown'></use></svg></button>";
            }
            for (var i = 1; i <= totalPages; i++) {
                if (i == page) {
                    paginationContent += "<button class='pagei already'>" + i + "</button>";
                } else {
                    paginationContent += "<button class='pagei' onclick='getStockList(" + i + ")'>" + i + "</button>";
                }
            }
            if (page < totalPages) {
                paginationContent += "<button class='pagei' onclick='getStockList(" + (page + 1) + ")'><svg style='transform:rotate(270deg);' width='22' height='22'><use xlink:href='../img/all.svg#dropDown'></use></svg></button>";
            }
            document.getElementById('pagination').innerHTML = paginationContent;
        })
        .catch(err => console.log(err));

    return false;
}
