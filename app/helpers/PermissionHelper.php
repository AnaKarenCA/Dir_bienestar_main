<?php

class PermissionHelper
{
    /**
     * Obtiene las unidades administrativas accesibles para un usuario.
     *
     * @param Database $db      Objeto Database (con método query)
     * @param array    $usuario Datos del usuario (con id, rol_id, unidad_administrativa_id)
     * @return array|null Array de IDs de unidades, o null si acceso total.
     */
    public static function getUnidadesAccesibles($db, $usuario)
    {
        $rolId = $usuario['rol_id'] ?? 0;

        // Admin (1) y Coordinador (5) ven todo
        if ($rolId == 1 || $rolId == 5) {
            return null; // null = acceso total
        }

        $unidadIds = [];

        if ($rolId == 3) {
            // Personal: solo su propia unidad
            if (!empty($usuario['unidad_administrativa_id'])) {
                $unidadIds[] = (int)$usuario['unidad_administrativa_id'];
            }
            return $unidadIds;
        }

        if ($rolId == 2) {
            // Jefe de área: su unidad + unidades donde es jefe (unidad_jefe) + hijas según jerarquía
            $propia = (int)$usuario['unidad_administrativa_id'];
            $unidadIds[] = $propia;

            // Obtener unidades donde es jefe desde unidad_jefe
            $sql = "SELECT unidad_administrativa_id 
                    FROM unidad_jefe 
                    WHERE usuario_id = ? AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())";
            $stmt = $db->query($sql);
            $stmt->execute([$usuario['id']]);
            $jefaturas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($jefaturas as $id) {
                $unidadIds[] = (int)$id;
            }

            // Si no tiene jefaturas en unidad_jefe, usar jerarquía estática (fallback)
            if (empty($jefaturas)) {
                $jerarquias = self::getJerarquias();
                if (isset($jerarquias[$propia])) {
                    foreach ($jerarquias[$propia] as $hija) {
                        $unidadIds[] = (int)$hija;
                    }
                }
            } else {
                // También agregar hijas de cada unidad que supervisa
                $jerarquias = self::getJerarquias();
                foreach ($unidadIds as $uid) {
                    if (isset($jerarquias[$uid])) {
                        foreach ($jerarquias[$uid] as $hija) {
                            $unidadIds[] = (int)$hija;
                        }
                    }
                }
            }

            // Eliminar duplicados
            $unidadIds = array_unique($unidadIds);
            return $unidadIds;
        }

        // Otros roles: solo su unidad
        if (!empty($usuario['unidad_administrativa_id'])) {
            return [(int)$usuario['unidad_administrativa_id']];
        }
        return [];
    }

    /**
     * Devuelve la jerarquía estática de unidades.
     * Debe coincidir con la definida en RegistroActividad::JERARQUIAS.
     */
    private static function getJerarquias()
    {
        return [
            5  => [6, 7],      // UAP -> DGV, DIC
            8  => [11, 9, 10], // DPSAI -> DPS, DMV, DAI
            15 => [16, 17],    // DAJRCS -> DVIJ, DRCS
            12 => [13, 14],    // DCSIND -> DPSC, DAGSV
        ];
    }

    /**
     * Construye condiciones SQL para filtrar por unidades accesibles.
     *
     * @param Database $db
     * @param array    $usuario
     * @param string   $alias Alias de la tabla registro_actividad (ej. 'ra')
     * @return array ['sql' => string, 'params' => array]
     */
    public static function getFiltroSQL($db, $usuario, $alias = 'ra')
    {
        $unidades = self::getUnidadesAccesibles($db, $usuario);
        if ($unidades === null) {
            return ['sql' => '', 'params' => []];
        }
        if (empty($unidades)) {
            return ['sql' => " AND 1=0 ", 'params' => []];
        }
        $placeholders = implode(',', array_fill(0, count($unidades), '?'));
        return [
            'sql' => " AND $alias.unidad_administrativa_id IN ($placeholders) ",
            'params' => $unidades
        ];
    }

    /**
     * Verifica si un usuario tiene acceso a un registro específico.
     *
     * @param Database $db
     * @param int      $registroId
     * @param int      $usuarioId
     * @return bool
     */
    public static function tieneAccesoARegistro($db, $registroId, $usuarioId)
    {
        // Obtener el usuario
        $sql = "SELECT * FROM usuario WHERE id = ?";
        $stmt = $db->query($sql);
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) return false;

        $unidades = self::getUnidadesAccesibles($db, $usuario);
        if ($unidades === null) {
            return true; // Admin o Coordinador
        }

        // Obtener la unidad del registro
        $sql = "SELECT unidad_administrativa_id, usuario_id FROM registro_actividad WHERE id = ?";
        $stmt = $db->query($sql);
        $stmt->execute([$registroId]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$registro) return false;

        // Si es personal (rol=3), solo puede ver sus propios registros
        if ($usuario['rol_id'] == 3) {
            return $registro['usuario_id'] == $usuarioId;
        }

        // Jefe: verifica que la unidad del registro esté en las unidades accesibles
        return in_array($registro['unidad_administrativa_id'], $unidades);
    }
}