<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require('../DataBase-master/autoload.php');
require ('../simple-api-master/autoload.php');

use DigitalStars\DataBase\DB;
use DigitalStars\SimpleAPI;

header('Access-Control-Expose-Headers: Access-Control-Allow-Origin', false);
header('Access-Control-Allow-Origin: *', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
header('Access-Control-Allow-Credentials: true');

$db_type = 'mysql';
$db_name = 'learner12';
$login = 'learner12';
$pass = 'zl5QSpBiFwyCPidfA2pq';
$ip = 'l12.scripthub.ru';

$db = new DB("$db_type:host=$ip;dbname=$db_name", $login, $pass,
[PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
);
$api = new SimpleAPI();
switch ($api->module) {
    case 'add':
        $data = $api->params(['subject', 'date', 'time']);
        $result = $db->query("SELECT * FROM schedule where `date` =?s and `id_subject` = (SELECT subjects.id FROM subjects WHERE subjects.title = ?s)", [$data['date']." ".$data['time'], $data["subject"]]);
        $an = $result->fetch();
        if ($an == null) {
            $db->query("INSERT INTO schedule (`date`, `id_subject`) VALUES (?s, (SELECT subjects.id FROM subjects WHERE subjects.title = ?s))",
                [$data['date']." ".$data['time'], $data["subject"]]);
            $api->answer["answer"] = "Успешно добавленно";
        } else $api->answer["answer"] = "Такая запись уже существует";
        break;
    case 'getSubjects':
        $sql = "SELECT title FROM subjects";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $i = 0;
        $arrayOfSubjects = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $arrayOfSubjects[$i] = $row[0];
          $i++;
        }
        $api->answer["answer"] = "Отправлено";
        $api->answer["subjects"] = $arrayOfSubjects;
        break;
    case 'getSchedule':
        $data = $api->params(['date']);
        $sql = "SELECT DATE_FORMAT(sc.date, '%H:%i'), sb.title, GROUP_CONCAT(sp.title) 
            FROM schedule sc 
                JOIN subjects sb ON sc.id_subject = sb.id
                JOIN subjects_to_specialities sbtosp ON sbtosp.id_subject = sb.id
                JOIN specialities sp ON sbtosp.id_speciality = sp.id
            WHERE DATE_FORMAT(sc.date, '%e-%c-%Y')=?s
            GROUP BY DATE_FORMAT(sc.date, '%H:%i'), sb.title";
        $stmt = $db->prepare($sql, [$_GET["date"]]);
        $stmt->execute();
        $arrayOfTime = [];
        $arrayOfSubjects = [];
        $arrayOfSpecialities = [];
        $i = 0;
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
              $arrayOfTime[$i] =  $row[0];
              $arrayOfSubjects[$i] =  $row[1];
              $arrayOfSpecialities[$i] =  $row[2];
              $i++;
        }
        $api->answer["answer"] = "Отправлено";
        $api->answer["time"] = $arrayOfTime;
        $api->answer["subjects"] = $arrayOfSubjects;
        $api->answer["specialities"] = $arrayOfSpecialities;
        break;
    default:
        $api->answer["answer"] = "Ошибка";
        break;
}
