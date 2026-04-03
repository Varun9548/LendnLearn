<?php 
// Support for Supabase PostgreSQL via Environment Variables
$db_host = getenv('DB_HOST') ?: 'db.bpmpidpftytimisafyem.supabase.co'; 
$db_port = getenv('DB_PORT') ?: 5432;        
$db_name = getenv('DB_NAME') ?: 'postgres';
$db_user = getenv('DB_USER') ?: 'postgres';      
$db_pass = getenv('DB_PASS') ?: 'LendnLearn@2026!Secure';          

try {
    // Connect using PDO with the pgsql driver
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Unable to connect to PostgreSQL: " . $e->getMessage());
}

/*			##############################	TIME Diffrence US to INDIA		####################*/
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
/*			##############################	TIME Diffrence US to INDIA		####################*/
?>