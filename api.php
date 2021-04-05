<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require('DataBase-1.0.6/autoload.php');
require ('simple-api-1.0.0/autoload.php');

use DigitalStars\DataBase\DB;
use DigitalStars\SimpleAPI;

header('Access-Control-Expose-Headers: Access-Control-Allow-Origin', false);
header('Access-Control-Allow-Origin: *', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
header('Access-Control-Allow-Credentials: true');

$db_type = 'mysql';
$db_name = 'epiz_28279468_dataBase';
$login = 'epiz_28279468';
$pass = '7G7VPBwFvvu32';
$ip = 'sql312.epizy.com';

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
    default:
        $api->answer["answer"] = "Ошибка";
        break;
}
