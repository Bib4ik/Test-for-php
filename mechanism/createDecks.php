<?php
// Мы не пишем тут require "connect-bd.php", так как он уже подключен в index.php

$userId = $_SESSION['user_id'] ?? 1; // Берем ID из сессии или 1 для теста
$newDeckName = "Моя новая колода";

// 1. Считаем количество колод юзера через mysqli
$count_query = "SELECT COUNT(*) as total FROM decks WHERE user_id = $userId";
$count_result = mysqli_query($connect, $count_query);
$row = mysqli_fetch_assoc($count_result);
$deckCount = $row['total'];

// 2. Проверяем лимит
if ($deckCount >= 5) {
    echo "Ошибка: У вас уже есть $deckCount колод. Лимит — 5!";
} else {
    // 3. Создаем новую колоду
    // Используем mysqli_real_escape_string для защиты, если имя вводит пользователь
    $safeName = mysqli_real_escape_string($connect, $newDeckName);
    $insert_query = "INSERT INTO decks (user_id, deck_name) VALUES ($userId, '$safeName')";

    if (mysqli_query($connect, $insert_query)) {
        echo "Успех! Колода создана.";
    } else {
        echo "Ошибка базы данных: " . mysqli_error($connect);
    }
}