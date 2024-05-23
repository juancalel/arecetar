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

use Parzibyte\BD;


class Recetas
{
    public static function buscar($busqueda)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("SELECT recetas.id, recetas.nombre, recetas.descripcion, recetas.porciones, fotos_recetas.foto 
                                       FROM recetas 
                                       INNER JOIN fotos_recetas ON recetas.id = fotos_recetas.id_receta 
                                       WHERE recetas.nombre LIKE ? OR recetas.descripcion LIKE ?");
            $sentencia->execute(["%$busqueda%", "%$busqueda%"]);
            return $sentencia->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error al buscar recetas: " . $e->getMessage(), $e->getCode());
        }
    }

    public static function actualizar($nombre, $descripcion, $porciones, $ingredientes, $pasos, $foto, $idReceta)
    {
        $bd = BD::obtener();
        try {
            // Iniciar la transacción
            $bd->beginTransaction();

            // Actualizar la receta
            $sentencia = $bd->prepare("UPDATE recetas SET nombre = ?, porciones = ?, descripcion = ? WHERE id = ?");
            $sentencia->execute([$nombre, $porciones, $descripcion, $idReceta]);

            // Actualizar pasos e ingredientes
            self::eliminarPasosDeReceta($idReceta);
            self::guardarPasos($pasos, $idReceta);
            self::eliminarIngredientesDeReceta($idReceta);
            self::guardarIngredientes($ingredientes, $idReceta);

            // Actualizar la foto si se proporciona
            if ($foto) {
                FotosRecetas::guardarFoto($foto, $idReceta);
            }

            // Confirmar la transacción
            $bd->commit();

            return true;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $bd->rollBack();
            throw new Exception("Error al actualizar receta: " . $e->getMessage(), $e->getCode());
        }
    }



    public static function eliminar($id)
    {
        try {
            $bd = BD::obtener();
            // Iniciar la transacción
            $bd->beginTransaction();

            // Eliminar fotos primero para evitar violaciones de clave foránea
            FotosRecetas::eliminarFotos($id);

            // Eliminar la receta
            $sentencia = $bd->prepare("DELETE FROM recetas WHERE id = ?");
            $sentencia->execute([$id]);

            // Confirmar la transacción
            $bd->commit();
            return true;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $bd->rollBack();
            throw new Exception("Error al eliminar receta: " . $e->getMessage(), $e->getCode());
        }
    }

    public static function agregar($nombre, $descripcion, $porciones, $ingredientes, $pasos, $foto)
{
    $bd = BD::obtener();
    try {
        // Iniciar la transacción
        
        // $bd->beginTransaction();

        // Insertar la receta y obtener el ID
        $sentencia = $bd->prepare("INSERT INTO recetas(nombre, porciones, descripcion) VALUES (?, ?, ?) RETURNING id");
        $resultado = $sentencia->execute([$nombre, $porciones, $descripcion]);
        if (!$resultado) {
            throw new Exception("Error al insertar receta en la base de datos");
        }
        $idReceta = $sentencia->fetchColumn();

        // Verificar que el ID de la receta sea válido
        if (!$idReceta) {
            throw new Exception("No se pudo obtener el ID de la receta");
        }

        // Guardar los pasos e ingredientes
        self::guardarIngredientes($ingredientes, $idReceta);
        self::guardarPasos($pasos, $idReceta);
        

        // Guardar la foto si se proporciona
        if ($foto) {
            FotosRecetas::guardarFoto($foto, $idReceta);
        }

        // Confirmar la transacción
        
        // $bd->commit();

        return true;
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        
        // $bd->rollBack();
        
        throw new Exception("Error al agregar receta: " . $e->getMessage(), $e->getCode());
    }
}


    private static function guardarPasos($pasos, $idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("INSERT INTO pasos_recetas(id_receta, paso) VALUES (?, ?)");
            foreach ($pasos as $paso) {
                // Asegurarse de que $paso es un string
                if (is_array($paso) && isset($paso['paso'])) {
                    $sentencia->execute([$idReceta, $paso['paso']]);
                } elseif (is_string($paso)) {
                    $sentencia->execute([$idReceta, $paso]);
                } else {
                    throw new Exception("Formato de paso no válido");
                }
            }
        } catch (PDOException $e) {
            throw new Exception("Error al guardar pasos: " . $e->getMessage(), $e->getCode());
        }
    }

    private static function guardarIngredientes($ingredientes, $idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("INSERT INTO ingredientes_recetas(id_receta, nombre, cantidad, unidad_medida) VALUES (?, ?, ?, ?)");
            foreach ($ingredientes as $ingrediente) {
                $sentencia->execute([$idReceta, $ingrediente['nombre'], $ingrediente['cantidad'], $ingrediente['unidadMedida']]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al guardar ingredientes: " . $e->getMessage(), $e->getCode());
        }
    }

    public static function ingredientesDeReceta($idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("SELECT nombre, cantidad, unidad_medida as unidadMedida 
                                       FROM ingredientes_recetas 
                                       WHERE id_receta = ?");
            $sentencia->execute([$idReceta]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener ingredientes: " . $e->getMessage(), $e->getCode());
        }
    }


    public static function pasosDeReceta($idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("SELECT paso FROM pasos_recetas WHERE id_receta = ?");
            $sentencia->execute([$idReceta]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pasos: " . $e->getMessage(), $e->getCode());
        }
    }

    private static function eliminarPasosDeReceta($idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("DELETE FROM pasos_recetas WHERE id_receta = ?");
            $sentencia->execute([$idReceta]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar pasos: " . $e->getMessage(), $e->getCode());
        }
    }

    public static function eliminarIngredientesDeReceta($idReceta)
    {
        $bd = BD::obtener();
        try {
            $sentencia = $bd->prepare("DELETE FROM ingredientes_recetas WHERE id_receta = ?");
            $sentencia->execute([$idReceta]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar ingredientes: " . $e->getMessage(), $e->getCode());
        }
    }

    public static function obtener()
{
    $bd = BD::obtener();
    try {
        $sentencia = $bd->query("SELECT * FROM recetas");
        return $sentencia->fetchAll();
    } catch (PDOException $e) {
        throw new Exception("Error al obtener recetas: " . $e->getMessage(), $e->getCode());
    }
}

}


