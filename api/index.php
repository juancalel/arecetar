<?php
require_once "vendor/autoload.php";

use Parzibyte\BD;

header('Content-Type: application/json');

try {
    // Get the connection to the database
    $db = BD::obtener();

    // Execute the query
    $resultado = $db->query("SELECT * FROM a");

    // Check if the query was successful
    if (!$resultado) {
        throw new Exception("Query failed: " . $db->getError());
    }

    // Convert the result to an array
    $datos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }

    // Encode the data as JSON
    $respuesta = ["datos" => $datos];
    echo json_encode($respuesta);
} catch (Exception $e) {
    $error = $e->getMessage();
    $respuesta = ["error" => $error];
    echo json_encode($respuesta);
}
