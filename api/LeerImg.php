<?php

$image = $_GET['image'];

try {
    header("Content-Type: image/jpeg");
    readfile($image);
} catch (Exception $e) {
    header("Content-Type: image/jpeg");
    readfile("c:/firmas/logo-small.png");
}
