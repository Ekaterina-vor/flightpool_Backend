<?php
header('Content-Type: application/json');

$host = 'localhost'; // Ваш хост базы данных
$db = 'flightpool'; // Имя вашей базы данных
$user = 'root'; // Имя пользователя базы данных
$pass = ''; // Пароль к базе данных

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['error' => ['code' => 500, 'message' => 'Ошибка подключения к базе данных']]));
}

$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => ['code' => 401, 'message' => 'Неавторизован']]);
    exit;
}

// Логика проверки токена здесь (например, проверка по сессии или JWT)

$data = json_decode(file_get_contents("php://input"), true);
$new_password = $data['password'] ?? '';

if (empty($new_password)) {
    http_response_code(422);
    echo json_encode(['error' => ['code' => 422, 'message' => 'Ошибка валидации', 'errors' => ['password' => ['Пароль обязателен']]]]);
    exit;
}

// Хэшируем новый пароль перед его сохранением
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Обновляем пароль в базе данных (предполагая, что ID пользователя получен из проверки токена)
$user_id = 1; // Замените на фактический ID пользователя после проверки
$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['message' => 'Пароль успешно обновлен']);
} else {
    http_response_code(500);
    echo json_encode(['error' => ['code' => 500, 'message' => 'Не удалось обновить пароль']]);
}

$stmt->close();
$conn->close();
?>