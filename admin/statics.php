<?php

session_start();
if (!isset($_SESSION['swiftlyadmin']) || $_SESSION['swiftlyadmin'] != sha1(md5("9Ar7dasSTQhdayuıAS5uuy"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");
require("../format.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İstatistikler</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        .col-t {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<header>
    <?php require "navbar.php"; ?>
</header>

<body class="preload light">
    <main>
        <div class="center">
            <h2>İstatistikler</h2>


            <?php
            function listFiles($baseFolder)
            {
                $yearFolders = glob($baseFolder . '/*', GLOB_ONLYDIR);
                rsort($yearFolders);

                $yearIndex = 0;

                foreach ($yearFolders as $yearFolder) {
                    $yearIndex++;

                    echo "<div class='col col-xl' id='statics'>";
                    echo "<div onclick='toggleElements(\"#year$yearIndex .months\")' class='dropdown year' id='year$yearIndex'><svg class='flr' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                    echo "<h2 >Yıl - " . basename($yearFolder) . "</h2>";

                    $monthFolders = glob($yearFolder . '/*', GLOB_ONLYDIR);
                    natsort($monthFolders);
                    $monthIndex = 0;

                    foreach ($monthFolders as $monthFolder) {
                        $monthIndex++;

                        echo "<div onclick='toggleElements(\"#months$yearIndex$monthIndex .days\")' class='dropdown months' id='months$yearIndex$monthIndex'><svg  class='flr ' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                        echo "<h3>Ay - " . basename($monthFolder) . "</h3><hr>";


                        $jsonFiles = glob($monthFolder . '/*.json');
                        foreach ($jsonFiles as $jsonFile) {
                            $jsonContent = file_get_contents($jsonFile);
                            $data = json_decode($jsonContent, true);


                            echo '
                            <div class="dropdown days" >
                            <div class="container">
                            
                            <div class="statics">
                            <div class="center"><b>' . pathinfo($jsonFile, PATHINFO_FILENAME) . '</b> Tarihine ait istatistikler</div>
                            <table>
                            <thead>
                            <tr>
                            <td>Yapılan Satış</td>
                            <td>Satılan Ürün Adedi</td>
                            <td>Satılan Ürün KG</td>
                            <td>Vergisiz Fiyat</td>
                            <td>Fiyat</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <td>' . htmlspecialchars($data['NumberOfSales']) . '</</td>
                            <td>' . htmlspecialchars($data['NumberOfProductsNumber']) . '</td>
                            <td>' . htmlspecialchars(formatTR($data['NumberOfProductsWeight'])) . '</td>
                            <td>' . htmlspecialchars(formatTR($data['TaxFreePrice'])) . '₺</td>
                            <td>' . htmlspecialchars(formatTR($data['FinalPrice'])) . '₺</td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                            </div>
                            </div>
                    ';

                        }


                        $dayFolders = glob($monthFolder . '/*', GLOB_ONLYDIR);
                        natsort($dayFolders);

                        foreach ($dayFolders as $dayFolder) {
                            echo "<div class='dropdown days'><svg onclick='toggleFiles(this)' class='flr ' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                            echo "<h3 onclick='toggleFiles(this)'>Gün - " . basename($dayFolder) . "</h3>";


                            $firstDayJson = glob($dayFolder . '/??-??-????.json');
                            foreach ($firstDayJson as $firstJson) {
                                $jsonContent = file_get_contents($firstJson);
                                $dataDay = json_decode($jsonContent, true);

                                echo '
                                <div class="file" style="display:none;">
                                <div class="container">
                                
                                <div class="statics">
                                <div class="center"><b>' . pathinfo($firstJson, PATHINFO_FILENAME) . '</b> tarihine ait istatistikler</div>
                                <table>
                                <thead>
                                <tr>
                                <td>Yapılan Satış</td>
                                <td>Satılan Ürün Adedi</td>
                                <td>Satılan Ürün KG</td>
                                <td>Vergisiz Fiyat</td>
                                <td>Fiyat</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                <td>' . htmlspecialchars($dataDay['NumberOfSales']) . '</</td>
                                <td>' . htmlspecialchars($dataDay['NumberOfProductsNumber']) . '</td>
                                <td>' . htmlspecialchars(formatTR($dataDay['NumberOfProductsWeight'])) . '</td>
                                <td>' . htmlspecialchars(formatTR($dataDay['TaxFreePrice'])) . '₺</td>
                                <td>' . htmlspecialchars(formatTR($dataDay['FinalPrice'])) . '₺</td>
                                </tr>
                                </tbody>
                                </table>
                                </div>
                                </div>
                                </div>

                        ';

                            }


                            $jsonFiles = glob($dayFolder . '//??-??-????-?.json');
                            usort($jsonFiles, function ($a, $b) {
                                preg_match('/(\d+)\.json$/', $a, $matchesA);
                                preg_match('/(\d+)\.json$/', $b, $matchesB);

                                $numberA = isset($matchesA[1]) ? intval($matchesA[1]) : 0;
                                $numberB = isset($matchesB[1]) ? intval($matchesB[1]) : 0;

                                return $numberA - $numberB;
                            });

                            foreach ($jsonFiles as $jsonFile) {
                                $jsonContent = file_get_contents($jsonFile);
                                $jsonData = json_decode($jsonContent, true);



                                echo '
                                <div class="file" style="display:none;">
                                <div class="container">
                            
                            <div class="statics">
                            <div class="center"><b>' . pathinfo($jsonFile, PATHINFO_FILENAME) . '</b> satışına ait istatistikler</div>
                            <table class="fit">
                            <thead>
                            <tr>
                            <td>Satış Numarası</td>
                            <td>Tarih</td>
                            <td>Kasiyer</td>
                            <td>Vergisiz Tutarı</td>
                            <td>Tutarı</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            ';

                                foreach ($jsonData[0] as $value) {

                                    echo "<td>".htmlspecialchars($value)."</td>";

                                }


                                echo '
                            </tr>
                            </tbody>
                            </table>
                      
                   
                            <div>
                            <table class="center">
                            <thead>
                            <td>Barkod</td>
                            <td>Adı</td>
                            <td>Birim Fiyatı</td>
                            <td>Stok</td>
                            <td>KDV</td>
                            <td>ÖTV</td>
                            <td>Adet</td>
                            <td>Toplam Fiyat</td>
                            </thead>
                            <tbody>
                            ';

                                foreach ($jsonData[1] as $item) {
                                    echo "<tr>";
                                    foreach ($item as $value) {
                                        echo "<td>" . htmlspecialchars($value) . "</td>";
                                    }
                                    echo "</tr>";
                                }

                                echo '
                            </tbody>
                            </table>
                            </div>
                            </div>
                            </div>
                            </div>
                               ';



                            }



                            echo "</div>";
                        }

                        echo "</div>";
                    }

                    echo "</div></div><br>";
                }


            }

            $fatura_klasoru = '../faturalar';
            listFiles($fatura_klasoru);


            ?>

        </div>
        </div>
        <div class="col-t homid" id='notFound'>
        </div>
    </main>
</body>
<script>
    if (!(document.getElementById('statics'))) {
        document.getElementById('notFound').innerHTML = "<h2>Herhangi bir satış yapılmadığı için veri bulunmamakta!</h2>";
    } else {

    }
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function (event) {
            document.querySelectorAll('.pagination a button').forEach(button => {
                button.classList.remove('already');
            });
            event.target.classList.add('already');
        });
    });

    function toggleElements(elementId) {
        var elements = document.querySelectorAll(elementId);
        elements.forEach(function (element) {
            element.classList.toggle('show');
        });
    }

    function toggleFiles(element) {
        var files = element.parentElement.querySelectorAll('.file');
        files.forEach(function (file) {
            file.style.display = (file.style.display === 'block') ? 'none' : 'block';
        });
    }

    document.querySelectorAll('.dropdown').forEach(dropDownElement => {
        dropDownElement.addEventListener('click', function (event) {
            event.stopPropagation(); // Diğer tıklamaların etkisini engellemek için

            const clickedDropdown = this;
            const adjacentSVG = clickedDropdown.querySelector('svg');

            if (adjacentSVG) {
                adjacentSVG.classList.toggle('rotate');
            }
        });
    });



</script>
<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/format.js"></script>
<script src="../scripts/admin/product.js"></script>
<script src="../scripts/logout.js"></script>

</html>