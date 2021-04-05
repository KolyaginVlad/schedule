<!DOCTYPE html>
<html lang="ru">
<head>

    <?php
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
    $month = date("n");
    $year = date("Y"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Расписание</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
            crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script">

    </script>
</head>
<body>

<div class="row">
    <div class="text-center col-lg-12" style="background-color: greenyellow">
        <?php
        setlocale(LC_ALL, 'ru_RU.utf8');
        echo strftime("%B %Y", mktime("1", "1", "1", $month, "1", $year));
        ?>
    </div>
</div>
<table class="table table-bordered">
    <thead>
    <tr class="active">
        <th class='text-center'>Пн</th>
        <th class='text-center'>Вт</th>
        <th class='text-center'>Ср</th>
        <th class='text-center'>Чт</th>
        <th class='text-center'>Пт</th>
        <th class='text-center'>Сб</th>
        <th class='text-center'>Вс</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $emptyDays = date("w", mktime(1, 1, 1, date("n"), 1, date(Y)));
    if ($emptyDays == 0) $emptyDays = 6;
    else $emptyDays--;
    echo "<tr>";
    for ($i = 0; $i < $emptyDays; $i++) {
        echo "<th> </th>";
    }
    $i;
    for ($i = 0; $i < (int)date("t"); $i++) {
        if (($i + $emptyDays) % 7 == 0) {
            echo "</tr>
                  <tr>";
        }
        $day = $i + 1;
        $date = $day . "-" . $month . "-" . $year;
        echo "<th class='text-center'><a href='/day.php?date=$date'>$day</a></th></th>";
    }
    while (($i + $emptyDays) % 7 != 0) {
        echo "<th> </th>";
        $i++;
    }
    echo "</tr>";
    ?>
    </tbody>
</table>
<br><br><br><br><br>
<form class="col-md-4" action="">
    <div class="col-md-4 text-primary">Добавление новой записи в расписание</div>
    <select class="form-select" id="select">
        <option>Выберите предмет</option>
        <?php
        $sql = "SELECT title FROM subjects";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            echo "<option value='$row[0]'>$row[0]</option>";
        }
        ?>
    </select>
    <input type="datetime-local" class="form-control" id="date">
    <input type="button" class="btn btn-success" value="Добавить" id="submit">
</form>
<div id="ans" class="col-md-3"></div>
</body>
</html>