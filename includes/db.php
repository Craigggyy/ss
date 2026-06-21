<?php

$conn = new mysqli(

    "localhost",

    "root",

    "",

    "dormease_db"

);

if($conn->connect_error)
{
    die("Connection Failed");
}

?>