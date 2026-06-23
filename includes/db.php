<?php

$conn = new mysqli(

    "sql123.infinityfree.com",

    "if0_42254110",

    "2YOYuaIDtB8",

    "if0_42254110_dormease"

);

if($conn->connect_error)
{
    die("Connection Failed");
}

?>