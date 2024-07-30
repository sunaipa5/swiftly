<?php

session_start();
if (!isset($_SESSION['swiftlyadmin']) || $_SESSION['swiftlyadmin'] != sha1(md5("9Ar7dasSTQhdayuıAS5uuy"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");
require("../enc.php");
require("../format.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Defteri</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        table {
            width: 100%;
        }
    </style>
</head>
<header>
    <?php require "navbar.php"; ?>
</header>

<body class="preload light">
    <main>
        <div class="center">
            <div class=" col col-xl over-x">
                <table class="center">
                    <thead>
                        <tr>
                            <th>IP Adresi</th>
                            <th>Kullanıcı ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Yetki</th>
                            <th>Giriş Zamanı</th>
                            <th>Giriş Ortamı</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        $itemsPerPage = 12;

                        $page = isset($_GET['page']) ? $_GET['page'] : 1;

                        $startFrom = ($page - 1) * $itemsPerPage;

                        $sql = "SELECT ip,user,user_id,user_agent,date,authority FROM registry ORDER BY date LIMIT $startFrom, $itemsPerPage";
                        $stmt = $connect->query($sql);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($decrypted = openssl_decrypt($row['ip'], $cpr, $enckey, $ops, $enc_iv)) . "</td>";
                            echo "<td>" . htmlspecialchars(openssl_decrypt($row['user_id'], $cpr, $enckey, $ops, $enc_iv)) . "</td>";
                            echo "<td>" . htmlspecialchars(openssl_decrypt($row['user'], $cpr, $enckey, $ops, $enc_iv)) . "</td>";
                            echo "<td>" . htmlspecialchars(openssl_decrypt($row['authority'], $cpr, $enckey, $ops, $enc_iv)) . "</td>";
                            echo "<td>" . htmlspecialchars(openssl_decrypt($row['date'], $cpr, $enckey, $ops, $enc_iv)) . "</td>";
                            echo "<td  style='font-size:12px;width:35%;'><textarea class='' rows='2.5' style='resize:none;width:90%;' readonly>" . htmlspecialchars(openssl_decrypt($row['user_agent'], $cpr, $enckey, $ops, $enc_iv)) . "</textarea></td>";
                            echo "</tr>";
                        }

                        echo "</tbody></table>";

                        $totalStmt = $connect->query("SELECT COUNT(*) as total FROM registry");
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

                        ?>


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