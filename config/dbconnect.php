<?php 
// Support for Supabase PostgreSQL via Environment Variables
$db_host = getenv('DB_HOST') ?: 'aws-1-ap-south-1.pooler.supabase.com'; 
$db_port = getenv('DB_PORT') ?: 6543;        
$db_name = getenv('DB_NAME') ?: 'postgres';
$db_user = getenv('DB_USER') ?: 'postgres.bpmpidpftytimisafyem';      
$db_pass = getenv('DB_PASS') ?: '';          

try {
    // Connect using PDO with the pgsql driver (SSL required for Supabase)
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;sslmode=require";
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