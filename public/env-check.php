<?php
header('Content-Type: application/json');

$envVars = [
    // App variables
    'APP_NAME' => $_ENV['APP_NAME'] ?? getenv('APP_NAME') ?: 'NOT SET',
    'APP_ENV' => $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'NOT SET', 
    'APP_KEY' => !empty($_ENV['APP_KEY'] ?? getenv('APP_KEY')) ? 'SET (hidden)' : 'NOT SET',
    'APP_DEBUG' => $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: 'NOT SET',
    'APP_URL' => $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'NOT SET',
    
    // Database variables
    'DB_CONNECTION' => $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'NOT SET',
    'DB_HOST' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'NOT SET',
    'DB_PORT' => $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 'NOT SET',
    'DB_DATABASE' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'NOT SET',
    'DB_USERNAME' => !empty($_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME')) ? 'SET (hidden)' : 'NOT SET',
    'DB_PASSWORD' => !empty($_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')) ? 'SET (hidden)' : 'NOT SET',
    
    // Railway MySQL variables (should be auto-provided)
    'MYSQLHOST' => $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?: 'NOT SET',
    'MYSQLPORT' => $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?: 'NOT SET',
    'MYSQLDATABASE' => $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?: 'NOT SET',
    'MYSQLUSER' => !empty($_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER')) ? 'SET (hidden)' : 'NOT SET',
    'MYSQLPASSWORD' => !empty($_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD')) ? 'SET (hidden)' : 'NOT SET',
];

echo json_encode([
    'status' => 'Environment Variables Check',
    'php_version' => phpversion(),
    'environment_variables' => $envVars,
    'all_env_count' => count($_ENV),
    'getenv_method_works' => getenv('PATH') ? 'YES' : 'NO',
    'superglobal_method_works' => isset($_ENV['PATH']) ? 'YES' : 'NO'
], JSON_PRETTY_PRINT);