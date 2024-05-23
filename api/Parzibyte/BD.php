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

namespace Parzibyte;

use PDO;

class BD
{
  static function obtener()
  {
    $password = Utiles::obtenerVariableDelEntorno("POSTGRES_PASSWORD");
    $user = Utiles::obtenerVariableDelEntorno("POSTGRES_USER");
    $dbName = Utiles::obtenerVariableDelEntorno("POSTGRES_DATABASE_NAME");
    $port = Utiles::obtenerVariableDelEntorno("POSTGRES_PORT", 5432);

    try {
      $database = new PDO('pgsql:host=localhost;port=' . $port . ';dbname=' . $dbName, $user, $password);
      $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
      $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      return $database;
    } catch (PDOException $e) {
      throw new Exception("Error al conectar a la base de datos: " . $e->getMessage());
    }
  }
}


// namespace Parzibyte;

// use PDO;
// use Exception;
// use PDOException;

// class BD
// {
//     static function obtener()
//     {
//         $password = Utiles::obtenerVariableDelEntorno("POSTGRES_PASSWORD");
//         $user = Utiles::obtenerVariableDelEntorno("POSTGRES_USER");
//         $dbName = Utiles::obtenerVariableDelEntorno("POSTGRES_DATABASE_NAME");
//         $host = Utiles::obtenerVariableDelEntorno("POSTGRES_HOST"); // Default host is 'localhost'
//         $port = Utiles::obtenerVariableDelEntorno("POSTGRES_PORT"); // Default port is 5432
        
//         // Obtener el endpoint ID (la primera parte del hostname)
//         $endpointId = explode('.', $host)[0];
//         $dsn = "pgsql:host=$host;port=$port;dbname=$dbName"; //añadir si es conexion remota ;sslmode=verify-ca;sslrootcert=c:/ca.pem

//         try {
//             $database = new PDO($dsn, $user, $password);
//             $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//             $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
//             return $database;
//         } catch (PDOException $e) {
//             throw new Exception('Error de conexión: ' . $e->getMessage());
//         }
//     }
// }