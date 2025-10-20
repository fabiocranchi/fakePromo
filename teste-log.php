<?php
// Arquivo de teste para verificar se o logging está funcionando
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

echo "Teste de funcionamento do log.php\n\n";

// Verificar se o arquivo log.json existe
$logFile = __DIR__ . '/log.json';
echo "Arquivo log.json existe: " . (file_exists($logFile) ? "SIM" : "NÃO") . "\n";

// Verificar permissões de escrita
echo "Permissões de escrita: " . (is_writable($logFile) ? "SIM" : "NÃO") . "\n";

// Verificar se o diretório é gravável
echo "Diretório gravável: " . (is_writable(__DIR__) ? "SIM" : "NÃO") . "\n";

// Testar escrita no arquivo
$testData = [
    "indice" => 1,
    "sucesso" => true,
    "dataHora" => date('c'),
    "timestamp" => date('d/m/Y H:i:s'),
    "fase" => "Teste de Funcionamento"
];

$result = file_put_contents($logFile, json_encode([$testData], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Resultado da escrita: " . ($result !== false ? "SUCESSO" : "FALHOU") . "\n";
echo "Bytes escritos: " . $result . "\n";

// Ler o arquivo para confirmar
$content = file_get_contents($logFile);
echo "Conteúdo do arquivo:\n" . $content . "\n";

?>
