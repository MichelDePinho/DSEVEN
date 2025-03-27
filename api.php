<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli('localhost', 'root', '', 'negocio_db');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Falha na conexão com o banco de dados']));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['nome'], $data['email'], $data['mensagem'])) {
        // Validação do email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['error' => 'Email inválido']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['nome'], $data['email'], $data['mensagem']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Mensagem enviada com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao salvar mensagem']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Dados inválidos']);
    }
} elseif ($method == 'GET') {
    $result = $conn->query("SELECT * FROM contatos ORDER BY data_envio DESC");
    $contatos = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($contatos);
}

$conn->close();
?>
