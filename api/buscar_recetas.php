<?php
include_once "cors.php";

try {
    // Verificar si el parámetro "busqueda" está presente en la URL
    if (!isset($_GET["busqueda"])) {
        throw new Exception("No se recibió el término de búsqueda.");
    }

    // Recuperar y decodificar el término de búsqueda
    $busqueda = urldecode($_GET["busqueda"]);

    // Validar el término de búsqueda
    if (empty($busqueda)) {
        throw new Exception("El término de búsqueda no puede estar vacío.");
    }

    // Llamar a Recetas::buscar() para buscar las recetas
    $resultados = Parzibyte\Recetas::buscar($busqueda);

    // Verificar si se encontraron resultados
    if (empty($resultados)) {
        $respuesta = ["resultados" => [], "mensaje" => "No se encontraron resultados."];
    } else {
        $respuesta = ["resultados" => $resultados];
    }
} catch (Exception $e) {
    $respuesta = ["error" => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($respuesta);
