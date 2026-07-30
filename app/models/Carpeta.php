<?php

class Carpeta extends Model
{
    /**
     * Obtiene una carpeta a partir del ID de evento_detalle
     */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT c.* FROM carpeta c
                JOIN evento_detalle ed ON ed.carpeta_id = c.id
                WHERE ed.id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetch();
    }

    /**
     * Obtiene una carpeta a partir del ID de registro_actividad
     */
    public function obtenerPorRegistroActividadId($registroActividadId)
    {
        $sql = "SELECT * FROM carpeta WHERE registro_actividad_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroActividadId]);
        return $stmt->fetch();
    }

    /**
     * Guarda (inserta o actualiza) una carpeta
     */
    public function guardar($datos)
    {
        if (isset($datos['id']) && $datos['id']) {
            $sql = "UPDATE carpeta SET 
                        logo_toluca = ?,
                        direccion_entrega = ?,
                        fecha_entrega = ?,
                        link_mapa = ?,
                        realizo_id = ?,
                        autorizado_por_id = ?,
                        firma = ?,
                        estado = ?,
                        justificacion_fuera_tiempo = ?
                    WHERE id = ?";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['logo_toluca'],
                $datos['direccion_entrega'],
                $datos['fecha_entrega'],
                $datos['link_mapa'],
                $datos['realizo_id'],
                $datos['autorizado_por_id'],
                $datos['firma'],
                $datos['estado'],
                $datos['justificacion_fuera_tiempo'],
                $datos['id']
            ]);
            return $result ? $datos['id'] : false;
        } else {
            $sql = "INSERT INTO carpeta (registro_actividad_id, logo_toluca, direccion_entrega, fecha_entrega, link_mapa, realizo_id, autorizado_por_id, firma, estado, justificacion_fuera_tiempo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['registro_actividad_id'],
                $datos['logo_toluca'],
                $datos['direccion_entrega'],
                $datos['fecha_entrega'],
                $datos['link_mapa'],
                $datos['realizo_id'],
                $datos['autorizado_por_id'],
                $datos['firma'],
                $datos['estado'],
                $datos['justificacion_fuera_tiempo']
            ]);
            if ($result) {
                return $this->db->getConnection()->lastInsertId();
            }
            return false;
        }
    }

    /**
     * Obtiene las carpetas pendientes de revisión para una unidad (o todas si $unidadId es null)
     * Incluye los estados: entregado, revisado, fuera_tiempo
     */
    public function obtenerPendientesRevision($unidadId = null)
    {
        $sql = "SELECT c.*, 
                       u.nombre AS usuario_nombre,
                       u.puesto AS usuario_puesto,
                       ua.nombre AS unidad_nombre,
                       ua.id AS unidad_administrativa_id,
                       ap.descripcion AS actividad_desc,
                       r.descripcion AS registro_descripcion,
                       r.fecha_inicio,
                       r.fecha_fin,
                       r.hora_inicio,
                       r.hora_fin,
                       r.lugar_id,
                       r.beneficiarios_asistentes,
                       r.tipo_entregable_id
                FROM carpeta c
                JOIN registro_actividad r ON r.id = c.registro_actividad_id
                JOIN usuario u ON u.id = r.usuario_id
                LEFT JOIN unidad_administrativa ua ON ua.id = r.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = r.actividad_programada_id
                WHERE c.estado IN ('entregado', 'revisado', 'fuera_tiempo')";
        
        if ($unidadId) {
            $sql .= " AND r.unidad_administrativa_id = ?";
        }
        $sql .= " ORDER BY c.fecha_entrega DESC";

        $stmt = $this->db->query($sql);
        if ($unidadId) {
            $stmt->execute([$unidadId]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una carpeta con datos completos (incluyendo nombres de quienes realizan y autorizan)
     * a partir del ID de registro_actividad
     */
    public function obtenerConDatosCompletos($registroActividadId)
{
    $sql = "SELECT c.*, ed.id AS evento_detalle_id
            FROM carpeta c
            LEFT JOIN evento_detalle ed ON ed.carpeta_id = c.id
            WHERE c.registro_actividad_id = ?";
    $stmt = $this->db->query($sql);
    $stmt->execute([$registroActividadId]);
    return $stmt->fetch();
}

    /**
     * Obtiene una carpeta por su ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->db->query("SELECT * FROM carpeta WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Actualiza únicamente el estado de una carpeta
     */
    public function actualizarEstado($id, $estado)
    {
        $sql = "UPDATE carpeta SET estado = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Obtiene TODA la información de una carpeta, incluyendo datos del registro,
     * unidad, actividad programada y evento_detalle.
     * Útil para la vista de detalle (revisión).
     * 
     * IMPORTANTE: se incluye ua.id AS unidad_administrativa_id para validar permisos.
     */
    public function obtenerConTodo($id)
    {
        $sql = "SELECT c.*, 
                       u.nombre AS usuario_nombre,
                       u.puesto AS usuario_puesto,
                       ua.nombre AS unidad_nombre,
                       ua.id AS unidad_administrativa_id,
                       ap.descripcion AS actividad_desc,
                       r.descripcion AS registro_descripcion,
                       r.fecha_inicio,
                       r.fecha_fin,
                       r.hora_inicio,
                       r.hora_fin,
                       r.lugar_id,
                       r.beneficiarios_asistentes,
                       r.tipo_entregable_id,
                       ed.nombre_evento,
                       ed.fecha_evento,
                       ed.objetivo,
                       ed.justificacion
                FROM carpeta c
                JOIN registro_actividad r ON r.id = c.registro_actividad_id
                JOIN usuario u ON u.id = r.usuario_id
                LEFT JOIN unidad_administrativa ua ON ua.id = r.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = r.actividad_programada_id
                LEFT JOIN evento_detalle ed ON ed.carpeta_id = c.id
                WHERE c.id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}