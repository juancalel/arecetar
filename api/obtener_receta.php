<?php
include_once "cors.php";

header('Content-Type: application/json');

try {
    // Retrieve recipe ID from URL query
    $idReceta = $_GET["id"];

    // Validate recipe ID
    if (!is_numeric($idReceta) || $idReceta <= 0) {
        throw new Exception("El ID de receta debe ser un número entero positivo.");
    }

    // Call Recetas::obtenerPorId() to get recipe
    $receta = Parzibyte\Recetas::obtenerPorId($idReceta);

    // Check if recipe was found
    if (!$receta) {
        $respuesta = ["error" => "Receta no encontrada para el ID proporcionado."];
    } else {
        $respuesta = $receta;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $respuesta = ["error" => $error];
}

echo json_encode($respuesta);
