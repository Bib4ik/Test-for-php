<?php

$connect = mysqli_connect("db", "root", "root", "SpyPerson");

if (!$connect){
    die('Error');
}

// Установка кодировки для корректного отображения русских символов
mysqli_set_charset($connect, 'utf8mb4');
