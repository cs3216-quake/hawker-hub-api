<?php
$host     = getenv('DOMAIN_IP');
$port     = 3307;
$socket   = "";
$user 		= getenv('MYSQL_ENV_MYSQL_USER');
$password = getenv('MYSQL_ENV_MYSQL_PASSWORD');
$dbname   = getenv('MYSQL_ENV_MYSQL_DATABASE');

echo "Building SQL Database from 'database.sql'";

try{
    // db connection
	$mysqli = new mysqli($host, $user, $password, $dbname, $port);
	mysqli_set_charset($mysqli,'utf8');
	if($mysqli->connect_errno){
		throw new Exception("Connection Failed: [".$mysqli->connect_errno. "] : ".$mysqli->connect_error );
		exit();
	}

    // read file.
    // This file has multiple sql statements.
	$file_sql = file_get_contents("database.sql");
	$file_sql .= file_get_contents("seed.sql");

	if($file_sql == "null" || empty($file_sql) || strlen($file_sql) <= 0){
		throw new Exception("File is empty. I wont run it..");
	}

    //run the sql file contents through the mysqli's multi_query function.
    // here is where it gets complicated...
    // if the first query has errors, here is where you get it.
	$sqlFileResult = $mysqli->multi_query($file_sql);
    // this returns false only if there are errros on first sql statement, it doesn't care about the rest of the sql statements.

	$sqlCount = 1;
	if( $sqlFileResult == false ){
		throw new Exception("File:  , Query#[".$sqlCount."], [".$mysqli->errno."]: '".$mysqli->error."' }");
	}

    // so handle the errors on the subsequent statements like this.
    // while I have more results. This will start from the second sql statement. The first statement errors are thrown above on the $mysqli->multi_query("SQL"); line
	while($mysqli->more_results()){
		$sqlCount++;
        // load the next result set into mysqli's active buffer. if this fails the $mysqli->error, $mysqli->errno will have appropriate error info.
		if($mysqli->next_result() == false){
			throw new Exception("File:  , Query#[".$sqlCount."], Error No: [".$mysqli->errno."]: '".$mysqli->error."' }");
		}
	}
	echo $sqlCount . "Completed\n";
}
catch(Exception $e){
	echo $e->getMessage(). " <pre>".$e->getTraceAsString()."</pre>";
}
