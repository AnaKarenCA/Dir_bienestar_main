<?php

class Evidencia extends Model
{
    /**
     * Obtiene todas las evidencias de un registro de actividad
     */
    public function obtenerPorRegistro($registroActividadId)
    {
        $sql = "SELECT * FROM evidencia WHERE registro_actividad_id = ? ORDER BY FIELD(tipo, 'llegada', 'durante', 'finalizacion')";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroActividadId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una evidencia específica por registro y tipo
     */
    public function obtenerPorTipo($registroActividadId, $tipo)
    {
        $sql = "SELECT * FROM evidencia WHERE registro_actividad_id = ? AND tipo = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroActividadId, $tipo]);
        return $stmt->fetch();
    }

    /**
     * Obtiene una evidencia por su ID
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM evidencia WHERE id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cuenta cuántas evidencias tiene un registro
     */
    public function contarPorRegistro($registroActividadId)
    {
        $sql = "SELECT COUNT(*) AS total FROM evidencia WHERE registro_actividad_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroActividadId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Guarda una nueva evidencia o actualiza una existente
     */
    public function guardar($datos)
    {
        // Validar que exista el registro
        if (empty($datos['registro_actividad_id']) || empty($datos['tipo'])) {
            return false;
        }

        // Verificar si ya existe una evidencia de este tipo
        $existente = $this->obtenerPorTipo($datos['registro_actividad_id'], $datos['tipo']);

        if ($existente) {
            // UPDATE
            $sql = "UPDATE evidencia SET 
                        fotografia = ?,
                        latitud = ?,
                        longitud = ?,
                        precision_geolocalizacion = ?,
                        fecha = ?,
                        hora = ?,
                        comentarios = ?
                    WHERE id = ?";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['fotografia'] ?? null,
                $datos['latitud'] ?? null,
                $datos['longitud'] ?? null,
                $datos['precision'] ?? null,
                $datos['fecha'] ?? date('Y-m-d'),
                $datos['hora'] ?? date('H:i:s'),
                $datos['comentarios'] ?? null,
                $existente['id']
            ]);
            return $result ? $existente['id'] : false;
        } else {
            // INSERT
            $sql = "INSERT INTO evidencia 
                        (registro_actividad_id, tipo, fotografia, latitud, longitud, precision_geolocalizacion, fecha, hora, comentarios)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['registro_actividad_id'],
                $datos['tipo'],
                $datos['fotografia'] ?? null,
                $datos['latitud'] ?? null,
                $datos['longitud'] ?? null,
                $datos['precision'] ?? null,
                $datos['fecha'] ?? date('Y-m-d'),
                $datos['hora'] ?? date('H:i:s'),
                $datos['comentarios'] ?? null
            ]);
            if ($result) {
                return $this->db->getConnection()->lastInsertId();
            }
            return false;
        }
    }

    /**
     * Elimina una evidencia por ID
     */
    public function eliminar($id)
    {
        // Obtener la ruta de la imagen antes de eliminar
        $evidencia = $this->obtenerPorId($id);
        if ($evidencia && !empty($evidencia['fotografia'])) {
            $ruta = PUBLIC_PATH . '/uploads/evidencias/' . basename($evidencia['fotografia']);
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        $sql = "DELETE FROM evidencia WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene las actividades del usuario con el conteo de evidencias
     */
public function obtenerActividadesConEvidencias($usuarioId)
{
    $sql = "SELECT 
                r.id,
                COALESCE(ap.descripcion, r.descripcion, CONCAT('Actividad #', r.id)) AS actividad_desc,
                r.fecha_inicio,
                r.fecha_fin,
                r.hora_inicio,
                r.hora_fin,
                l.nombre AS lugar_nombre,
                COUNT(e.id) AS evidencias_count,
                MAX(CASE WHEN e.tipo = 'llegada' THEN 1 ELSE 0 END) AS tiene_llegada,
                MAX(CASE WHEN e.tipo = 'durante' THEN 1 ELSE 0 END) AS tiene_durante,
                MAX(CASE WHEN e.tipo = 'finalizacion' THEN 1 ELSE 0 END) AS tiene_finalizacion
            FROM registro_actividad r
            LEFT JOIN lugar l ON l.id = r.lugar_id
            LEFT JOIN actividad_programada ap ON ap.id = r.actividad_programada_id
            LEFT JOIN evidencia e ON e.registro_actividad_id = r.id
            WHERE r.usuario_id = ?
            GROUP BY r.id
            ORDER BY r.fecha_inicio DESC, r.hora_inicio DESC";
    $stmt = $this->db->query($sql);
    $stmt->execute([$usuarioId]);
    return $stmt->fetchAll();
}
}