<?php

namespace Parzibyte;

use Exception;

class Utiles
{
    private static $variables = null;

    static function obtenerVariableDelEntorno($clave, $valorPorDefecto = null)
    {
        if (self::$variables === null) {
            self::cargarVariablesDeEntorno();
        }

        if (isset(self::$variables[$clave])) {
            return self::$variables[$clave];
        }

        if ($valorPorDefecto !== null) {
            return $valorPorDefecto;
        }

        throw new Exception("La clave especificada (" . $clave . ") no existe en el archivo de las variables de entorno");
    }

    private static function cargarVariablesDeEntorno()
    {
        $archivo = "env.php";
        if (!file_exists($archivo)) {
            throw new Exception("El archivo de las variables de entorno ($archivo) no existe. Favor de crearlo");
        }

        self::$variables = parse_ini_file($archivo);
        if (self::$variables === false) {
            throw new Exception("Error al parsear el archivo de variables de entorno ($archivo)");
        }
    }
}
