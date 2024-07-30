<?php
session_start();
if (!isset($_SESSION['swiftlystaff']) || $_SESSION['swiftlystaff'] != sha1(md5("nmHN738NDSSasdaU26d62Naj"))) {
    header("Location: index.php");
    exit;
}
require("../set.php");
require("../format.php");

if (isset($_POST['requestType']) && $_POST['requestType'] === 'getStockList') {
    $itemsPerPage = 10;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $startFrom = ($page - 1) * $itemsPerPage;

    $sql = "SELECT id,name,sale_price,image FROM stock ORDER BY id LIMIT $startFrom, $itemsPerPage";
    $stmt = $connect->query($sql);

    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $totalStmt = $connect->query("SELECT COUNT(*) as total FROM stock");
    $totalRows = $totalStmt->fetch()['total'];
    $totalPages = ceil($totalRows / $itemsPerPage);

    $response = [
        'data' => $data,
        'totalPages' => $totalPages
    ];

    echo json_encode($response);
    exit;
}


if (isset($_POST['requestType']) && $_POST['requestType'] === 'getProduct') {
    $barcode = $_POST['barcode'];
    $query = $connect->prepare("SELECT id,name,sale_price,number,kdv,otv,image FROM stock WHERE id = :id");
    $query->bindParam(':id', $barcode, PDO::PARAM_STR);
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC); // Tek bir satırı al

    if ($row) {
        $data = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'sale_price' => $row['sale_price'],
            'kdv' => $row['kdv'],
            'otv' => $row['otv'],
            'number' => $row['number'],
            'image' => $row['image'],
        );

        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Veri bulunamadı'));
    }

    $connect = null;
    exit;

}

if (isset($_POST['requestType']) && $_POST['requestType'] === 'finishSale') {
    $pureJson = $_POST['json'];
    $jsonData = json_decode($pureJson, true);

    if ($jsonData !== null && is_array($jsonData)) {
        $data = "";

        foreach ($jsonData as $product) {
            $barcode = $product['Barkod'];
            $number = formatNumber($product['Adet/Kg']);
            $sql = "UPDATE stock SET number = number - :saleNumber WHERE id = :barcode";
            $stmt = $connect->prepare($sql);

            $stmt->bindParam(':saleNumber', $number, PDO::PARAM_INT);
            $stmt->bindParam(':barcode', $barcode, PDO::PARAM_STR);

            $stmt->execute();
        }


    } else {
        echo "Geçersiz veya işlenemeyen bir veri geldi";
    }

    echo logCreate($jsonData, $_POST['finalPrice']);

    $connect = null;
    exit;
}

function jsonMetrics($fileName, $finalPrice, $jsonData)
{
    if (file_exists($fileName)) {
        $existingData = file_get_contents($fileName);
        $existingArray = json_decode($existingData, true);

        $productNumber = 0;
        $productWeight = 0;
        $taxFreePrice = 0;


        foreach ($jsonData as $product) {
            $number = formatNumber($product['Adet/Kg']);
            $price = formatNumber($product['Adet/Kg']) * formatNumber($product['Birim Fiyatı']);

            if (strpos($number, '.') !== false || strpos($number, ',') !== false) {

                $productWeight += $number;
            } else {

                $productNumber += $number;

            }
            $taxFreePrice += $price;
        }




        if ($existingArray !== null) {
            $existingArray['NumberOfProductsNumber'] = strval(intval($existingArray['NumberOfProductsNumber']) + $productNumber);
            $existingArray['NumberOfProductsWeight'] = strval(floatval($existingArray['NumberOfProductsWeight']) + $productWeight);
            $existingArray['NumberOfSales'] = strval(floatval(($existingArray['NumberOfSales']) ?? 0) + 1);
            $existingArray['TaxFreePrice'] = strval(floatval($existingArray['TaxFreePrice']) + $taxFreePrice);
            $existingArray['FinalPrice'] = strval(floatval($existingArray['FinalPrice']) + formatNumber($finalPrice));


            file_put_contents($fileName, json_encode($existingArray));
        }
    }
}

function logCreate($jsonData, $finalPrice)
{

    date_default_timezone_set('Europe/Istanbul');
    $time = date("H:i");
    $date = date("Y-m-d");
    $monthYear = date("m-Y");
    $monthDay = date("d-m-Y");
    $yearMonth = date("Y-m");

    $productNumber = 0;
    $productWeight = 0;
    $taxFreePrice = 0;

    foreach ($jsonData as $product) {

        $number = formatNumber($product['Adet/Kg']);
        $price = formatNumber($product['Adet/Kg']) * formatNumber($product['Birim Fiyatı']);
        if (strpos($number, '.') !== false || strpos($number, ',') !== false) {
            $productWeight += $number;

        } else {
            $productNumber += $number;
        }

        $taxFreePrice += $price;

    }


    $folderName = '../faturalar/' . str_replace('-', '/', $date);
    $fileName = '../faturalar/' . str_replace('-', '/', $yearMonth) . '/' . $monthYear . '.json';
    $fileNameDay = '../faturalar/' . str_replace('-', '/', $date) . '/' . $monthDay . '.json';

    if (!file_exists($folderName)) {
        mkdir($folderName, 0777, true);
    }

    $newFinalPrice = formatNumber($finalPrice);
    if (!file_exists($fileName)) {
        $newFile = fopen($fileName, "w");


        $data = array(
            'NumberOfSales' => "1",
            'NumberOfProductsNumber' => "$productNumber",
            'NumberOfProductsWeight' => "$productWeight",
            'TaxFreePrice' => "$taxFreePrice",
            'FinalPrice' => "$newFinalPrice"
        );

        $jsonReady = json_encode($data);
        file_put_contents($fileName, $jsonReady);

        fclose($newFile);
    } else {
        jsonMetrics($fileName, $finalPrice, $jsonData);
    }

    if (!file_exists($fileNameDay)) {
        $newFileDay = fopen($fileNameDay, "w");


        $dataDay = array(
            'NumberOfSales' => "1",
            'NumberOfProductsNumber' => "$productNumber",
            'NumberOfProductsWeight' => "$productWeight",
            'TaxFreePrice' => "$taxFreePrice",
            'FinalPrice' => "$newFinalPrice"
        );

        $jsonReadyDay = json_encode($dataDay);
        file_put_contents($fileNameDay, $jsonReadyDay);

        fclose($newFileDay);
    } else {
        jsonMetrics($fileNameDay, $finalPrice, $jsonData);
    }


    $baseDate = date("d-m-Y");

    $files = glob($folderName . '/' . $baseDate . '-*.json');
    $latestNumber = 0;

    foreach ($files as $file) {
        $fileName = basename($file, '.json');
        $parts = explode('-', $fileName);
        $number = (int) end($parts);

        if ($number > $latestNumber) {
            $latestNumber = $number;
        }
    }

    $newFileNumber = $latestNumber + 1;
    $newFileNameTxt = $folderName . '/' . $baseDate . '-' . $newFileNumber . '.json';


    require("../set.php");


    $saleId = date("dmY") . (count($files) + 1);
    $logfile = $folderName . '/' . date("d-m-Y") . '-' . (count($files) + 1);
    $staffId = $_SESSION['staffid'];
    $daten = date("d/m/Y H:i");
    $price = formatNumber($finalPrice);


    /*Day detailed json*/
    $dayJson = array(
        'SatışNumarası' => "$saleId",
        'Tarih' => $monthDay . ' ' . $time,
        'Kasiyer' => $_SESSION['staffname'] . ' ' . $_SESSION['staffsurname'],
        'TaxFreePrice' => "$taxFreePrice",
        'FinalPrice' => "$newFinalPrice"
    );

    $fileContents = [$dayJson, $jsonData];


    file_put_contents($newFileNameTxt, json_encode($fileContents, JSON_PRETTY_PRINT));



    $save = $connect->prepare("INSERT INTO sale(sale_id,log_file,staff_id,date,price) VALUES (:saleId,:logFile,:staffId,:date,:price)");
    $save->bindParam(':saleId', $saleId, PDO::PARAM_STR);
    $save->bindParam(':logFile', $logfile, PDO::PARAM_STR);
    $save->bindParam(':staffId', $staffId, PDO::PARAM_STR);
    $save->bindParam(':date', $daten, PDO::PARAM_STR);
    $save->bindParam(':price', $price, PDO::PARAM_STR);
    $save->execute();

    require('../tfpdf/tfpdf.php');

    $newFileNamePdf = str_replace('.json', '.pdf', $newFileNameTxt);

    $pdf = new tFPDF();
    $pdf->AddPage();

    $pdf->AddFont('roboto', '', 'Roboto-Medium.ttf', true);
    $pdf->AddFont('roboto', 'B', 'Roboto-Bold.ttf', true);
    $pdf->AddFont('roboto', 'L', 'Roboto-Light.ttf', true);

    $pdf->SetFont('roboto', 'B', 13);
    $pdf->Cell(0, 10, 'Swiftly', 0, 1);


    $pdf->SetFont('roboto', '', 8);
    $pdf->Cell(25, 5, 'Satış Numarası:', 1);
    $pdf->Cell(50, 5, $saleId, 1);
    $pdf->Ln();
    $pdf->Cell(25, 5, 'Tarih:', 1);
    $pdf->Cell(50, 5, $monthDay . ' ' . $time, 1);
    $pdf->Ln();
    $pdf->Cell(25, 5, 'Kasiyer:', 1);
    $pdf->Cell(50, 5, $_SESSION['staffname'] . ' ' . $_SESSION['staffsurname'], 1);
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('roboto', 'B', 10);
    $pdf->Cell(25, 7, 'Barkod', 1);
    $pdf->Cell(50, 7, 'Ürün Adı', 1);
    $pdf->Cell(30, 7, 'Birim Fiyatı', 1);
    $pdf->Cell(10, 7, 'KDV', 1);
    $pdf->Cell(10, 7, 'ÖTV', 1);
    $pdf->Cell(30, 7, 'Adet/Kg', 1);
    $pdf->Cell(30, 7, 'Toplam Fiyat', 1);
    $pdf->Ln();

    $pdf->SetFont('roboto', '', 10);
    foreach ($jsonData as $product) {
        $pdf->Cell(25, 5, $product['Barkod'], 1);
        $pdf->Cell(50, 5, strlen($product['Adı']) > 27 ? substr($product['Adı'], 0, 27) . '...' : $product['Adı'], 1);
        $pdf->Cell(30, 5, $product['Birim Fiyatı'], 1);
        $pdf->Cell(10, 5, $product['KDV'], 1);
        $pdf->Cell(10, 5, $product['ÖTV'], 1);
        $pdf->Cell(30, 5, $product['Adet/Kg'], 1);
        $pdf->Cell(30, 5, $product['Toplam Fiyat'], 1);
        $pdf->Ln();
    }
    $pdf->Ln();
    $pdf->SetFont('roboto', 'B', 12);
    $pdf->Cell(75, 7, 'Genel toplam: ' . $finalPrice, 1);
    $pdf->Output($newFileNamePdf, 'F');

    return $newFileNamePdf;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <style>
        .topr div {
            width: 90px;
            font-size: 13px;

        }

        td,
        th {
            width: 12.5%;
        }

        table {
            width: 100%;
        }

        #info p {
            font-weight: 600;
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

        #stockList td {
            width: fit-content;
            max-width: 210px;
        }
    </style>
</head>
<script>
    /*
    window.onbeforeunload = function () {
        return "Sayfayı yenilerseniz alışveriş iptal olacaktır!!";
    };
    */
</script>

<body class="center dark">
    <?php require "navbar.php"; ?>
    <main>
        <!--Top-Section-->
        <div class="fll">
            <div>

                <div class="col col-smx">
                    <div>
                        <?php
                        echo '<p>' . $_SESSION['staffname'];
                        echo ' ' . $_SESSION['staffsurname'] . '</p>';
                        ?>
                    </div>
                    <div id="info">

                    </div>
                    <h4 class="vex">Genel toplam:&nbsp;<p id="finalPrice">0</p>₺</h4>
                </div>
                <br>
                <div class="flr topr center">

                    <div class="btn-x btb hmid" id="addProduct">
                        <svg width="35" height="35">
                            <use xlink:href="../img/all.svg#saleAdd"></use>
                        </svg>
                        <h4>Ürün Ekle</h4>
                    </div>
                    <div class="btn-x btg hmid " id="finishSale">
                        <svg width="35" height="35">
                            <use xlink:href="../img/all.svg#saleFinish"></use>
                        </svg>
                        <h4>Alışveriş Bitir</h4>
                    </div>
                    <div class="btn-x btr hmid" id="cancelSale">
                        <svg width="35" height="35">
                            <use xlink:href="../img/all.svg#saleCancel"></use>
                        </svg>
                        <h4>Alışveriş İptal</h4>
                    </div>

                </div>
            </div>
            <br>
            <!--Sale-List-->
            <div class="center">
                <div class="col col-xl over-x">
                    <table class="center">
                        <thead>
                            <th>Resim</th>
                            <th>Barkod</th>
                            <th>Adı</th>
                            <th>Birim Fiyatı</th>
                            <th>Stok</th>
                            <th>KDV</th>
                            <th>ÖTV</th>
                            <th>Adet/Kg</th>
                            <th>Toplam Fiyat</th>
                            <th>Kaldır</th>
                        </thead>
                        <tbody id="saleList">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-smx container imgw" id="imageview">

            </div>
        </div>

        <div class="col col-xlx flr over-x">
            <div id="stockList"></div>
            <div class="pagination center vex" id="pagination">

            </div>
        </div>


    </main>
</body>
<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/format.js"></script>
<script src="../scripts/popupMenu.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/staff/sale.js"></script>
<script src="../scripts/time.js"></script>
<script src="../scripts/logout.js"></script>

</html>