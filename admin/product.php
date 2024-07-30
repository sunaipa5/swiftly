<?php

session_start();
if (!isset($_SESSION['swiftlyadmin']) || $_SESSION['swiftlyadmin'] != sha1(md5("9Ar7dasSTQhdayuıAS5uuy"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");
require("../format.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'deleteProduct') {
        $barcode = $_POST['barcode'];
        $sql = "SELECT image FROM stock WHERE id=:id";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':id', $barcode);
        $stmt->execute();

        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $imagePath = "../productImages/" . $file['image'];
            if (unlink($imagePath)) {
                $delete = $connect->prepare("DELETE FROM stock WHERE id=:barcode");
                $delete->bindParam(':barcode', $barcode, PDO::PARAM_STR);
                $delete->execute();

                echo "Ürün Kaldırıldı.";
            } else {
                echo "Ürün resmi silinemedi.";
            }
        } else {
            echo "Ürün bulunamadı.";
        }
        exit;
    }




    if (isset($_POST['requestType']) && $_POST['requestType'] === 'addProduct') {

        $uploadedFile = $_FILES['productImage']['tmp_name'];

        $width = 0;
        $height = 0;

        if (file_exists($uploadedFile)) {
            list($width, $height) = getimagesize($uploadedFile);
            $fileName = $_FILES['productImage']['name'];
            $fileSize = $_FILES['productImage']['size'];
        }

        if ($width !== $height) {
            echo "Resim kare olmalıdır!";
            exit;
        } else {



            $barcode = $_POST['barcode'];

            $query = $connect->prepare("SELECT id FROM stock WHERE id = :id");
            $query->bindParam(':id', $barcode, PDO::PARAM_STR);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                echo "Aynı barkod numarasına sahip birden fazla ürün olmaz!";
                exit;
            } else {


                $productName = $_POST['productName'];
                $salePrice = formatNumber($_POST['salePrice']);
                $arrivalPrice = formatNumber($_POST['arrivalPrice']);
                $kdv = formatNumber($_POST['kdv']);
                $otv = formatNumber($_POST['otv']);
                $number = formatNumber($_POST['number']);

                $tempname = $_FILES["productImage"]["tmp_name"];
                $fileExt = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
                $folder = "../productImages/" . $barcode . "." . $fileExt;
                $fileName = $barcode . "." . $fileExt;

                if ($productName == "" || $barcode == "" || $salePrice == "" || $arrivalPrice == "" || $number == "" || $tempname == "") {
                    echo "Tüm alanlar doldurulmalı!";
                    exit;
                } else {
                    if (!move_uploaded_file($tempname, $folder)) {
                        echo "Fotoğraf yükleme hatası!";
                        exit;
                    } else {
                        $save = $connect->prepare("INSERT INTO stock(id,name,sale_price,arrival_price,kdv,otv,number,image) VALUES (:id,:name,:sale_price,:arrival_price,:kdv,:otv,:number,:image)");
                        $save->bindParam(':id', $barcode, PDO::PARAM_STR);
                        $save->bindParam(':name', $productName, PDO::PARAM_STR);
                        $save->bindParam(':sale_price', $salePrice, PDO::PARAM_STR);
                        $save->bindParam(':arrival_price', $arrivalPrice, PDO::PARAM_STR);
                        $save->bindParam(':kdv', $kdv, PDO::PARAM_STR);
                        $save->bindParam(':otv', $otv, PDO::PARAM_STR);
                        $save->bindParam(':number', $number, PDO::PARAM_STR);
                        $save->bindParam(':image', $fileName, PDO::PARAM_STR);
                        $save->execute();
                        echo "Ürün eklendi."; // veya başka bir cevap
                        exit;
                    }
                }
            }
        }
    }

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'getProduct') {
        $barcode = $_POST['barcode'];
        $query = $connect->prepare("SELECT * FROM stock WHERE id = :id");
        $query->bindParam(':id', $barcode, PDO::PARAM_STR);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC); // Tek bir satırı al

        if ($row) {
            $data = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'sale_price' => $row['sale_price'],
                'arrival_price' => $row['arrival_price'],
                'kdv' => $row['kdv'],
                'otv' => $row['otv'],
                'number' => $row['number'],
                'image' => $row['image'],

            );

            echo json_encode($data);
        } else {
            echo "Ürün bulunamadı!";
        }

        $connect = null;
        exit;


    }

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'changeProduct') {

        $uploadedFile = $_FILES['productImage']['tmp_name'];

        $width = 0;
        $height = 0;

        if (file_exists($uploadedFile)) {
            list($width, $height) = getimagesize($uploadedFile);
            $fileName = $_FILES['productImage']['name'];
            $fileSize = $_FILES['productImage']['size'];
        }

        if ($width !== $height) {
            echo "Resim kare olmalıdır!";
            exit;
        } else {

            $oldBarcode = $_POST['oldBarcode'];
            $tempname = $_FILES["productImage"]["tmp_name"];

            if ($oldBarcode != $_POST['barcode']) {
                $query = $connect->prepare("SELECT id FROM stock WHERE id = :id");
                $query->bindParam(':id', $_POST['barcode'], PDO::PARAM_STR);
                $query->execute();

                $row = $query->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    echo "Aynı barkod numarasına sahip birden fazla ürün olmaz!";
                    exit;
                }
            } else {

                if ($tempname != '') {
                    $newBarcode = '';

                    if ($oldBarcode == $_POST['barcode']) {
                        $newBarcode = $oldBarcode;
                    } else if ($oldBarcode != $_POST['barcode']) {
                        $newBarcode = $_POST['barcode'];
                    }

                    $path = '../productImages/';

                    $files = scandir($path);

                    foreach ($files as $file) {
                        $filePath = $path . $file;

                        $fileName = pathinfo($filePath, PATHINFO_FILENAME);

                        if ($fileName === $newBarcode && is_file($filePath)) {
                            unlink($filePath);
                            break;
                        }
                    }

                    $fileExt = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
                    $folder = "../productImages/" . $newBarcode . "." . $fileExt;
                    $fileName = $newBarcode . "." . $fileExt;

                    if (!move_uploaded_file($tempname, $folder)) {
                        echo "Fotoğraf yükleme hatası!";
                        exit;
                    }
                } else if ($oldBarcode != $_POST['barcode']) {
                    $path = '../productImages/';

                    $files = scandir($path);

                    foreach ($files as $file) {
                        $filePath = $path . $file;

                        $oldfileName = pathinfo($filePath, PATHINFO_FILENAME);

                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

                        if ($oldfileName === $oldBarcode && is_file($filePath)) {

                            $newFileName = $_POST['barcode'] . '.' . $fileExtension;

                            if (rename($filePath, $path . $newFileName)) {
                                $fileName = $newFileName;
                            }

                            break;
                        }
                    }

                }
            }



            $data = [
                'name' => $_POST['productName'] ?? null,
                'sale_price' => $_POST['salePrice'] ?? '',
                'arrival_price' => $_POST['arrivalPrice'] ?? null,
                'kdv' => $_POST['kdv'] ?? null,
                'otv' => $_POST['otv'] ?? null,
                'number' => $_POST['number'] ?? null,
                'id' => $_POST['barcode'] ?? null,
                'image' => $fileName ?? null
            ];

            $sql = "UPDATE stock SET ";

            $updateFields = [];
            $bindParamTypes = [];

            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $updateFields[] = "$key = :" . $key;
                    $bindParamTypes[$key] = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                }

            }



            if (!empty($updateFields) && $oldBarcode) {
                $sql .= implode(', ', $updateFields) . " WHERE id = :recordID";

                $stmt = $connect->prepare($sql);

                foreach ($data as $key => &$value) {
                    if (isset($bindParamTypes[$key]) && $value !== "" || $value !== null) {
                        if ($key !== 'name' && $key !== 'image') {
                            $value = formatNumber($value);
                            $stmt->bindParam(":$key", $value, $bindParamTypes[$key]);
                        } else if ($key == 'name' || $key == 'image') {
                            $stmt->bindParam(":$key", $value, $bindParamTypes[$key]);

                        }

                    }



                }


                $stmt->bindParam(':recordID', $oldBarcode, PDO::PARAM_INT);

                $stmt->execute();
            }


            echo "Ürün Güncellendi.";

            exit;
        }

    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Yönetimi</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        .passwordInp {
            width: fit-content;
            border: none;
            background-color: transparent;
        }

        .imgw {
            margin: 0;
        }

        .imgw img {
            border-radius: 7px;
            width: 100px;
            height: 100px;
        }

        .imgw div {
            width: 120px;

        }

        td,
        th {
            width: 12.5%;
        }

        td {
            height: 65px;
        }

        table {
            width: 100%;
        }


        .stockImg:hover {
            width: 300px;
           position: absolute;
            z-index: 999;
            
        }
        
    </style>
</head>
<header>
    <?php require "navbar.php"; ?>
</header>

<body class="preload light">
    <main>
        <div class="center mvp-90 container">
            <div class="fll">
                <div class="">
                    <div class="">
                        <div class="btn-c " id="addProductBtn">
                            <div class="scale hmid ">
                                <svg width="80" height="60">
                                    <use xlink:href="../img/all.svg#productAdd"></use>
                                </svg>
                                <h4>Ürün ekle</h4>
                            </div>
                        </div>
                        <div class="btn-c hmid " id="deleteProductBtn">
                            <div class="scale hmid ">
                                <svg width="80" height="60">
                                    <use xlink:href="../img/all.svg#productDelete"></use>
                                </svg>
                                <h4>Ürün kaldır</h4>
                            </div>
                        </div>
                        <div class="btn-c hmid " id="getProduct">
                            <div class="scale hmid ">
                                <svg width="80" height="60">
                                    <use xlink:href="../img/all.svg#product"></use>
                                </svg>
                                <h4>Bilgi değiştir</h4>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="">
                    <div class="col col-xl row">

                        <table class="center">
                            <thead>
                                <tr>
                                    <th>Resim</th>
                                    <th>Barkod</th>
                                    <th>Adı</th>
                                    <th>Adet/Kg</th>
                                    <th>Alış Fiyatı</th>
                                    <th>Satış Fiyatı</th>
                                    <th>KDV</th>
                                    <th>ÖTV</th>
                                    <th>Değiştir</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                $itemsPerPage = 12;

                                $page = isset($_GET['page']) ? $_GET['page'] : 1;

                                $startFrom = ($page - 1) * $itemsPerPage;

                                $sql = "SELECT id, name, number, arrival_price, sale_price, kdv, otv,image FROM stock ORDER BY id LIMIT $startFrom, $itemsPerPage";
                                $stmt = $connect->query($sql);

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td><img class='stockImg' src='../productImages/" . $row['image'] . "'></td>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars(formatTR($row['number'])) . "</td>";
                                    echo "<td>" . htmlspecialchars(formatTR($row['arrival_price'])) . "</td>";
                                    echo "<td>" . htmlspecialchars(formatTR($row['sale_price'])) . "</td>";
                                    echo "<td>%" . htmlspecialchars(formatTR($row['kdv'])) . "</td>";
                                    echo "<td>%" . htmlspecialchars(formatTR($row['otv'])) . "</td>";
                                    echo "<td onclick='quickEdit(".$row['id'].")'><svg width='25' height='25'><use xlink:href='../img/all.svg#edit'></use></svg></td>";
                                    echo "</tr> ";
                                }

                                echo "</tbody></table>";

                                $totalStmt = $connect->query("SELECT COUNT(*) as total FROM stock");
                                $totalRows = $totalStmt->fetch()['total'];
                                $totalPages = ceil($totalRows / $itemsPerPage);

                                // Sayfalama bağlantılarını oluştur
                                echo "<div class='pagination center vex'>";
                                if ($page > 1) {
                                    echo "<a href='?page=" . ($page - 1) . "'><button class='pagei '><svg class='' style='transform:rotate(90deg);' width='22' height='22'><use xlink:href='../img/all.svg#dropDown'></use></svg></button></a>";
                                }
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    if ($i == $page) {
                                        echo "<a href='?page=$i'><button class='pagei already'>$i</button></a> ";
                                    } else {
                                        echo "<a href='?page=$i'><button class='pagei'>$i</button></a> ";
                                    }
                                }
                                if ($page < $totalPages) {
                                    echo "<a href='?page=" . ($page + 1) . "'><button class='pagei veb'><svg style='transform:rotate(270deg);' width='22' height='22'><use xlink:href='../img/all.svg#dropDown'></use></svg></button></a>";
                                }
                                echo "</div>";


                                ?>
                            </tbody>
                        </table>
                        <?php

                        ?>
                    </div>

                </div>
            </div>


        </div>
    </main>
</body>
<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/popupMenu.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/format.js"></script>
<script src="../scripts/admin/product.js"></script>
<script src="../scripts/logout.js"></script>

</html>