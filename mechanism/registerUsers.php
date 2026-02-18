<?php
require_once "connect-bd.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    // Хешируем пароль — это стандарт безопасности
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if (mysqli_query($connect, $sql)) {
        echo "Регистрация успешна! <a href='login.php'>Войти</a>";
    } else {
        echo "Ошибка: Пользователь уже существует.";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Зарегистрироваться</button>
</form>
