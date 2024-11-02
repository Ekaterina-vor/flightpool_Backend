<?php 
// Подключение к базе данных 
$pdo = new PDO ( 
    'mysql:host=localhost;dbname=flightpool;charset=utf8', 
    'root', 
    null, [
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]); 
// токен, который приходит
$token = getallheaders()['Authorization'];

    // Получаем document_number пользователя по токену
    $document_number = $pdo->query(
        "SELECT document_number FROM users WHERE api_token = '$token'"
    )->fetchColumn();

    // по номеру документа из таблицы passengers получить place_from, place_back
        $places = $pdo->query(
            "SELECT place_from, place_back FROM passengers WHERE document_number = '$document_number'"
        )->fetchAll()[0];

        echo json_encode($places);
    

?>