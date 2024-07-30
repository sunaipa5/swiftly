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
    <title>Satış Yönetimi</title>
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
            width: 15.5%;
        }

        table {
            width: 100%;
        }



        .column-dropdown {
            display: flex;
            flex-direction: column;
        }


        .months,
        .days {
            display: none;
            padding-left: 20px;
            cursor: pointer;
        }

        .year h3,
        .months h4,
        .days h5 {
            margin: 0;
        }

        .show {
            display: block;
        }

        .rotate {
            transition: transform 0.3s ease;
            transform: rotate(180deg);
        }

        .col {
            margin-right: 0;
            margin-left: 0;
        }
    </style>
</head>

<header>

    <?php require "navbar.php"; ?>
</header>

<script src="../scripts/simpss-1.1.min.js"></script>

<body class="preload light">

    <main>
        <div class="center">
            <h2>Kayıtlar</h2>
            <div class="col col-xl row over-x">
                <table class="center">
                    <thead>
                        <tr>
                            <th>Satış numarası</th>
                            <th>Fatura</th>
                            <th>Kasiyer</th>
                            <th>Tarih</th>
                            <th>Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $itemsPerPage = 10;
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $startFrom = ($page - 1) * $itemsPerPage;

                        $sql = "SELECT * FROM sale ORDER BY date DESC LIMIT $startFrom, $itemsPerPage";
                        $stmt = $connect->query($sql);

                        echo "<tbody>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['sale_id']) . "</td>";
                            echo "<td><a onclick=\"viewBill('" . htmlspecialchars($row['log_file']). ".pdf');\"><p class='fll'>Görüntüle&nbsp;</p><svg onclick='toggleFiles(this)' width='16' height='16'><use xlink:href='../img/all.svg#direct'></use></svg></a></td>";

                            $staffid = $row['staff_id'];
                            $squsr = $connect->prepare("SELECT name, surname FROM staff WHERE id = :id");
                            $squsr->bindParam(':id', $staffid);
                            $squsr->execute();
                            $result = $squsr->fetch(PDO::FETCH_ASSOC);

                            echo "<td>" . htmlspecialchars($result['name'] . " " . $result['surname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars(formatTR($row['price'])) . "TL</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";


                        $totalStmt = $connect->query("SELECT COUNT(*) as total FROM sale");
                        $totalRows = $totalStmt->fetch()['total'];
                        $totalPages = ceil($totalRows / $itemsPerPage);


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



                        $connect = null;
                        ?>

                    </tbody>
                </table>
            </div>
            <h2>Faturalar</h2>
            <?php
            function listFiles($baseFolder)
            {
                $yearFolders = glob($baseFolder . '/*', GLOB_ONLYDIR);
                rsort($yearFolders);

                $yearIndex = 0;

                foreach ($yearFolders as $yearFolder) {
                    $yearIndex++;

                    echo "<div class='col col-xl'>";
                    echo "<div onclick='toggleElements(\"#year$yearIndex .months\")' class='dropdown year' id='year$yearIndex'><svg class='flr' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                    echo "<h2 >Yıl - " . basename($yearFolder) . "</h2>";

                    $monthFolders = glob($yearFolder . '/*', GLOB_ONLYDIR);
                    natsort($monthFolders);
                    $monthIndex = 0;

                    foreach ($monthFolders as $monthFolder) {
                        $monthIndex++;

                        echo "<div onclick='toggleElements(\"#months$yearIndex$monthIndex .days\")' class='dropdown months' id='months$yearIndex$monthIndex'><svg  class='flr ' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                        echo "<h3>Ay - " . basename($monthFolder) . "</h3><hr>";

                        $dayFolders = glob($monthFolder . '/*', GLOB_ONLYDIR);
                        natsort($dayFolders);

                        foreach ($dayFolders as $dayFolder) {
                            echo "<div class='dropdown days'><svg onclick='toggleFiles(this)' class='flr ' width='16' height='16'><use xlink:href='../img/all.svg#dropDown'></use></svg>";
                            echo "<h3 onclick='toggleFiles(this)'>Gün - " . basename($dayFolder) . "</h3>";

                            $files = glob($dayFolder . '/*');
                            natsort($files);

                            foreach ($files as $file) {
                                if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                                    echo "<div class='file vex' style='display:none;' onclick=\"viewBill('" . $file . "');\"><p class='fll'>" . basename($file, '.pdf') . "&nbsp;</p> <svg class='' width='16' height='16'><use xlink:href='../img/all.svg#direct'></use></svg></div>";
                                }
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


    </main>
</body>
<script>
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

    function viewBill(txt) {


        var body = `
                <div>
                <iframe src='${txt}'>
                </iframe>
                <br>
                <div>
                <a href="${txt}" target="_blank"><button class="btb btn-md flr">Dosyaya git</button></a>
                </div>
                </div>
                `;
        popupIframe('Fatura Görüntüleme', body);
    }
</script>
<script src="../scripts/popupMenu.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/format.js"></script>
<script src="../scripts/admin/product.js"></script>
<script src="../scripts/logout.js"></script>

</html>