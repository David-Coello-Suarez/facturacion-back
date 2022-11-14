<?php

$image = $_GET['image'];

try {
    header("Content-Type: image/jpeg");

    if( !file_exists($image) ) throw new Exception("");

    readfile($image);
} catch (Exception $e) {
    header("Content-Type: image/jpeg");
    readfile("c:/firmas/logo-small.png");
}
