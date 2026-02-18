<?php
session_start();
require_once "connect-bd.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);

    // Проверяем: есть ли такой юзер и подходит ли пароль
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); // Уходим на главную
    } else {
        echo "Неверный логин или пароль.";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
</form>
