<?php

date_default_timezone_set('Asia/Shanghai');

$f3=require('app/lib/base.php');

$f3->config('app/cfg/setup.cfg');
$f3->config('app/cfg/routes.cfg');

# Check for AppFogs ENV variable
if( getenv("VCAP_SERVICES") ) {
    $json = getenv("VCAP_SERVICES");
} 
# Check for local file, placed by: af tunnel
else if( file_exists('af_vcap_services') ) {
    $json = file_get_contents('af_vcap_services');
} 
# No DB credentials
else {
    throw new Exception("No Database Information Available.", 1);
}

# Decode JSON and gather DB Info
$services_json = json_decode($json,true);
$mysql_config = $services_json["mysql-5.1"][0]["credentials"];

$username = $mysql_config["username"];
$password = $mysql_config["password"];
$hostname = $mysql_config["hostname"];
$port = $mysql_config["port"];
$db = $mysql_config["name"];

try{
	$dsn = 'mysql:host='.$hostname.';port='.$port.';dbname='.$db;
	$f3->set('DB', new DB\SQL($dsn, $username, $password));

}catch(PDOException $e){
	echo $e.message;
	echo "db error";
	exit;
}

$f3->run();

?>
