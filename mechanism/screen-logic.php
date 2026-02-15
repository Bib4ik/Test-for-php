<?php
/**
 * @var array $themes - Массив тем из includes/themes.php
 */
// Определяем текущий экран
$screen = 'setup';
if (isset($_SESSION['gameStarted']) && $_SESSION['gameStarted']) {
    if (isset($_SESSION['showResults']) && $_SESSION['showResults']) {
        $screen = 'results';
    } else {
        $screen = 'game';
    }
}

// Получаем название текущей темы
$currentThemeName = 'Шпион';
if (isset($_SESSION['currentTheme']) && isset($themes[$_SESSION['currentTheme']])) {
    $currentThemeName = $themes[$_SESSION['currentTheme']]['name'];
}
?>
