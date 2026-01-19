<?php
$conn = new mysqli("localhost", "root", "", "football_db");

if ($conn->connect_error) {
    die("Erreur connexion BDD");
}

$conn->set_charset("utf8mb4");
