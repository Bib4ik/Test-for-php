<?php
session_start();

// 1. Подключаем только базовые настройки
require_once "connect-bd.php";

// 2. Игровая логика срабатывает ТОЛЬКО при нажатии на кнопки в игре
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once "mechanism/logic.php";
}

// 3. Определяем текущий экран (setup/game/results)
require_once "mechanism/screen-logic.php";

// 4. Проверяем авторизацию
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['username'] : 'Гость';

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

<header class="main-header">
    <div class="user-info">
        <span>Привет, <strong><?= htmlspecialchars($userName) ?></strong>!</span>
    </div>
    <div class="auth-buttons">
        <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="btn-auth">Войти</a>
            <a href="register.php" class="btn-auth">Регистрация</a>
        <?php else: ?>
            <a href="my-decks.php" class="btn-auth">Мои колоды</a>
            <a href="logout.php" class="btn-logout">Выйти</a>
        <?php endif; ?>
    </div>
</header>
<main class="game-wrapper">
<div class="container">
    <h1>🎮 Шпион</h1>

    <?php if (!$isLoggedIn): ?>
        <div class="alert">
            <p>Чтобы начать игру, создавать свои колоды и сохранять прогресс, пожалуйста, войдите в систему.</p>
            <div style="margin-top: 20px;">
                <a href="login.php" class="btn-auth" style="display:inline-block; padding: 10px 20px;">Войти</a>
                <a href="register.php" class="btn-auth" style="display:inline-block; padding: 10px 20px; background:#2ecc71;">Создать аккаунт</a>
            </div>
        </div>

    <?php else: ?>
        <?php if ($screen === 'setup'): ?>
            <form action="" method="POST" class="game-form">
                <input type="hidden" name="action" value="start_game">

                <label>Количество игроков:</label>
                <input type="number" name="playerCount" min="3" max="10" value="3" required>

                <label for="theme">Выберите колоду:</label>
                <select name="theme" id="theme" required>
                    <?php
                    $current_uid = $_SESSION['user_id'];
                    $decks_query = mysqli_query($connect, "SELECT id, deck_name FROM decks WHERE user_id = 1 OR user_id = $current_uid");
                    while ($deck = mysqli_fetch_assoc($decks_query)): ?>
                        <option value="<?= $deck['id'] ?>">
                            <?= htmlspecialchars($deck['deck_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Начать игру</button>
            </form>

            <div class="instruction">
                📝 Один игрок будет шпионом, остальные получат одинакового персонажа. Цель: вычислить шпиона!
            </div>

        <?php elseif ($screen === 'game'): ?>
            <div class="player-info">
                <div class="player-number">Игрок <?= $_SESSION['currentPlayer'] ?></div>
            </div>

            <?php if (!$_SESSION['cardRevealed']): ?>
                <div class="card card-hidden" onclick="document.getElementById('revealCardForm').submit();">
                    <div class="card-character-emoji">❓</div>
                    <div class="card-name">Нажмите, чтобы узнать роль</div>
                </div>
                <form method="POST" id="revealCardForm" style="display: none;">
                    <input type="hidden" name="action" value="reveal_card">
                </form>
            <?php else: ?>
                <?php
                $currentRole = $_SESSION['roles'][$_SESSION['currentPlayer'] - 1];
                $isSpy = ($currentRole['role'] === 'spy');
                ?>
                <div class="card revealed <?= $isSpy ? 'spy' : '' ?>">
                    <div class="card-character">
                        <?php if ($isSpy): ?>
                            <div class="card-character-emoji">🕵️‍♂️</div>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($currentRole['character']['image']) ?>" alt="role">
                        <?php endif; ?>
                    </div>
                    <div class="card-name"><?= $isSpy ? 'ШПИОН' : htmlspecialchars($currentRole['character']['name']) ?></div>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="next_player">
                    <button type="submit" class="next-button">
                        <?= ($_SESSION['currentPlayer'] < $_SESSION['totalPlayers']) ? 'Следующий игрок' : 'Результаты' ?>
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($screen === 'results'): ?>
            <h2>🎭 Время голосовать!</h2>
            <div class="card spy revealed" style="margin: 20px auto;">
                <div class="card-character-emoji" style="font-size: 80px;">🕵️‍♂️</div>
                <div class="card-name">Ищите шпиона среди вас!</div>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="reset_game">
                <button type="submit">Завершить и выйти</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
</div>
</main>

</body>
</html>
<?php mysqli_close($connect); ?>