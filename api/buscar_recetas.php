<?php
 ?>
<?php

include_once "cors.php";

try {
  // Retrieve search term from URL query
  $busqueda = urldecode($_GET["busqueda"]);

  // Validate search term
  if (empty($busqueda)) {
    throw new Exception("Search term cannot be empty.");
  }

  // Call Recetas::buscar() to search recipes
  $resultados = Parzibyte\Recetas::buscar($busqueda);

  // Check if any results were found
  if (!$resultados) {
    $respuesta = ["resultados" => [], "mensaje" => "No se encontraron resultados."];
  } else {
    $respuesta = ["resultados" => $resultados];
  }
} catch (Exception $e) {
  $error = $e->getMessage();
  $respuesta = ["error" => $error];
}

echo json_encode($respuesta);
