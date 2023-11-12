<?php
session_start();
$db_connect = 'sqlite:users.sqlite3';
$db = new PDO($db_connect);

// session_register();

$db->query('CREATE TABLE IF NOT EXISTS users (
	id INTEGER PRIMARY KEY,
	login TEXT NOT NULL,
	password_hash TEXT
);');

header('Content-type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$login = $_REQUEST['login'];
	$pass = $_REQUEST['pass'];
	$action = $_REQUEST['action'];
	if ($login and $pass) {
		if ($action == 'create') {
			if ($_SESSION['user_id']) {
				$login = $_SESSION['login'];
				$result = ["error" => "Вы уже вошли под логином $login"];
				header("HTTP/1.1 400 Bad Request");
				echo json_encode($result);
				return false;
			};
			$is_user_exists = $db->prepare('SELECT count() FROM users WHERE login = ?;');
			$is_user_exists->execute([$login]);
			$has_user = $is_user_exists->fetch(PDO::FETCH_ASSOC)['count()'];
			if ($has_user) {
				$result = ['error' => 'Такой пользователь уже зарегистрирован!'];
				header("HTTP/1.1 409 Conflict");
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
				return false;
			};
			$create_user = $db->prepare('INSERT INTO users (login, password_hash) VALUES (?, ?)');
			$create_user->execute([$login, $pass]);
			$user_id = $db->lastInsertId();
			$_SESSION["user_id"] = $user_id;
			$_SESSION["login"] = $login;
			$result = ['login' => $login];
			echo json_encode($result);
		} elseif ($action == 'login') {
			if ($_SESSION['user_id']) {
				$login = $_SESSION['login'];
				$result = ["error" => "Вы уже вошли под логином $login"];
				header("HTTP/1.1 400 Bad Request");
				echo json_encode($result);
				return false;
			};
			$check_user = $db->prepare('SELECT id FROM users WHERE login = ? AND password_hash = ?;');
			$check_user->execute([$login, $pass]);
			$user_id = $check_user->fetch(PDO::FETCH_ASSOC)["id"];
			if ($user_id) {
				$_SESSION["user_id"] = $user_id;
				$_SESSION["login"] = $login;
				$result = ["login" => $login];
				echo json_encode($result);
			} else {
				header("HTTP/1.1 401 Unauthorized");
				$result = ['error' => "Неверные данные для входа. Проверье логин или пароль."];
				echo json_encode($result);
			};
		}
	} elseif ($action == 'logout') {
		$user_id = $_SESSION["user_id"];
		if ($user_id) {
			unset($_SESSION['user_id']);
			unset($_SESSION['login']);
			$result = [True];
			echo json_encode($result);
		} else {
			$result = ['error' => "В этой сессии не выполнен вход"];
			header("HTTP/1.1 401 Unauthorized");
			echo json_encode($result);
		};
	} elseif ($action == 'get_login') {
		$login = $_SESSION['login'];
		if ($login) {
			$result = ['login' => $login];
			echo json_encode($result);
		} else {
			$result = ['error' => "В этой сессии не выполнен вход"];
			header("HTTP/1.1 401 Unauthorized");
			echo json_encode($result);
		};
	} else {
		$result = ['error' => "Both pass and login required."];
		header("HTTP/1.1 400 Bad Request");
		echo json_encode($result);
	}
} else {
	header("HTTP/1.1 405 Method Not Allowed");
	$result = ['error' => "Method is not allowed. Use the POST method."];
	echo json_encode($result);
}