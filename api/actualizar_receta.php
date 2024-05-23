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
  // Decode JSON data
  $receta = json_decode($_POST["receta"], true);
  if (!$receta) {
    throw new Exception("Invalid JSON data: " . json_last_error_msg());
  }

  // Handle photo upload
  $foto = null;
  if (isset($_FILES["foto"])) {
    $foto = $_FILES["foto"];
    if ($foto["error"] !== UPLOAD_ERR_OK) {
      throw new Exception("Photo upload error: " . $foto["error"]);
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
} catch (Exception $e) {
  $error = $e->getMessage();
  $respuesta = ["error" => $error];
}

echo json_encode($respuesta);
