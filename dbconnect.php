<?php 
$servername = "localhost";
$username   =  "root";
$password   =  "";
$database   =  "datasave";

//Conection establishment
$conn = mysqli_connect($servername,$username,$password,$database);

if(!$conn){
    die("Sorry failed to connect. " . mysqli_connect_error());
}
else{
   // echo "<h3>Connection Established</h3><br>";
}

// $serverName = "datasave.mssql.somee.com"; // Correct server name for SQL Server
// $username   = "uzair456_SQLLogin_1"; 
// $password   = "6ute49ffqh"; 
// $database   = "datasave"; 

// $connectionOptions = array(
//     "Database" => $database,
//     "Uid" => $username,
//     "PWD" => $password,
// );

// // Establishes the connection
// $conn = sqlsrv_connect($serverName, $connectionOptions);

// // Check connection
// if ($conn === false) {
//     die(print_r(sqlsrv_errors(), true));
// } else {
//     echo "<h3>Connection Established</h3><br>";
// }
?>