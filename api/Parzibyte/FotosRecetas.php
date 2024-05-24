<?php
namespace Parzibyte;

use Exception;
use PDOException;

class FotosRecetas
{
    const UBICACION_FOTOS = "fotos_recetas";

    static function existeFoto($nombreFoto)
    {
        try {
            $bd = BD::obtener();
            $sentencia = $bd->prepare("SELECT foto FROM fotos_recetas WHERE foto = ?");
            $sentencia->execute([$nombreFoto]);
            return $sentencia->fetchObject() !== false;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar la existencia de la foto: " . $e->getMessage());
        }
    }

    public static function obtenerFotos($idReceta)
    {
        try {
            $bd = BD::obtener();
            $sentencia = $bd->prepare("SELECT foto FROM fotos_recetas WHERE id_receta = ?");
            $sentencia->execute([$idReceta]);
            return $sentencia->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener fotos: " . $e->getMessage());
        }
    }

    public static function eliminarFotosDeAlmacenamiento($idReceta)
    {
        $fotos = self::obtenerFotos($idReceta);
        foreach ($fotos as $foto) {
            $rutaAbsoluta = self::obtenerRutaAbsolutaFoto($foto->foto);
            if (file_exists($rutaAbsoluta) && is_writable($rutaAbsoluta)) {
                unlink($rutaAbsoluta);
            }
        }
    }

    public static function obtenerRutaAbsolutaFoto($nombre)
    {
        return self::UBICACION_FOTOS . DIRECTORY_SEPARATOR . $nombre;
    }

    public static function eliminarFotos($idReceta)
    {
        self::eliminarFotosDeAlmacenamiento($idReceta);
        try {
            $bd = BD::obtener();
            $sentencia = $bd->prepare("DELETE FROM fotos_recetas WHERE id_receta = ?");
            return $sentencia->execute([$idReceta]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar fotos de la base de datos: " . $e->getMessage());
        }
    }

    static function cadenaAleatoriaInsegura()
    {
        $caracteres = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($caracteres), 0, 10);
    }

    static function obtenerNombreFotoNoRepetido($extensionArchivo)
    {
        do {
            $nombre = self::cadenaAleatoriaInsegura() . "." . $extensionArchivo;
        } while (self::existeFoto($nombre));
        return $nombre;
    }

    public static function fotoValida($ubicacionTemporal)
    {
        $mime = mime_content_type($ubicacionTemporal);
        return in_array($mime, ["image/jpeg", "image/png"]);
    }

    public static function guardarFoto($archivo, $idReceta)
    {
        $ubicacionTemporal = $archivo["tmp_name"];
        if (!self::fotoValida($ubicacionTemporal)) {
            return false;
        }

        // Hasta aquí podemos confiar en la extensión del archivo
        self::eliminarFotos($idReceta);
        $nombreArchivo = basename($archivo["name"]); // Para evitar cualquier path traversal
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombre = self::obtenerNombreFotoNoRepetido($extension);
        
        if (!file_exists(self::UBICACION_FOTOS)) {
            mkdir(self::UBICACION_FOTOS, 0755, true); // Establece permisos adecuados
        }

        $nuevaUbicacion = self::obtenerRutaAbsolutaFoto($nombre);
        if (!move_uploaded_file($ubicacionTemporal, $nuevaUbicacion)) {
            return false;
        }

        try {
            $bd = BD::obtener();
            $sentencia = $bd->prepare("INSERT INTO fotos_recetas(id_receta, foto) VALUES(?, ?)");
            return $sentencia->execute([$idReceta, $nombre]);
        } catch (PDOException $e) {
            throw new Exception("Error al guardar la foto en la base de datos: " . $e->getMessage());
        }
    }
}
