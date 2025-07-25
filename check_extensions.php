<?php
header('Content-Type: application/json');

// Verificar extensões PHP necessárias
$extensions_check = [
    'pdo' => extension_loaded('pdo'),
    'pdo_pgsql' => extension_loaded('pdo_pgsql'),
    'pgsql' => extension_loaded('pgsql'),
    'openssl' => extension_loaded('openssl'),
    'curl' => extension_loaded('curl'),
    'json' => extension_loaded('json')
];

// Listar todas as extensões carregadas
$loaded_extensions = get_loaded_extensions();
sort($loaded_extensions);

// Verificar drivers PDO disponíveis
$pdo_drivers = [];
if (extension_loaded('pdo')) {
    $pdo_drivers = PDO::getAvailableDrivers();
}

// Informações do PHP
$php_info = [
    'version' => phpversion(),
    'sapi' => php_sapi_name(),
    'os' => PHP_OS,
    'architecture' => php_uname('m')
];

// Configurações importantes
$important_configs = [
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_info' => $php_info,
    'extensions_check' => $extensions_check,
    'pdo_drivers' => $pdo_drivers,
    'loaded_extensions' => $loaded_extensions,
    'important_configs' => $important_configs,
    'recommendations' => []
];

// Adicionar recomendações
if (!$extensions_check['pdo']) {
    $response['recommendations'][] = 'CRÍTICO: Extensão PDO não está instalada';
}

if (!$extensions_check['pdo_pgsql']) {
    $response['recommendations'][] = 'CRÍTICO: Extensão PDO_PGSQL não está instalada - necessária para PostgreSQL';
}

if (!in_array('pgsql', $pdo_drivers)) {
    $response['recommendations'][] = 'Driver PostgreSQL não disponível no PDO';
}

if (empty($response['recommendations'])) {
    $response['recommendations'][] = 'Todas as extensões necessárias estão disponíveis';
}

// Status geral
$response['status'] = ($extensions_check['pdo'] && $extensions_check['pdo_pgsql']) ? 'ready' : 'missing_extensions';

echo json_encode($response, JSON_PRETTY_PRINT);
?>
