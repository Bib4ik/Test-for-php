<?php
session_start();

// Подключаем конфигурацию БД
require_once "connect-bd.php";

// Загружаем темы
require_once "mechanism/Database.php";

// Обрабатываем действия игры
require_once "mechanism/logic.php";

// Определяем текущий экран
require_once "mechanism/screen-logic.php";

/**
 * @var mysqli $connect - Подключение к БД
 * @var array $themes - Массив тем
 * @var string $screen - Текущий экран (setup/game/results)
 * @var string $currentThemeName - Название текущей темы
 */
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шпион</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>🎮 Шпион</h1>

    <?php if ($screen === 'setup'): ?>
        <!-- Экран настройки -->
        <form method="POST">
            <input type="hidden" name="action" value="start_game">
            <input type="hidden" name="theme" value="clash_royale">

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

        <div class="card spy revealed" style="margin: 40px auto;">
            <div class="card-character">
                <div class="card-character-emoji" style="font-size: 120px;">🕵️‍♂️</div>
            </div>
            <div class="card-name" style="font-size: 28px; margin-top: 20px;">Удачи в поисках шпиона!</div>
        </div>

        <form method="POST" style="margin-top: 20px;">
            <input type="hidden" name="action" value="reset_game">
            <button type="submit">Новая игра</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
<?php
// Закрываем соединение с БД
mysqli_close($connect);
?>