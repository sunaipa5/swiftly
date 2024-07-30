<?php
session_start();
if (!isset($_SESSION['swiftlyadmin']) || $_SESSION['swiftlyadmin'] != sha1(md5("9Ar7dasSTQhdayuıAS5uuy"))) {
    header("Location: index.php");
    exit;
}

require("../set.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['requestType']) && $_POST['requestType'] === 'addStaff') {
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $username = $_POST['username'];
            $password = hash('sha256', ($_POST['password']));
    
         
            $checkUser = $connect->prepare("SELECT COUNT(*) FROM admin WHERE username = :username");
            $checkUser->bindParam(':username', $username, PDO::PARAM_STR);
            $checkUser->execute();
            $userExists = $checkUser->fetchColumn();
    
            if ($userExists) {
                echo "Bu kullanıcı adı zaten mevcut!";
            } else {
                $save = $connect->prepare("INSERT INTO admin(username,password,name,surname) VALUES (:username,:password,:name,:surname)");
                $save->bindParam(':username', $username, PDO::PARAM_STR);
                $save->bindParam(':password', $password, PDO::PARAM_STR);
                $save->bindParam(':name', $name, PDO::PARAM_STR);
                $save->bindParam(':surname', $surname, PDO::PARAM_STR);
                $save->execute();
    
                if ($save->rowCount() > 0) {
                    echo "Kullanıcı eklendi.";
                } else {
                    echo "Kullanıcı eklenirken sorun oluştu!";
                }
            }
            exit;
        }
    
    

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'deleteStaff') {
        $username = $_POST['username'];
    
        $countAdmins = $connect->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    
        if ($countAdmins > 1) {
            $delete = $connect->prepare("DELETE FROM admin WHERE username=:username");
            $delete->bindParam(':username', $username, PDO::PARAM_STR);
            $delete->execute();
    
            if ($delete->rowCount() > 0) {
                echo "$username kaldırıldı";
            } else {
                echo "$username kullanıcısı bulunamadı!";
            }
        } else {
            echo "En az bir yönetici olmak zorunda";
        }
        exit;
    }
    

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'getStaff') {
        $username = $_POST['username'];
        $query = $connect->prepare("SELECT name,surname,username FROM admin WHERE username = :username");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC); // Tek bir satırı al

        if ($row) {
            $data = array(
                'name' => $row['name'],
                'surname' => $row['surname'],
                'username' => $row['username'],

            );

            echo json_encode($data);
        } else {
            echo "Yönetici bulunamadı!";
        }

        $connect = null;
        exit;


    }


    if (isset($_POST['requestType']) && $_POST['requestType'] === 'changeStaff') {
       
        $oldUsername = $_POST['oldusername'];

        if ($oldUsername) {
            if ($oldUsername !== $_POST['staffusername']) {
                $query = $connect->prepare("SELECT username FROM admin WHERE username = :username");
                $query->bindParam(':username', $_POST['staffusername'], PDO::PARAM_STR);
                $query->execute();
        
                $row = $query->fetch(PDO::FETCH_ASSOC);
        
                if ($row) {
                    echo "Aynı kullanıcı adına sahip birden fazla yönetici olamaz!";
                    exit;
                }
            }
        
            $data = [
                'name' => $_POST['staffname'] ?? null,
                'surname' => $_POST['staffsurname'] ?? '',
                'username' => $_POST['staffusername'] ?? null,
                'password' => $_POST['staffpassword'] ?? null,
            ];
        
            $sql = "UPDATE staff SET ";
        
            $updateFields = [];
            $bindParamTypes = [];
        
            foreach ($data as $key => $value) {
                if ($value !== null && $value !== '') {
                    $updateFields[] = "$key = :" . $key;
                    $bindParamTypes[$key] = PDO::PARAM_STR;
                }
            }
        
            if (!empty($updateFields)) {
                $sql .= implode(', ', $updateFields) . " WHERE username = :oldUsername";
        
                $stmt = $connect->prepare($sql);
        
                foreach ($data as $key => &$value) {
                    if (isset($bindParamTypes[$key]) && $value !== "" && $value !== null) {
                        if($key == 'password'){
                            echo "hash run";
                            $hpass = hash('sha256', $value);
                            $stmt->bindParam(":$key", $hpass, $bindParamTypes[$key]);
                        }else{
                            $stmt->bindParam(":$key", $value, $bindParamTypes[$key]);
                        }
                        
                    }
                }
        
                $stmt->bindParam(':oldUsername', $oldUsername, PDO::PARAM_STR);
        
                $stmt->execute();
                echo "Personel Güncellendi.";
                exit;
            } else {
                echo "Güncellenecek veri bulunamadı.";
                exit;
            }
        } else {
            echo "Geçersiz istek.";
            exit;
        }

    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Hesapları</title>
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
    </style>
</head>
<header>
    <?php require "navbar.php"; ?>
</header>

<body class="preload light">
    <main class="">
        <div class="center">
            <div>
                <div class="btn-c hmid hover" id="addStaffBtn">
                    <svg width="80" height="60">
                        <use xlink:href="../img/all.svg#staffAdd"></use>
                    </svg>
                    <h4>Yönetici ekle</h4>
                </div>
                <div class="btn-c hmid hover" id="deleteStaffBtn">
                    <svg width="80" height="60">
                        <use xlink:href="../img/all.svg#staffDelete"></use>
                    </svg>
                    <h4>Yönetici kaldır</h4>
                </div>
                <div class="btn-c hmid hover" id="changeStaff">
                    <svg width="80" height="60">
                        <use xlink:href="../img/all.svg#staffReplace"></use>
                    </svg>
                    <h4>Bilgi değiştir</h4>
                </div>
            </div>
        </div>
        <div class=" center">
            <div class="col col-md">

                <table class="center flx">
                    <thead>
                        <tr>
                            <th>Adı</th>
                            <th>Soyadı</th>
                            <th>Kullanıcı Adı</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

           

                        $itemsPerPage = 12;

                        $page = isset($_GET['page']) ? $_GET['page'] : 1;

                        $startFrom = ($page - 1) * $itemsPerPage;

                        $sql = "SELECT name,surname,username FROM admin ORDER BY name LIMIT $startFrom, $itemsPerPage";
                        $stmt = $connect->query($sql);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['surname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody></table>";

                        $totalStmt = $connect->query("SELECT COUNT(*) as total FROM admin");
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

                        $connect = null;
                        

                        ?>
                    </tbody>
                </table>
                <?php
                $connect = null;
                ?>
            </div>

        </div>
    </main>
</body>
<script src="../scripts/simpss-1.1.min.js"></script>
<script src="../scripts/popupMenu.js"></script>
<script src="../scripts/posty.js"></script>
<script src="../scripts/admin/manager.js"></script>
<script src="../scripts/logout.js"></script>

</html>