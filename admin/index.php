<?php
session_start();
if (empty($_SESSION['swiftlyadmin'])) {

} else {
  unset($_SESSION['swiftlyadmin']);
}

include("../set.php");
include("../enc.php");


if (isset($_POST['requestType']) && $_POST['requestType'] === 'loginManager') {
  $usern = $_POST["username"];
  $passw = hash('sha256', $_POST["password"]);
  $sql = "SELECT id,username,password FROM admin WHERE username=:username AND password=:password";
  $query = $connect->prepare($sql);
  $query->bindParam(':username', $usern, PDO::PARAM_STR);
  $query->bindParam(':password', $passw, PDO::PARAM_STR);
  $query->execute();

  $row = $query->fetch(PDO::FETCH_ASSOC);
  if ($query->rowCount() > 0) {
    $ip = openssl_encrypt($_SERVER['REMOTE_ADDR'], $cpr, $enckey, $ops, $enc_iv);
    $userAgent = openssl_encrypt($_SERVER['HTTP_USER_AGENT'], $cpr, $enckey, $ops, $enc_iv);
    $date = openssl_encrypt(date("d-m-Y H:i"), $cpr, $enckey, $ops, $enc_iv);
    $userid = openssl_encrypt($row['id'], $cpr, $enckey, $ops, $enc_iv);
    $auth = openssl_encrypt("yönetici", $cpr, $enckey, $ops, $enc_iv);
    $encuser = openssl_encrypt($usern, $cpr, $enckey, $ops, $enc_iv);
    $save = $connect->prepare("INSERT INTO registry(ip,user,user_id,user_agent,date,authority) VALUES (:ip,:user,:userId,:userAgent,:date,:authority)");
    $save->bindParam(':ip', $ip, PDO::PARAM_STR);
    $save->bindParam(':user', $encuser, PDO::PARAM_STR);
    $save->bindParam(':userId', $userid, PDO::PARAM_STR);
    $save->bindParam(':userAgent', $userAgent, PDO::PARAM_STR);
    $save->bindParam(':date', $date, PDO::PARAM_STR);
    $save->bindParam(':authority', $auth, PDO::PARAM_STR);
    $save->execute();

    $connect = null;

    $_SESSION['swiftlyadmin'] = sha1(md5("9Ar7dasSTQhdayuıAS5uuy"));
    echo "document.location = 'main.php';";
  } else {
    echo "Kullanıcı bulunamadı!";
    exit;
  }
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yönetici Giriş</title>
  <link rel="icon" type="image/png" sizes="32x32" href="../img/faviconx32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../img/faviconx16.png">
  <link rel="stylesheet" href="../styles/simpss-1.1.min.css">
  <style>
    .col {
      margin-top: 20vh;
      position: relative;

    }

    .tbtn {
      background-color: transparent;
      border: none;
      outline: none;
    }

    .tbtn * {
      position: absolute;
      top: 0;
      right: 0;
      padding: 10px;
      margin: 0;

    }

    .backb {
      position: absolute;
      top: 0;
      left: 0;
      padding: 10px;
      margin: 0;
    }

    .formh {
      margin: 0;
    }
  </style>
</head>

<body id="body" class="preload center light">
  <main>
    <div class="col col-sm">
      <svg class="backb hover" width="20" height="20" onclick="window.location.href='../index.php'">
        <use xlink:href="../img/all.svg#back"></use>
      </svg>
      <div class="hmid fomrh">
        <button class=" tbtn" onclick="toggleTheme()">
          <svg id="light" width="16" height="16">
            <use xlink:href="../img/all.svg#themeToggleLight"></use>
          </svg>
          <svg id="dark" width="16" height="16">
            <use xlink:href="../img/all.svg#themeToggleDark"></use>
          </svg>
        </button>

        <svg width="40" height="40">
          <use xlink:href="../img/all.svg#manager"></use>
        </svg>
        <h2 class="fll">Yönetici</h2>
      </div>
      <form id="loginManager" onsubmit="return posty(event,'index.php','login','response')">
        <input class="fit inp-md" type="text" name="username" placeholder="Kullanıcı adı" require autofocus>
        <br>
        <input class="fit inp-md" type="password" name="password" placeholder="Parola" required autofocus>
        <br>
        <div class="fll response">
          <span id="response" class="fll"></span>
        </div>
        <button type="submit" name="submit" class="btn-lg btb flr">Giriş</button>
      </form>



    </div>

  </main>

</body>
<script src="../scripts/posty.js"></script>
<script src="../scripts/simpss-1.1.min.js"></script>

</html>