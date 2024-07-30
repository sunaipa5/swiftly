<?php
session_start();
if (!isset($_SESSION['swiftlyadmin']) || $_SESSION['swiftlyadmin'] != sha1(md5("9Ar7dasSTQhdayuıAS5uuy"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'getBill') {
        $salenumber = $_POST['salenumber'];
        $query = $connect->prepare("SELECT log_file FROM sale WHERE sale_id = :salenumber");
        $query->bindParam(':salenumber', $salenumber, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo $row['log_file'];
        } else {
            echo "Fatura Bulunamadı!";
        }

        $connect = null;
        exit;

    }

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'getLowStock') {

    $sql = "SELECT * FROM stock WHERE number < 3 ORDER BY number ASC;";
    $stmt = $connect->query($sql);

    if ($stmt->rowCount() > 0) {

        echo '
        <h2 class="center">Azalan Stoklar</h2>
        <table class="center fit">
        <thead>
        <tr>
        <td>Barkod</td>
        <td>Adı</td>
        <td>Adet/KG</td>
        </tr>
        </thead>
        <tbody>
        ';

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . $row['name'] . '</td>
        <td style="color:red;font-weight:900;">' . $row['number'] . '</td>
        </tr>';
        }

        echo '</tbody></table>';

    }

    exit;

   }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiftly Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <style>
        .btn-c {
            width: 140px;
            height: 140px;
            margin-top: 0;
            margin-bottom: 0;
        }
    </style>
</head>
<header>
    <?php require "navbar.php"; ?>
</header>

<body class="preload center mvp-80 light">
    <div id="popup"></div>
    <main class="mvp-90">

       
        <div class="container">

            <div class="col col-sm">
                <h2 class="center">Günlük veriler</h2>
                <?php
                $fileName = '../faturalar/' . date("Y/m/d") . '/' . date("d-m-Y") . '.json';
                echo "<p class='center'>" . date("d/m/Y") . "</p>";
                ?>


                <div class="container">

                    <?php

                    require("../format.php");

                    if (file_exists($fileName)) {

                        $jsonData = file_get_contents($fileName);
                        $data = json_decode($jsonData, true);


                        if ($data !== null) {

                            echo '
                                <div class="row">
                                <div class="vex">
                                <b>Yapılan Satış:&nbsp;</b>
                                <p>' . $data['NumberOfSales'] . '</p>
                            </div>
                            <br>
                            <div class="vex">
                                <b>Satılan Ürün Adedi:&nbsp;</b>
                                <p>' . $data['NumberOfProductsNumber'] . '</p>
                            </div>
                            <br>
                            <div class="vex">
                            <b>Satılan Ürün KG:&nbsp;</b>
                            <p>' . formatTR($data['NumberOfProductsWeight']) . '</p>
                            </div>
                            </div>
                            <div class="row">
                            <div class="vex">
                            <b>Vergisiz Fiyat:&nbsp;</b>
                            <p>' . formatTR($data['TaxFreePrice']) . '₺</p>
                            </div>
                            <br>
                            <div class="vex">
                            <b>Fiyat:&nbsp;</b>
                            <p>' . formatTR($data['FinalPrice']) . '₺</p>
                            </div>
                            </div>
                        ';



                        }
                    } else {
                        echo "<h3>Veri yok</h3>";
                    }
                    ?>



                </div>
            </div>
            <div class="col col-sm">
                <h2 class="center">Aylık veriler</h2>
                <?php
                $fileName = '../faturalar/' . date("Y/m") . '/' . date("m-Y") . '.json';
                echo "<p class='center'>" . date("m/Y") . "</p>";
                ?>
                <div class="container">

                    <?php



                    if (file_exists($fileName)) {
                        $jsonData = file_get_contents($fileName);
                        $data = json_decode($jsonData, true);

                        if ($data !== null) {
                            echo '
                                <div class="row">
                                <div class="vex">
                                <b>Yapılan Satış:&nbsp;</b>
                                <p>' . $data['NumberOfSales'] . '</p>
                            </div>
                            <br>
                            <div class="vex">
                                <b>Satılan Ürün Adedi:&nbsp;</b>
                                <p>' . $data['NumberOfProductsNumber'] . '</p>
                            </div>
                            <br>
                            <div class="vex">
                            <b>Satılan Ürün KG:&nbsp;</b>
                            <p>' . formatTR($data['NumberOfProductsWeight']) . '</p>
                            </div>
                            </div>
                            <div class="row">
                            <div class="vex">
                            <b>Vergisiz Fiyat:&nbsp;</b>
                            <p>' . formatTR($data['TaxFreePrice']) . '₺</p>
                            </div>
                            <br>
                            <div class="vex">
                            <b>Fiyat:&nbsp;</b>
                            <p>' . formatTR($data['FinalPrice']) . '₺</p>
                            </div>
                            </div>
                            
                        ';



                        }
                    } else {
                        echo "<h3>Veri yok</h3>";
                    }



                    ?>

                </div>
            </div>
        </div>

        <!--Quick-Processes-->
        <div class="container">
            <div class="col-c col-sm ">
                <h2 class="center">Hızlı işlemler</h2>
                <div class="container">
                    <a>
                        <div class="btn-c hover " id="addProductBtn">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#productAdd"></use>
                                </svg>
                                <h4>Ürün ekle</h4>
                            </div>
                        </div>
                    </a>
                    <a>
                        <div class="btn-c hover" id="addStaffBtn">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#staffAdd"></use>
                                </svg>
                                <h4>Personel ekle</h4>
                            </div>
                        </div>
                    </a>
                    <a>
                        <div class="btn-c hover" id="getBill">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#bill"></use>
                                </svg>
                                <h4>Fatura Görüntüle</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!--Management-->
            <div class="col-c col-sm ">
                <h2 class="center">Yönetim</h2>
                <div class="container">
                    <a href="product.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#product"></use>
                                </svg>
                                <h4>Ürün Yönetimi</h4>
                            </div>
                        </div>
                    </a>
                    <a href="staff.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#staff"></use>
                                </svg>
                                <h4>Personel Yönetimi</h4>
                            </div>
                        </div>
                    </a>
                    <a href="manager.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#manager"></use>
                                </svg>
                                <h4>Yönetici Hesapları</h4>
                            </div>
                        </div>
                    </a>
                    <a href="sale.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#sale"></use>
                                </svg>
                                <h4>Satış Kayıtları</h4>
                            </div>
                        </div>
                    </a>
                    <a href="statics.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#statics"></use>
                                </svg>
                                <h4>İstatistikler</h4>
                            </div>
                        </div>
                    </a>
                    <a href="registry.php" >
                        <div class="btn-c hover">
                            <div class="scale flx hmid">
                                <svg width="80" height="80">
                                    <use xlink:href="../img/all.svg#registry"></use>
                                </svg>
                                <h4>Kayıt Defteri</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


    </main>
</body>

<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/popupMenu.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/admin/staff.js"></script>
<script src="../scripts/admin/product.js"></script>
<script src="../scripts/format.js"></script>
<script src="../scripts/logout.js"></script>
<script>
lowStock();

function lowStock() {
    var data = new FormData();
    data.append('requestType', 'getLowStock');
    
    fetch('main.php', { method: "post", body: data })
        .then(res => res.text())
        .then(txt => {
            if (txt.trim() !== "") {
             

                var containerDivs = document.createElement('div');
                containerDivs.innerHTML = `
                    <div class="container">
                        <div class="col fit">
                            ${txt.trim()}
                        </div>
                    </div>
                `;     
                var mainTag = document.querySelector('main');
                mainTag.insertBefore(containerDivs, mainTag.children[0]);
            }
        })
        .catch(err => console.log(err));
}


  
</script>
</html>