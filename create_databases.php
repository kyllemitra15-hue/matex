<?php
// create_databases.php
// Run this script once (via browser or CLI) to create the required databases and tables.

set_time_limit(30);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'db_init.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile";
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    echo "Failed to read SQL file.";
    exit(1);
}

// Connect to MySQL (use your XAMPP defaults)
$host = '127.0.0.1';
$user = 'root';
$pass = ''; // adjust if you have a MySQL password

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit(1);
}

// Split statements on semicolon followed by newline to avoid splitting inside routines.
$stmts = preg_split('/;\s*\n/', $sql);
$errors = [];
foreach ($stmts as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    if (!$mysqli->query($stmt)) {
        $errors[] = "Error executing statement: " . $mysqli->error . '\nStatement: ' . $stmt;
    }
}

if (empty($errors)) {
    echo "Databases and tables created successfully.\n";
} else {
    echo "Completed with errors:\n" . implode("\n\n", $errors);
}

$mysqli->close();

// Helpful note when run from browser
if (PHP_SAPI !== 'cli') {
    echo "<p>Done. You can now remove this file for security: <code>create_databases.php</code></p>";
}

?>
