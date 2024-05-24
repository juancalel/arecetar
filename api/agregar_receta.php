<?php
include_once "cors.php";

try {
    // Verificar si se recibió la receta en la solicitud
    if (!isset($_POST["receta"])) {
        throw new Exception("No se recibió la receta en la solicitud.");
    }

    // Decodificar los datos JSON
    $receta = json_decode($_POST["receta"], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Datos JSON inválidos: " . json_last_error_msg());
    }

    // Validar los datos de la receta
    if (empty($receta["nombre"])) {
        throw new Exception("El nombre de la receta no puede estar vacío.");
    }
    if (empty($receta["descripcion"])) {
        throw new Exception("La descripción de la receta no puede estar vacía.");
    }
    if (!is_numeric($receta["porciones"])) {
        throw new Exception("Las porciones de la receta deben ser un número.");
    }
    if (!isset($receta["ingredientes"]) || !is_array($receta["ingredientes"])) {
        throw new Exception("Los ingredientes de la receta deben ser un arreglo.");
    }
    if (!isset($receta["pasos"]) || !is_array($receta["pasos"])) {
        throw new Exception("Los pasos de la receta deben ser un arreglo.");
    }

    // Manejar la subida de fotos
    $foto = null;
    if (isset($_FILES["foto"])) {
        $foto = $_FILES["foto"];
        if ($foto["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir la foto: " . $foto["error"]);
        }
    }

    // Agregar la receta
    $respuesta = Parzibyte\Recetas::agregar(
        $receta["nombre"],
        $receta["descripcion"],
        $receta["porciones"],
        $receta["ingredientes"],
        $receta["pasos"],
        $foto
    );

    // Verificar si la receta se agregó correctamente
    if ($respuesta) {
        $respuesta = ["success" => true, "message" => "Receta agregada correctamente"];
    } else {
        throw new Exception("Error al agregar la receta.");
    }
} catch (Exception $e) {
    // Capturar y manejar las excepciones
    $respuesta = ["error" => $e->getMessage()];
}

// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');
// Enviar la respuesta JSON al cliente
echo json_encode($respuesta);
?>
