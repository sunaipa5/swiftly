<?php
session_start();
if (!isset($_SESSION['swiftlystaff']) || $_SESSION['swiftlystaff'] != sha1(md5("nmHN738NDSSasdaU26d62Naj"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");

if (isset($_POST['requestType']) && $_POST['requestType'] === 'username') {
    $username = $_POST['username'];
    $query = $connect->prepare("SELECT id,name,surname FROM staff WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $data = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'surname' => $row['surname']

        );

        $_SESSION['staffid'] = htmlspecialchars($row['id']);
        $_SESSION['staffname'] = htmlspecialchars($row['name']);
        $_SESSION['staffsurname'] = htmlspecialchars($row['surname']);
        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Veri bulunamadı'));
    }

    $connect = null;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personel</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
    <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
    <link rel="stylesheet" href="../styles/main.css">
</head>

<body class="center mvp-80  dark">
    <?php require "navbar.php"; ?>
    <main>
        <div>
            <div class="btn-c col-sm">
                <div class="tx-cen" id="info">

                </div>
                <div id="userview">

                </div>
            </div>

            <br>
            <a href="sale.php">
                <div class="btn-c col-sm hmid" style="width:; height:200px;">
                    <div class="scale flx hmid vex">
                        <svg width="60" height="60">
                            <use xlink:href="../img/all.svg#sale"></use>
                        </svg>
                        <h3>Yeni Satış</h3>
                    </div>
                </div>
            </a>
        </div>

    </main>
</body>
<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/staff/main.js"></script>
<script src="../scripts/time.js"></script>

</html>