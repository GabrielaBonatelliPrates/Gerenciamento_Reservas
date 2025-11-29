<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Falha na autenticação'
    ]);
    exit;
}

$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : '';
$conn = getConnection();

switch ($action) {
    case 'list':
        listReservations($conn);
        break;
        
    case 'search':
        searchReservations($conn);
        break;
        
    case 'create':
        createReservation($conn);
        break;
        
    case 'update':
        updateReservation($conn);
        break;
        
    case 'delete':
        deleteReservation($conn);
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação inválida'
        ]);
}

function listReservations($conn) {
    $sql = "SELECT * FROM Reservas ORDER BY DataRetirada ASC, id DESC";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao buscar reservas: ' . mysqli_error($conn)
        ]);
        return;
    }
    
    $reservas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reservas[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $reservas]);
}

function searchReservations($conn) {
    $query = isset($_GET['query']) ? sanitizeInput($_GET['query']) : '';
    
    if (empty($query)) {
        listReservations($conn);
        return;
    }
    
    $searchTerm = "%{$query}%";
    
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM Reservas 
        WHERE NomeCliente LIKE ? 
           OR Produto LIKE ? 
           OR DataRetirada LIKE ?
           OR DataReserva LIKE ?
        ORDER BY DataRetirada ASC, id DESC
    ");
    
    mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $reservas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reservas[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'success' => true,
        'data' => $reservas
    ]);
}

function createReservation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = ['NomeCliente', 'Produto', 'PrecoProduto', 'Quantidade', 'DataReserva'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Campo obrigatório ausente: {$field}"
            ]);
            return;
        }
    }
    
    if (!empty($data['Email']) && !filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        return;
    }
    
    if (!empty($data['Telefone']) && !validatePhone($data['Telefone'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Telefone inválido']);
        return;
    }
    
    if (!validateDate($data['DataReserva'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data inválida']);
        return;
    }
    
    if (!empty($data['DataRetirada']) && !validateDate($data['DataRetirada'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data inválida']);
        return;
    }
    
    if (!is_numeric($data['PrecoProduto']) || $data['PrecoProduto'] <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Preço inválido']);
        return;
    }
    
    if (!is_numeric($data['Quantidade']) || $data['Quantidade'] <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Quantidade inválida'
        ]);
        return;
    }
    
    $status = isset($data['Status']) ? $data['Status'] : 'reservado';
    if (!in_array($status, ['reservado', 'retirado'])) {
        $status = 'reservado';
    }
    
    $nomeCliente = sanitizeInput($data['NomeCliente']);
    $telefone = sanitizeInput($data['Telefone'] ?? '');
    $email = sanitizeInput($data['Email'] ?? '');
    $produto = sanitizeInput($data['Produto']);
    $precoProduto = $data['PrecoProduto'];
    $quantidade = $data['Quantidade'];
    $dataReserva = $data['DataReserva'];
    $dataRetirada = $data['DataRetirada'] ?? null;
    
    $stmt = mysqli_prepare($conn, "
        INSERT INTO Reservas (NomeCliente, Telefone, Email, Produto, PrecoProduto, Quantidade, DataReserva, DataRetirada, Status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    mysqli_stmt_bind_param($stmt, "ssssdisss", 
        $nomeCliente, $telefone, $email, $produto, 
        $precoProduto, $quantidade, $dataReserva, $dataRetirada, $status
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Reserva criada com sucesso', 'id' => mysqli_insert_id($conn)]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar reserva: ' . mysqli_error($conn)
        ]);
    }
    
    mysqli_stmt_close($stmt);
}

function updateReservation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID inválido'
        ]);
        return;
    }
    
    $stmt = mysqli_prepare($conn, "SELECT id FROM Reservas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Reserva não encontrada'
        ]);
        mysqli_stmt_close($stmt);
        return;
    }
    mysqli_stmt_close($stmt);
    
    $required = ['NomeCliente', 'Produto', 'PrecoProduto', 'Quantidade', 'DataReserva'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Campo obrigatório ausente: {$field}"
            ]);
            return;
        }
    }
    
    if (!empty($data['Email']) && !validateEmail($data['Email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email inválido'
        ]);
        return;
    }
    
    if (!empty($data['Telefone']) && !validatePhone($data['Telefone'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Telefone inválido'
        ]);
        return;
    }
    
    if (!validateDate($data['DataReserva'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Data de reserva inválida'
        ]);
        return;
    }
    
    if (!empty($data['DataRetirada']) && !validateDate($data['DataRetirada'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Data de retirada inválida'
        ]);
        return;
    }
    
    $status = isset($data['Status']) ? $data['Status'] : 'reservado';
    if (!in_array($status, ['reservado', 'retirado'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Status inválido'
        ]);
        return;
    }
    
    $id = $data['id'];
    $nomeCliente = sanitizeInput($data['NomeCliente']);
    $telefone = sanitizeInput($data['Telefone'] ?? '');
    $email = sanitizeInput($data['Email'] ?? '');
    $produto = sanitizeInput($data['Produto']);
    $precoProduto = $data['PrecoProduto'];
    $quantidade = $data['Quantidade'];
    $dataReserva = $data['DataReserva'];
    $dataRetirada = $data['DataRetirada'] ?? null;
    
    $stmt = mysqli_prepare($conn, "
        UPDATE Reservas 
        SET NomeCliente = ?,
            Telefone = ?,
            Email = ?,
            Produto = ?,
            PrecoProduto = ?,
            Quantidade = ?,
            DataReserva = ?,
            DataRetirada = ?,
            Status = ?
        WHERE id = ?
    ");
    
    mysqli_stmt_bind_param($stmt, "sssssisssi", 
        $nomeCliente, $telefone, $email, $produto, 
        $precoProduto, $quantidade, $dataReserva, $dataRetirada, $status, $id
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Reserva atualizada com sucesso'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar reserva: ' . mysqli_error($conn)
        ]);
    }
    
    mysqli_stmt_close($stmt);
}

function deleteReservation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID inválido'
        ]);
        return;
    }
    
    $stmt = mysqli_prepare($conn, "SELECT id FROM Reservas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Reserva não encontrada'
        ]);
        mysqli_stmt_close($stmt);
        return;
    }
    mysqli_stmt_close($stmt);
    
    $stmt = mysqli_prepare($conn, "DELETE FROM Reservas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Reserva excluída com sucesso'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao excluir reserva: ' . mysqli_error($conn)
        ]);
    }
    
    mysqli_stmt_close($stmt);
}
