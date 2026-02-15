<?php
/**
 * @var mysqli $connect - Подключение к БД из config/database.php
 * @var array $themes - Массив тем из includes/themes.php
 */
// Обработка действий игры
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'start_game':
            $playerCount = intval($_POST['playerCount']);
            $selectedTheme = isset($_POST['theme']) ? $_POST['theme'] : 'clash_royale';

            if ($playerCount >= 3 && $playerCount <= 10 && isset($themes[$selectedTheme])) {
                $_SESSION['totalPlayers'] = $playerCount;
                $_SESSION['currentPlayer'] = 1;
                $_SESSION['spyIndex'] = rand(1, $playerCount);
                $_SESSION['currentTheme'] = $selectedTheme;

                $characters = $themes[$selectedTheme]['characters'];
                $_SESSION['selectedCharacter'] = $characters[array_rand($characters)];
                $_SESSION['cardRevealed'] = false;
                $_SESSION['roles'] = [];

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
            if (!isset($_SESSION['currentPlayer'])) {
                $_SESSION['currentPlayer'] = 1;
            }
            if (!isset($_SESSION['totalPlayers'])) {
                $_SESSION['totalPlayers'] = 1;
            }

            if ($_SESSION['currentPlayer'] < $_SESSION['totalPlayers']) {
                $_SESSION['currentPlayer']++;
                $_SESSION['cardRevealed'] = false;
            } else {
                $_SESSION['showResults'] = true;
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

        case 'reset_game':
            session_destroy();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
    }
}
?>
