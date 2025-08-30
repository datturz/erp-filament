<?php
// Test Railway deployment
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$response = [
    'status' => 'Railway Debug Test',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'environment_check' => []
];

// Check critical environment variables
$requiredVars = [
    'APP_KEY' => 'Application encryption key',
    'APP_ENV' => 'Application environment',
    'DB_HOST' => 'Database host (from MySQL service)',
    'DB_PORT' => 'Database port',
    'DB_DATABASE' => 'Database name',
    'DB_USERNAME' => 'Database username',
    'DB_PASSWORD' => 'Database password',
    'MYSQLHOST' => 'Railway MySQL host',
    'MYSQLPORT' => 'Railway MySQL port',
    'MYSQLDATABASE' => 'Railway MySQL database',
    'MYSQLUSER' => 'Railway MySQL user',
    'MYSQLPASSWORD' => 'Railway MySQL password'
];

foreach ($requiredVars as $var => $description) {
    $value = $_ENV[$var] ?? getenv($var) ?? null;
    if ($value) {
        // Hide sensitive values
        if (strpos($var, 'PASSWORD') !== false || $var === 'APP_KEY') {
            $response['environment_check'][$var] = 'SET (hidden)';
        } else {
            $response['environment_check'][$var] = $value;
        }
    } else {
        $response['environment_check'][$var] = 'NOT SET';
    }
}

// Test database connection with both variable sets
$response['database_tests'] = [];

// Test with DB_ variables
if (!empty($_ENV['DB_HOST']) || !empty(getenv('DB_HOST'))) {
    $dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $dbPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
    $dbName = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $dbUser = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
    $dbPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
    
    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query('SELECT 1');
        $response['database_tests']['DB_variables'] = 'SUCCESS - Connected!';
    } catch (Exception $e) {
        $response['database_tests']['DB_variables'] = 'FAILED: ' . $e->getMessage();
    }
} else {
    $response['database_tests']['DB_variables'] = 'Variables not set';
}

// Test with MYSQL variables
if (!empty($_ENV['MYSQLHOST']) || !empty(getenv('MYSQLHOST'))) {
    $dbHost = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST');
    $dbPort = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
    $dbName = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE');
    $dbUser = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER');
    $dbPass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');
    
    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query('SELECT 1');
        $response['database_tests']['MYSQL_variables'] = 'SUCCESS - Connected!';
    } catch (Exception $e) {
        $response['database_tests']['MYSQL_variables'] = 'FAILED: ' . $e->getMessage();
    }
} else {
    $response['database_tests']['MYSQL_variables'] = 'Variables not set';
}

// Check Laravel files
$response['laravel_check'] = [
    'vendor_exists' => file_exists('../vendor') ? 'YES' : 'NO',
    'bootstrap_exists' => file_exists('../bootstrap/app.php') ? 'YES' : 'NO',
    'env_file_exists' => file_exists('../.env') ? 'YES' : 'NO',
    'storage_writable' => is_writable('../storage') ? 'YES' : 'NO'
];

// Output
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);