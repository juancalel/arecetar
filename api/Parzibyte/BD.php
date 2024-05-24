<?php

namespace Parzibyte;

use PDO;
use PDOException;
use Exception;

class BD
{
  static function obtener()
  {
    try {
      $password = Utiles::obtenerVariableDelEntorno("POSTGRES_PASSWORD");
      $user = Utiles::obtenerVariableDelEntorno("POSTGRES_USER");
      $dbName = Utiles::obtenerVariableDelEntorno("POSTGRES_DATABASE_NAME");
      $port = Utiles::obtenerVariableDelEntorno("POSTGRES_PORT", 5432);

      if (!$password || !$user || !$dbName) {
        throw new Exception("Faltan variables de entorno requeridas para la conexión a la base de datos.");
      }

      $database = new PDO('pgsql:host=localhost;port=' . $port . ';dbname=' . $dbName, $user, $password);
      $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
      $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      return $database;
    } catch (PDOException $e) {
      throw new Exception("Error al conectar a la base de datos: " . $e->getMessage());
    } catch (Exception $e) {
      throw new Exception("Error: " . $e->getMessage());
    }
  }
}
