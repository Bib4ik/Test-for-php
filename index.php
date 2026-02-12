<?php
session_start();

// Персонажи Clash Royale
$characters = [
        ['emoji' => '👑', 'name' => 'Король'],
        ['emoji' => '🧙‍♂️', 'name' => 'Волшебник'],
        ['emoji' => '⚔️', 'name' => 'Рыцарь'],
        ['emoji' => '🏹', 'name' => 'Лучница'],
        ['emoji' => '🐉', 'name' => 'Дракон'],
        ['emoji' => '💀', 'name' => 'Скелеты'],
        ['emoji' => '🛡️', 'name' => 'Гигант'],
        ['emoji' => '🧊', 'name' => 'Ледяной Маг'],
        ['emoji' => '⚡', 'name' => 'Электро Волшебник'],
        ['emoji' => '👹', 'name' => 'Хог Райдер'],
        ['emoji' => '🦇', 'name' => 'Летучие мыши'],
        ['emoji' => '🏰', 'name' => 'Принцесса'],
        ['emoji' => '💣', 'name' => 'Бомбардир'],
        ['emoji' => '🔥', 'name' => 'Огненный Дух'],
        ['emoji' => '🌪️', 'name' => 'Торнадо']
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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шпион: Clash Royale Edition</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #667eea;
            margin-bottom: 10px;
            font-size: 32px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .card.spy {
            background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
        }

        .card-hidden {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-character {
            font-size: 72px;
            margin-bottom: 20px;
        }

        .card-name {
            font-size: 28px;
            color: white;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .card-role {
            font-size: 18px;
            color: rgba(255,255,255,0.9);
            margin-top: 10px;
        }

        .player-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .player-number {
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
        }

        .instruction {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
            text-align: center;
        }

        .next-button {
            margin-top: 20px;
        }

        .results {
            margin-top: 20px;
        }

        .result-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-item.spy-item {
            background: #ffe0e0;
            border: 2px solid #ff6b6b;
        }

        .character-emoji {
            font-size: 32px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #667eea;
        }
    </style>
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
            <div class="player-number">Игрок <?php echo $_SESSION['currentPlayer']; ?></div>
        </div>

        <div class="instruction">
            🔒 Передайте устройство игроку и нажмите кнопку, чтобы узнать свою роль. Не показывайте другим!
        </div>

        <?php if (!$_SESSION['cardRevealed']): ?>
            <!-- Скрытая карта -->
            <div class="card card-hidden">
                <div class="card-character">❓</div>
                <div class="card-name">Нажмите кнопку ниже</div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="reveal_card">
                <button type="submit">Открыть карту</button>
            </form>

        <?php else: ?>
            <!-- Открытая карта -->
            <?php
            $currentRole = $_SESSION['roles'][$_SESSION['currentPlayer'] - 1];
            if ($currentRole['role'] === 'spy'):
                ?>
                <div class="card spy">
                    <div class="card-character">🕵️‍♂️</div>
                    <div class="card-name">ШПИОН</div>
                    <div class="card-role">Вычислите персонажа других игроков!</div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-character"><?php echo $currentRole['character']['emoji']; ?></div>
                    <div class="card-name"><?php echo $currentRole['character']['name']; ?></div>
                    <div class="card-role">Вычислите шпиона!</div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="next_player">
                <button type="submit" class="next-button">
                    <?php echo ($_SESSION['currentPlayer'] < $_SESSION['totalPlayers']) ? 'Следующий игрок' : 'Показать результаты'; ?>
                </button>
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
                            <?php echo $role['character']['name']; ?>
                        <?php endif; ?>
                    </div>
                    <div class="character-emoji">
                        <?php echo ($role['role'] === 'spy') ? '🎭' : $role['character']['emoji']; ?>
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