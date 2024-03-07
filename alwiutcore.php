<?php

// Устанавливаем токен бота
define('BOT_TOKEN', '6995783951:AAHbKXf4Ssinu5LEdBCIRhroMU6aQPmBhtU');

// ID чатов администраторов (можно добавить несколько ID)
$admin_chat_ids = ['1755735783', '927996260'];

// Получаем обновления от Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Получаем текст сообщения
$message = $update['message']['text'];

// ID чата, откуда пришло сообщение
$chat_id = $update['message']['chat']['id'];

// Юзернейм пользователя, отправившего сообщение
$user_name = $update['message']['from']['username'];

// Проверяем команду
if ($message == '/start') {
    sendMessage($chat_id, 'Добро пожаловать!'); // Приветствие при старте
} else {
    // Если сообщение не команда, пересылаем его админам
    foreach ($admin_chat_ids as $admin_chat_id) {
        sendMessage($admin_chat_id, "Новое сообщение от @$user_name\nСообщение: $message");
    }
}

// Функция для отправки сообщения
function sendMessage($chat_id, $message) {
    $url = 'https://api.telegram.org/bot' . BOT_TOKEN . '/sendMessage';
    $params = [
        'chat_id' => $chat_id,
        'text' => $message
    ];
    $query = http_build_query($params);
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $query
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// Проверяем, является ли отправитель администратором (здесь пример)
if (in_array($chat_id, $admin_chat_ids)) {
    // Пример ответа администратора
    if ($message == 'Привет') {
        sendMessage($chat_id, 'Привет, администратор!');
    }
}
