<?php
/*

  ____          _____               _ _           _       
 |  _ \        |  __ \             (_) |         | |      
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___ 
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |        
        |___/                               |___/         
    
____________________________________
/ Si necesitas ayuda, contáctame en \
\ https://parzibyte.me               /
 ------------------------------------
        \   ^__^
         \  (oo)\_______
            (__)\       )\/\
                ||----w |
                ||     ||
Creado por Parzibyte (https://parzibyte.me).
------------------------------------------------------------------------------------------------
            | IMPORTANTE |
Si vas a borrar este encabezado, considera:
Seguirme: https://parzibyte.me/blog/sigueme/
Y compartir mi blog con tus amigos
También tengo canal de YouTube: https://www.youtube.com/channel/UCroP4BTWjfM0CkGB6AFUoBg?sub_confirmation=1
Twitter: https://twitter.com/parzibyte
Facebook: https://facebook.com/parzibyte.fanpage
Instagram: https://instagram.com/parzibyte
Hacer una donación vía PayPal: https://paypal.me/LuisCabreraBenito
------------------------------------------------------------------------------------------------
*/ ?>
<?php
include_once "cors.php";

try {
  // Retrieve recipe ID from URL query
  $idReceta = $_GET["id"];

  // Validate recipe ID
  if (!is_numeric($idReceta)) {
    throw new Exception("Recipe ID debe ser numerico.");
  }

  // Call Recetas::obtenerPorId() to get recipe
  $receta = Parzibyte\Recetas::obtenerPorId($idReceta);

  // Check if recipe was found
  if (!$receta) {
    $respuesta = ["error" => "Receta no encontrada."];
  } else {
    $respuesta = ["receta" => $receta];
  }
} catch (Exception $e) {
  $error = $e->getMessage();
  $respuesta = ["error" => $error];
}

echo json_encode($respuesta);