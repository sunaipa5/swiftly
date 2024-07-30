<?php 

session_start();

if (isset($_GET['admin'])) {
    unset($_SESSION['swiftlyadmin']);
    header("Location: ".$_SERVER['PHP_SELF']);
} elseif (isset($_GET['staff'])) {
    unset($_SESSION['swiftlystaff']);
    header("Location: ".$_SERVER['PHP_SELF']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiftly</title>
    <link rel="icon" type="image/png" sizes="32x32" href="img/faviconx32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/faviconx16.png">
    <link rel="stylesheet" href="styles/simpss-1.1.min.css">
    <style>
        @media (min-width: 576px) {
            .conzone {
                position: relative;
                top: 50%;
                transform: translateY(+50%);
            }
        }
    </style>
</head>

<body class="light preload center mvp-80">
    <nav class="navbar">
        <div class="navl">
            <h1>Swiftly</h1>
        </div>
        <div class="navr">
            <button class="btnf" onclick="toggleTheme()">
                <svg id="light" width="16" height="16">
                    <use xlink:href="img/all.svg#themeToggleLight"></use>
                </svg>
                <svg id="dark" width="16" height="16">
                    <use xlink:href="img/all.svg#themeToggleDark"></use>
                </svg>
            </button>
        </div>

    </nav>
    <main>
        <br>


        <div class="center tx-cen flxh">
            <h2>Giriş Yöntemini seçiniz</h2>
            <br>
            <a href="staff">
                <div class="col hmid hover" style="width:200px; height:200px;">
                    <div class="scale flx hmid vex">
                        <h3>Personel</h3>
                        <svg width="60" height="60">
                            
                            <use xlink:href="img/all.svg#staff"></use>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="admin">
                <div class="col hmid hover" style="width:200px; height:200px;">
                    <div class="scale flx hmid vex">
                        <h3>Yönetici</h3>
                        <svg width="60" height="60">
                            <use xlink:href="img/all.svg#manager"></use>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

    </main>

</body>
<script src="scripts/simpss-1.1.min.js"></script>

</html>