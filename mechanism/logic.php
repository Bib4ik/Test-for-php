<?php
/**
 * @var mysqli $connect - Подключение к БД (уже подключено в index.php)
 */

// Проверяем, было ли отправлено какое-либо действие
if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'start_game':
            $playerCount = intval($_POST['playerCount']);
            $deckId = intval($_POST['theme']); // Теперь получаем ID колоды из базы

            // 1. Запрос к базе: получаем все карты выбранной колоды
            $query = "SELECT card_name, photo FROM cards WHERE deck_id = $deckId";
            $cards_result = mysqli_query($connect, $query);

            // 2. Проверяем, что колода существует и в ней есть карты
            if ($playerCount >= 3 && $playerCount <= 10 && mysqli_num_rows($cards_result) > 0) {

                // Собираем все карты в массив
                $cards = [];
                while ($card = mysqli_fetch_assoc($cards_result)) {
                    $cards[] = [
                        'name' => $card['card_name'],
                        'image' => $card['photo']
                    ];
                }

                // Настраиваем сессию игры
                $_SESSION['totalPlayers'] = $playerCount;
                $_SESSION['currentPlayer'] = 1;
                $_SESSION['spyIndex'] = rand(1, $playerCount);
                $_SESSION['currentThemeId'] = $deckId;

                // Выбираем случайную карту (локацию) для этой партии
                $_SESSION['selectedCharacter'] = $cards[array_rand($cards)];
                $_SESSION['cardRevealed'] = false;
                $_SESSION['roles'] = [];

                // 3. Распределение ролей
                for ($i = 1; $i <= $playerCount; $i++) {
                    if ($i === $_SESSION['spyIndex']) {
                        $_SESSION['roles'][] = [
                            'player' => $i,
                            'role' => 'spy',
                            'character' => null
                        ];
                    } else {
                        $_SESSION['roles'][] = [
                            'player' => $i,
                            'role' => 'regular',
                            'character' => $_SESSION['selectedCharacter']
                        ];
                    }
                }

                $_SESSION['gameStarted'] = true;
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

        case 'reveal_card':
            $_SESSION['cardRevealed'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

        case 'next_player':
            // Проверка на существование данных в сессии перед переходом
            if (!isset($_SESSION['currentPlayer'])) $_SESSION['currentPlayer'] = 1;
            if (!isset($_SESSION['totalPlayers'])) $_SESSION['totalPlayers'] = 1;

            if ($_SESSION['currentPlayer'] < $_SESSION['totalPlayers']) {
                $_SESSION['currentPlayer']++;
                $_SESSION['cardRevealed'] = false;
            } else {
                $_SESSION['showResults'] = true;
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

        case 'reset_game':
            // Очищаем сессию, но сохраняем ID пользователя, если он залогинен
            $temp_user_id = $_SESSION['user_id'] ?? null;
            $temp_username = $_SESSION['username'] ?? null;

            session_destroy();
            session_start();

            if ($temp_user_id) {
                $_SESSION['user_id'] = $temp_user_id;
                $_SESSION['username'] = $temp_username;
            }

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
    }
}

