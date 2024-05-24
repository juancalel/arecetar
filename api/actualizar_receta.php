<?php
include_once "cors.php";

try {
    if (!isset($_POST["receta"])) {
        throw new Exception("No se recibió la receta en la solicitud.");
    }

    // Decode JSON data
    $receta = json_decode($_POST["receta"], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Datos JSON inválidos: " . json_last_error_msg());
    }

    // Verificar que todos los campos necesarios están presentes
    $requiredFields = ["nombre", "descripcion", "porciones", "ingredientes", "pasos", "id"];
    foreach ($requiredFields as $field) {
        if (!isset($receta[$field])) {
            throw new Exception("Falta el campo requerido: $field");
        }
    }

    // Handle photo upload
    $foto = null;
    if (isset($_FILES["foto"])) {
        $foto = $_FILES["foto"];
        if ($foto["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir la foto: " . $foto["error"]);
        }
    }

    // Update recipe
    $respuesta = Parzibyte\Recetas::actualizar(
        $receta["nombre"],
        $receta["descripcion"],
        $receta["porciones"],
        $receta["ingredientes"],
        $receta["pasos"],
        $foto,
        $receta["id"]
    );

    if ($respuesta) {
        $respuesta = ["success" => true, "message" => "Receta actualizada correctamente"];
    } else {
        throw new Exception("Error al actualizar la receta.");
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $respuesta = ["error" => $error];
}

header('Content-Type: application/json');
echo json_encode($respuesta);
