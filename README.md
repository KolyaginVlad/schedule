# schedule
## Ссылка на сайт [schedule.rf.gd](https://schedule.rf.gd)
* **Хостинг**: Infinity Free
* **База данных**: MySQL
* **Библиотеки**: [digitalstars/DataBase](https://github.com/digitalstars/DataBase), [digitalstars/simple-api](https://github.com/digitalstars/simple-api) для PHP, [JQuery](https://jquery.com) для JS и [Bootstrap 5](https://bootstrap5.ru) для вёрстки
* **Шаблонизатор**: PHP
* **Формат запросов/ответов модуля api.php**: JSON
* **Модули:**
  * ***index.php*** - модуль, отвечающий за загрузку главной страницы.
    * Узнаёт текущий месяц, количество дней в месяце и на основе этого формирует таблицу, содержащую ссылки для перехода на модуль **day.php**.
    * При переходе передаётся дата в формате ***d-m-yyyy***, где **d** - день 1-31 без ведущего нуля, **m** - месяц 1-12 без ведущего нуля, **yyyy** - год в 4-ёх значном формате.
    * Формирует форму добавления новой записи в таблицу **schedule.** Для заполнения **select**, который позволяет выбрать предмет, подключается к базе данных, используя библиотеку [digitalstars/DataBase](https://github.com/digitalstars/DataBase), и отправляет следующую SQL команду базе данных: `SELECT title FROM subjects`. При получении данных создаёт `<option>` с соотвественными данными внутри.
  
  
  * ***script.js*** - модуль, отвечающий за отправку формы с модуля **index.php** на модуль **api.php**. Использует [JQuery](https://jquery.com).
    * При нажатии на кнопку ***Добавить*** срабатывает JS скрипт, проверяющий выбор предмета на корректность и отправляющий **POST** запрос модулю **api.php**, передавая в качестве параметров **module = "add"**, **subject**, **date** и **time**.
    * Дата меняет формат передачи на **yyyy-mm-dd**, где **dd** - день 01-31 **с** ведущим нулём, **mm** - месяц 01-12 **с** ведущим нулём, **yyyy** - год в 4-ёх значном формате, а время имеет формат **hh:mm**.
    * После этого он принимает ответ от модуля **api.php**, выводя его в соотвествующие поле, меняя цвет текста в соотвествии с содержимым.
    
    
  * ***day.php*** - модуль, отвечающий за загрузку страницы расписания на определённый день.
    * Получает от **index.php** дату в формате ***d-m-yyyy***, где **d** - день 1-31 без ведущего нуля, **m** - месяц 1-12 без ведущего нуля, **yyyy** - год в 4-ёх значном формате.
    * Используя полученные данные обращается к MySQL серверу с командой:
      ```sql
      SELECT DATE_FORMAT(sc.date, '%H:%i'), sb.title, GROUP_CONCAT(sp.title) 
            FROM schedule sc 
                JOIN subjects sb ON sc.id_subject = sb.id
                JOIN subjects_to_specialities sbtosp ON sbtosp.id_subject = sb.id
                JOIN specialities sp ON sbtosp.id_speciality = sp.id
            WHERE DATE_FORMAT(sc.date, '%e-%c-%Y')=?s
            GROUP BY DATE_FORMAT(sc.date, '%H:%i'), sb.title
      ``` 
      и заменителем (`?s`) из библиотеки [digitalstars/DataBase](https://github.com/digitalstars/DataBase): `[$_GET["date"]]`, который подставляет полученную от **index.php** дату. Это используется для защиты от SQL-инъекций. 
    
    * Из полученных данных формируется таблица.
    * Кнопка ***Назад к календарю*** перенаправит на модуль **index.php**.
    
    
  * ***api.php*** - модуль, отвечающий за общение с мобильным приложением. Так же он позволяет сайту добавить запись в таблицу **schedule.**
    * Подключается к БД с помощью библиотеки [digitalstars/DataBase](https://github.com/digitalstars/DataBase).
    * Отправляет ответы с помощью библиотеки [digitalstars/simple-api](https://github.com/digitalstars/simple-api).
    * Может обрабатывать как **get**, так и **post** запросы, однако обязательно должен присутствовать параметр **module**.
    * Если **module = "add"**, то этот модуль будет добавлять в таблицу **schedule** новую запись. Необходимые дополнительные параметры: **subject**, **date** и **time**. Дата должна быть формата **yyyy-mm-dd**, где **dd** - день 01-31 **с** ведущим нулём, **mm** - месяц 01-12 **с** ведущим нулём, **yyyy** - год в 4-ёх значном формате, а время иметь формат **hh:mm**.
    С начала модуль проверит не существует ли добавляемая запись в таблице, при существовании отправит ответ ***Такая запись уже существует***, иначе добавит запись в таблицу и отправит ***Успешно добавленно***:
    ```php
        $result = $db->query("SELECT * FROM schedule where `date` =?s and `id_subject` = (SELECT subjects.id FROM subjects WHERE subjects.title = ?s)", [$data['date']." ".$data['time'], $data["subject"]]);
        $an = $result->fetch();
        if ($an == null) {
            $db->query("INSERT INTO schedule (`date`, `id_subject`) VALUES (?s, (SELECT subjects.id FROM subjects WHERE subjects.title = ?s))",
                [$data['date']." ".$data['time'], $data["subject"]]);
            $api->answer["answer"] = "Успешно добавленно";
        } else $api->answer["answer"] = "Такая запись уже существует";
    ```
    
    * При неверном указании **module** вернёт ответ ***Ошибка***
* **Структура БД**:
  * ***specialities*** : id, code (шифр специальности), title (название специальности)
  * ***subjects*** : id, title (название предмета)
  * ***schedule*** : id, id_subject, date (дата проведения экзамена)
  * ***subjects_to_specialities*** : id, id_speciality, id_subject
