<?php
header('Content-Type: text/html; charset=utf-8');
// Параметры подключения к базе данных
$host = 'localhost';
$dbName = 'zendy808_db';
$username = 'zendy808_db';
$password = 'Qwerty123+';

try {
    // Установка соединения с базой данных
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    
    // Установка режима обработки ошибок для PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Получение данных из формы авторизации
    $email = $_POST['email'];
    $password2 = $_POST['password'];
    
    // Подготовка SQL-запроса для выборки пользователя из базы данных
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->execute();
    
    // Получение результата запроса
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT password FROM users WHERE email = :email";
    $statement2 = $pdo->prepare($query);
    $statement2->bindParam(':email', $email);
    $statement2->execute();

    $db_password = $statement2->fetch(PDO::FETCH_ASSOC);
    
    // Проверка совпадения пароля
    if ($user && $password2 == $db_password["password"]) {
        // Авторизация успешна
        header('Location: index2.html');
        exit;
    } else {
        // Неправильное имя пользователя или пароль
        echo "Неправильное имя пользователя или пароль.";
    }
} catch (PDOException $e) {
    // Обработка ошибок подключения к базе данных
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?>