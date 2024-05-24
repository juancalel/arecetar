<?php
include_once "cors.php";

try {
    // Leer el ID de receta desde la entrada JSON
    $entradaJSON = file_get_contents("php://input");
    $idReceta = json_decode($entradaJSON, true);

    // Verificar si la decodificación JSON fue exitosa
    if ($idReceta === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Datos JSON inválidos: " . json_last_error_msg());
    }

    // Validar el ID de receta
    if (!isset($idReceta) || !is_numeric($idReceta)) {
        throw new Exception("El ID de receta debe ser un número.");
    }

    // Llamar a Recetas::eliminar() para eliminar la receta
    $respuesta = Parzibyte\Recetas::eliminar($idReceta);

    // Verificar si la eliminación fue exitosa
    if ($respuesta) {
        $respuesta = ["success" => true, "message" => "Receta eliminada correctamente"];
    } else {
        throw new Exception("Error al eliminar la receta.");
    }
} catch (Exception $e) {
    $respuesta = ["error" => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($respuesta);
