<!DOCTYPE html>
<html lang="ru">
<head>
    <?
    require('DataBase-1.0.6/autoload.php');

    use DigitalStars\DataBase\DB;

    $db_type = 'mysql';
    $db_name = 'epiz_28279468_dataBase';
    $login = 'epiz_28279468';
    $pass = '7G7VPBwFvvu32';
    $ip = 'sql312.epizy.com';

    $db = new DB("$db_type:host=$ip;dbname=$db_name", $login, $pass,
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
    );
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><? echo $_GET["date"];?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="row text-center">
    <a href="/index.php" class="col-md-2 btn btn-info" style="color: white">Назад к
        календарю</a>
    <div class="text-primary col-md-4">Расписание экзаменов на <?
        $date = explode("-",  $_GET["date"]);
        echo $_GET["date"]
    ?></div>
</div>
<table class="table table-bordered text-center">
    <thead>
    <tr class="active" style="background-color: greenyellow">
        <th class='text-center'>Время экзамена</th>
        <th class='text-center'>Название предмет</th>
        <th class='text-center'>Список специальностей, абитуриенты которых должны прийти на экзамен</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT DATE_FORMAT(sc.date, '%H:%i'), sb.title, GROUP_CONCAT(sp.title) 
            FROM schedule sc 
                JOIN subjects sb ON sc.id_subject = sb.id
                JOIN subjects_to_specialities sbtosp ON sbtosp.id_subject = sb.id
                JOIN specialities sp ON sbtosp.id_speciality = sp.id
            WHERE DATE_FORMAT(sc.date, '%e-%c-%Y')=?s
            GROUP BY DATE_FORMAT(sc.date, '%H:%i'), sb.title";
    $stmt = $db->prepare($sql, [$_GET["date"]]);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        echo "<tr>
                <th>$row[0]</th>
                <th>$row[1]</th>
                <th>$row[2]</th>
              </tr>";
    }
    ?>
    </tbody>
</table>
</body>