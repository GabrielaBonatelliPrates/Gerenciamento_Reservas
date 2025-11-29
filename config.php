<?php 

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'reservas_bd');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Login fictício apenas para a entrega do trabalho
define('LOGIN_USER', 'batllo@gmail.com');
define('LOGIN_PASS', '12345');

function getConnection() {
    static $conn = null;
    
    if ($conn === null) {

        $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        if (!$conn) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'message' => 'Erro de conexão com o banco de dados: ' . mysqli_connect_error()
            ]));
        }
        
        mysqli_set_charset($conn, DB_CHARSET);
    }
    
    return $conn;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 11;
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
