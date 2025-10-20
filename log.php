<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Ler dados do POST
$input = file_get_contents('php://input');
$newEntry = json_decode($input, true);

if (!$newEntry) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Caminho para o arquivo log.json
$logFile = 'log.json';

// Ler dados existentes
$existingLogs = [];
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    if ($content) {
        $existingLogs = json_decode($content, true);
        if (!$existingLogs) {
            $existingLogs = [];
        }
    }
}

// Adicionar índice sequencial
$newEntry['indice'] = count($existingLogs) + 1;

// Adicionar nova entrada
$existingLogs[] = $newEntry;

// Escrever de volta no arquivo
$result = file_put_contents($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to write log file']);
    exit;
}

// Resposta de sucesso
echo json_encode([
    'success' => true,
    'message' => 'Log entry added successfully',
    'totalEntries' => count($existingLogs)
]);
?>
