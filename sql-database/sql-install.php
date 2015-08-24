<?php
$host     = getenv('DOMAIN_IP');
$port     = 3307;
$socket   = "";
$user 		= getenv('MYSQL_ENV_MYSQL_USER');
$password = getenv('MYSQL_ENV_MYSQL_PASSWORD');
$dbname   = getenv('MYSQL_ENV_MYSQL_DATABASE');

$con = new mysqli($host, $user, $password, $dbname, $port)
	or die ('Could not connect to the database server' . mysqli_connect_error());
mysqli_set_charset($con,'utf8');
echo "Building SQL Database from 'database.sql'";
print_r(run_sql_file($con,"database.sql"));
print_r(run_sql_file($con,"seed.sql"));

function run_sql_file($con,$location){
	$commands = file_get_contents($location);

	$lines = explode("\n",$commands);
	$commands = '';
	foreach($lines as $line){
		$line = trim($line);
		if( $line && !startsWith($line,'--') ){
			$commands .= $line . "\n";
		}
	}

	$commands = explode(";", $commands);
	$total = $success = 0;
	foreach($commands as $command){
		if(trim($command)){
			if (mysqli_query($con,$command)) {
				$success++;
			} else {
				echo mysqli_error($con);
			}
			$total += 1;
		}
	}

	return array(
		"success" => $success,
		"total" => $total
	);
}

function startsWith($haystack, $needle){
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}
