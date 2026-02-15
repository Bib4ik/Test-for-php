<?php
// Формируем массив тем
$themes = [
    'clash_royale' => [
        'id' => 1,
        'name' => 'Clash Royale',
        'characters' => []
    ]
];

// Получаем персонажей из таблицы ClashRoyal
$characters_query = mysqli_query($connect, "SELECT * FROM `ClashRoyal`");

if (!$characters_query) {
    die("Ошибка запроса: " . mysqli_error($connect));
}

while ($character = mysqli_fetch_assoc($characters_query)) {
    $themes['clash_royale']['characters'][] = [
        'image' => $character['photo'],
        'name' => $character['name']
    ];
}
?>