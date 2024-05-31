<?php
session_start();

// Функция для генерации случайной строки
function generateCaptchaText($length = 3) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Генерация текста CAPTCHA
$captchaText = generateCaptchaText();
$_SESSION['captcha'] = $captchaText;

// Создание изображения CAPTCHA
$image = imagecreatetruecolor(90, 38);

// Цвета
$bgColor = imagecolorallocate($image, 255, 255, 255); // белый
$textColor = imagecolorallocate($image, 0, 0, 0); // черный

// Заливка фона
imagefilledrectangle($image, 0, 0, 120, 40, $bgColor);

// Добавление текста на изображение
imagettftext($image, 20, 0, 10, 30, $textColor, __DIR__ . '/captcha.ttf', $captchaText);

// Вывод изображения
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
