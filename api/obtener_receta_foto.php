<?php
include_once "cors.php";

try {
    // Call Recetas::obtener() to get all recipes
    $recetas = Parzibyte\Recetas::obtenerRecetaconFoto();

    // Check if any recipes were found
    if ($recetas) {
        $respuesta = $recetas;
    } else {
        $respuesta = ["mensaje" => "No se encontraron recetas."];
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $respuesta = ["error" => $error];
}

// Set the appropriate content type header
header('Content-Type: application/json');

// Encode the response as JSON and send it to the client
echo json_encode($respuesta);
?>
