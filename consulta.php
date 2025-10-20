<?php
// Função para formatar data/hora
function formatarDataHora($isoString) {
    try {
        $data = new DateTime($isoString);
        $data->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        return $data->format('d/m/Y H:i:s');
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

// Função para formatar timestamp brasileiro
function formatarTimestamp($timestamp) {
    try {
        if (strpos($timestamp, '/') !== false) {
            // Formato já brasileiro
            return $timestamp;
        } else {
            // Converter se necessário
            $data = new DateTime($timestamp);
            $data->setTimezone(new DateTimeZone('America/Sao_Paulo'));
            return $data->format('d/m/Y H:i:s');
        }
    } catch (Exception $e) {
        return $timestamp ?: 'Data não disponível';
    }
}

// Carregar logs
$logs = [];
$totalRegistros = 0;
$erro = null;

try {
    if (file_exists('log.json')) {
        $conteudo = file_get_contents('log.json');
        if ($conteudo) {
            $logs = json_decode($conteudo, true);
            if ($logs === null) {
                $erro = 'Erro ao decodificar JSON';
                $logs = [];
            } else {
                $totalRegistros = count($logs);
                
                // Ordenar por data (mais recente primeiro)
                usort($logs, function($a, $b) {
                    $dataA = new DateTime($a['dataHora'] ?? $a['timestamp'] ?? '1970-01-01');
                    $dataB = new DateTime($b['dataHora'] ?? $b['timestamp'] ?? '1970-01-01');
                    return $dataB <=> $dataA;
                });
            }
        } else {
            $erro = 'Arquivo log.json está vazio';
        }
    } else {
        $erro = 'Arquivo log.json não encontrado';
    }
} catch (Exception $e) {
    $erro = 'Erro ao ler arquivo: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Logs - Pesquisa de Mercado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 min-h-screen p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-10 mb-6 animate-fadeIn">
            <div class="text-center mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl text-white">📊</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    Consulta de Logs
                </h1>
                <p class="text-lg text-gray-600">
                    Relatório de Participações na Pesquisa
                </p>
            </div>

            <!-- Estatísticas -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Total de Participações:</p>
                        <p class="text-4xl md:text-5xl font-bold"><?= $totalRegistros ?></p>
                    </div>
                    <span class="text-6xl opacity-90">📈</span>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <div>
                        <span class="font-semibold">Última Atualização:</span>
                        <span><?= date('d/m/Y H:i:s', time()) ?></span>
                    </div>
                    <a href="consulta.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all flex items-center">
                        <span class="mr-2">🔄</span>
                        Atualizar
                    </a>
                </div>
            </div>

            <?php if ($erro): ?>
                <!-- Mensagem de Erro -->
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 text-center">
                    <span class="text-4xl text-red-500 mb-4 inline-block">❌</span>
                    <p class="text-lg text-red-700 font-semibold mb-2">Erro ao carregar dados</p>
                    <p class="text-red-600"><?= htmlspecialchars($erro) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$erro): ?>
            <!-- Lista de Registros -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-10 animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="mr-3">📝</span>
                    Histórico de Participações
                </h2>
                
                <?php if (empty($logs)): ?>
                    <div class="text-center py-8">
                        <span class="text-4xl text-gray-400 mb-4 inline-block">📭</span>
                        <p class="text-lg text-gray-600">Nenhuma participação registrada ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($logs as $index => $log): ?>
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-5 border-2 border-gray-200 hover:border-blue-400 transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                            <?= $log['indice'] ?? ($index + 1) ?>
                                        </span>
                                        <div>
                                            <p class="font-bold text-gray-800">Participação #<?= $log['indice'] ?? ($index + 1) ?></p>
                                            <p class="text-sm text-gray-600"><?= $log['fase'] ?? 'Fase 3 - Dados Pessoais' ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center">
                                            <span class="text-green-500 mr-2"><?= ($log['sucesso'] ?? true) ? '✅' : '❌' ?></span>
                                            <span class="text-sm font-medium <?= ($log['sucesso'] ?? true) ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= ($log['sucesso'] ?? true) ? 'Sucesso' : 'Erro' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="font-semibold text-gray-700 mb-1">🕐 Horário (UTC-3):</p>
                                            <p class="text-gray-600 font-mono"><?= formatarTimestamp($log['timestamp'] ?? $log['dataHora']) ?></p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-700 mb-1">📅 Data ISO:</p>
                                            <p class="text-gray-600 font-mono text-xs"><?= htmlspecialchars($log['dataHora'] ?? 'N/A') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Rodapé -->
        <div class="text-center mt-6">
            <p class="text-white/80 text-sm">
                🔒 Sistema de Logs - Pesquisa de Mercado Acadêmica
            </p>
        </div>
    </div>
</body>
</html>
