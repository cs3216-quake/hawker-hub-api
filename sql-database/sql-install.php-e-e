<?php
$host     = "128.199.157.242";
$port     = 3307;
$socket   = "";
$user     = "root";
$password = "83GvYmK1C";
$dbname   = "hawker-hub";

$con = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());
echo "Building SQL Database from 'database.sql'";
run_sql_file($con,"database.sql");

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
			$success += (mysqli_query($con,$command)==false ? 0 : 1);
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

