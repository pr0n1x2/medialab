<?php

// Подключаем массив $docs
require "docs.php";

// Данные для подключения к БД
$db_host = "localhost";
$db_port = "3306";
$db_database = "medialab";
$db_user = "root";
$db_pass = "";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

// Пробуем подключиться к базе данных
try {
    $db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_user, $db_pass, $options);
    $db->exec("set names utf8");
} catch (PDOException $e) {
    echo $e->getMessage();
}

/**
 * Получаем хеш строки методом sha1
 *
 * @param string $string Строка из которой нужно получить хеш
 *
 * @return string Хеш строки 40 символов
 */
function get_hash(&$string)
{
    return sha1($string);
}

/**
 * Получаем длину строки
 *
 * @param string $string Строка из которой нужно получить длину
 *
 * @return int Количество символов в строке
 */
function get_length(&$string)
{
    return mb_strlen($string);
}

/**
 * Получаем зжатую строку методом ZIP
 *
 * @param string $string Строка которую нужно сжать
 *
 * @return string Зжатая строка
 */
function get_zip(&$string)
{
    return gzcompress($string, 9);
}

/**
 * Получаем название партиции в таблице 'string' в которой находится
 * требуемая запись.
 * Партиция определяется по длине строки
 *
 * @param int $length Количество символов в строке
 *
 * @return string Название партиции
 */
function get_partition($length)
{
    if ($length <= 10240) {
        return 'p0';
    } elseif ($length <= 102400) {
        return 'p1';
    } elseif ($length <= 524288) {
        return 'p2';
    } elseif ($length <= 1048576) {
        return 'p3';
    } elseif ($length <= 5242880) {
        return 'p4';
    } else {
        return 'p5';
    }
}

/**
 * Получаем строку из БД если таковая существует
 *
 * @param string $string Строка которую требуется найти
 *
 * @return array или bool Если находим строку, тогда возвращаем array иначе false
 */
function check_string(&$string)
{
    global $db;

    // Генерируем хеш строки
    $hash = get_hash($string);
    // Определяем длину строки
    $length = get_length($string);
    // Определяем название партиции в которой храниться строка
    $partition = get_partition($length);

    $data = $db->prepare("SELECT * FROM strings PARTITION($partition) WHERE `hash` = :hash AND `length` = :length LIMIT 1");
    $data->execute([
        'hash' => $hash,
        'length' => $length
    ]);

    $result = $data->fetchAll();

    if ($result) {
        return $result[0];
    }

    return false;
}

/**
 * Вставляем строки в базу данных.
 * В случаем если строка дублируется, тогда в базу ее не вставляем, а
 * возвращаем количество дублированных строк.
 *
 * @param array $docs Массив строк, которые нужно вставить в БД
 *
 * @return int Количество дублированных строк
 */
function insert(&$docs)
{
    global $db;

    // Инициализируем счетчик дублированных строк
    $duplicate = 0;
    $docs_count = count($docs);

    if ($docs_count) {
        for ($i = 0; $i < $docs_count; $i++) {
            // Генерируем хеш строки
            $hash = get_hash($docs[$i]);
            // Определяем длину строки
            $length = get_length($docs[$i]);
            // Сжимаем строку
            // Этот метод можно опустить если нужна скорость!
            // Но если в базу будут записываться мегабайтные строки
            // тогда сжимая строки можно сэкономить место на диске.
            $zip = get_zip($docs[$i]);

            $data = $db->prepare("INSERT INTO `strings` VALUES (:hash, :length, :string)");
            $data->bindParam(':hash', $hash);
            $data->bindParam(':length', $length);
            $data->bindParam(':string', $zip);

            // Сохраням данные в БД
            // В случаем возникновения ошибки дублирования увеличиваем счетчик дубликатов
            try {
                $data->execute();
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $duplicate++;
                }
            }
        }
    }

    return $duplicate;
}

// Вставляем строки в БД
$duplicates = insert($docs);

echo "Количество дубликатов: $duplicates<br />";

// Проверяем есть ли такая строка в базе данные
$data = check_string($docs[2]);

if ($data) {
    echo "Строка из базы:<br />";
    echo gzuncompress($data['string'], $data['length']);
}
