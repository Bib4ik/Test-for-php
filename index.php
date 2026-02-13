<?php
session_start();

// Персонажи Clash Royale
$characters = [
        ['image' => 'images/Golem.png', 'name' => 'Голем'],
        ['image' => 'images/mag.png', 'name' => 'Маг'],
        ['image' => 'images/king.png', 'name' => 'Принц'],
        ['image' => 'images/Banditka.png', 'name' => 'Бандитка'],
        ['image' => 'images/Megaknight.png', 'name' => 'Мегарыцарь'],
        ['image' => 'images/mini-peka.png', 'name' => 'Мини-Пека'],
        ['image' => 'images/witch.png', 'name' => 'Ведьма'],
        ['image' => 'images/varvaru.png', 'name' => 'Варвары'],
];

// Обработка действий
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'start_game':
            $playerCount = intval($_POST['playerCount']);
            if ($playerCount >= 3 && $playerCount <= 10) {
                $_SESSION['totalPlayers'] = $playerCount;
                $_SESSION['currentPlayer'] = 1;
                $_SESSION['spyIndex'] = rand(1, $playerCount);
                $_SESSION['selectedCharacter'] = $characters[array_rand($characters)];
                $_SESSION['cardRevealed'] = false;
                $_SESSION['roles'] = [];

                // Инициализируем роли
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
            // Проверяем существование переменных
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

// Определяем текущий экран
$screen = 'setup';
if (isset($_SESSION['gameStarted']) && $_SESSION['gameStarted']) {
    if (isset($_SESSION['showResults']) && $_SESSION['showResults']) {
        $screen = 'results';
    } else {
        $screen = 'game';
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шпион: Clash Royale Edition</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>🎮 Шпион</h1>
    <div class="subtitle">Clash Royale Edition</div>

    <?php if ($screen === 'setup'): ?>
        <!-- Экран настройки -->
        <form method="POST">
            <input type="hidden" name="action" value="start_game">
            <div class="input-group">
                <label for="playerCount">Количество игроков:</label>
                <input type="number" id="playerCount" name="playerCount" min="3" max="10" value="4" required>
            </div>
            <button type="submit">Начать игру</button>
        </form>

        <div class="instruction">
            📝 Один игрок будет шпионом, остальные получат одинакового персонажа. Цель: вычислить шпиона!
        </div>

    <?php elseif ($screen === 'game'): ?>
        <!-- Игровой экран -->
        <div class="player-info">
            <div class="player-number">
                Игрок <?php echo isset($_SESSION['currentPlayer']) ? $_SESSION['currentPlayer'] : 1; ?>
            </div>
        </div>

        <div class="instruction">
            🔒 Передайте устройство игроку и нажмите кнопку, чтобы узнать свою роль. Не показывайте другим!
        </div>

        <?php if (!$_SESSION['cardRevealed']): ?>
            <!-- Скрытая карта -->
            <div class="card card-hidden" onclick="document.getElementById('revealCardForm').submit();">
                <div class="card-character">
                    <div class="card-character-emoji">❓</div>
                </div>
                <div class="card-name">Нажмите на карту</div>
            </div>

            <form method="POST" id="revealCardForm" style="display: none;">
                <input type="hidden" name="action" value="reveal_card">
            </form>

            <button type="button" onclick="document.getElementById('revealCardForm').submit();">Открыть карту</button>

        <?php else: ?>
            <!-- Открытая карта -->
            <?php
            $currentRole = $_SESSION['roles'][$_SESSION['currentPlayer'] - 1];
            if ($currentRole['role'] === 'spy'):
                ?>
                <div class="card spy revealed" onclick="document.getElementById('nextPlayerForm').submit();">
                    <div class="card-character">
                        <div class="card-character-emoji">🕵️‍♂️</div>
                    </div>
                    <div class="card-name">ШПИОН</div>
                    <div class="card-role">Вычислите персонажа других игроков!</div>
                </div>
            <?php else: ?>
                <div class="card revealed" onclick="document.getElementById('nextPlayerForm').submit();">
                    <div class="card-character">
                        <img src="<?php echo htmlspecialchars($currentRole['character']['image']); ?>"
                             alt="<?php echo htmlspecialchars($currentRole['character']['name']); ?>">
                    </div>
                    <div class="card-name"><?php echo htmlspecialchars($currentRole['character']['name']); ?></div>
                    <div class="card-role">Вычислите шпиона!</div>
                </div>
            <?php endif; ?>

            <form method="POST" id="nextPlayerForm">
                <input type="hidden" name="action" value="next_player">
                <button type="submit" class="next-button">
                    <?php echo ($_SESSION['currentPlayer'] < $_SESSION['totalPlayers']) ? 'Следующий игрок' : 'Показать результаты'; ?>
                </button>
            </form>

            <form method="POST" id="resetGameForm">
                <input type="hidden" name="action" value="reset_game">
                <button type="submit" class="secondary">Новая игра</button>
            </form>

        <?php endif; ?>

    <?php elseif ($screen === 'results'): ?>
        <!-- Экран результатов -->
        <h2>🎭 Результаты игры</h2>

        <div class="results">
            <?php foreach ($_SESSION['roles'] as $role): ?>
                <div class="result-item <?php echo ($role['role'] === 'spy') ? 'spy-item' : ''; ?>">
                    <div>
                        <strong>Игрок <?php echo $role['player']; ?></strong><br>
                        <?php if ($role['role'] === 'spy'): ?>
                            <span style="color: #c92a2a;">🕵️‍♂️ ШПИОН</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($role['character']['name']); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($role['role'] === 'spy'): ?>
                            <span class="character-emoji">🎭</span>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($role['character']['image']); ?>"
                                 alt="<?php echo htmlspecialchars($role['character']['name']); ?>"
                                 class="character-image">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" style="margin-top: 20px;">
            <input type="hidden" name="action" value="reset_game">
            <button type="submit">Новая игра</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>