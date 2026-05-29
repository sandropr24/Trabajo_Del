<?php

$conexion = mysqli_connect(
    "127.0.0.1",
    "root",
    "",
    "TESTDELATEL1",
    3306
);

if(!$conexion){

    die("Error: " . mysqli_connect_error());

}

?>