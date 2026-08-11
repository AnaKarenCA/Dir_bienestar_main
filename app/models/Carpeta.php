<?php

class Carpeta extends Model
{
    /**
     * Obtiene una carpeta por su ID.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM carpeta WHERE id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene la carpeta asociada a un registro de actividad.
     */
    public function obtenerPorRegistroActividadId($registroId)
    {
        $sql = "SELECT * FROM carpeta WHERE registro_actividad_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroId]);
        return $stmt->fetch();
    }

    /**
     * Obtiene datos completos de una carpeta junto con el registro y relaciones.
     */
    public function obtenerConDatosCompletos($registroId)
    {
        $sql = "
            SELECT 
                c.*,
                ra.id AS registro_id,
                ra.fecha_inicio,
                ra.fecha_fin,
                ra.hora_inicio,
                ra.hora_fin,
                ra.descripcion AS registro_descripcion,
                ra.beneficiarios_asistentes,
                u.nombre AS realizo_nombre,
                u.puesto AS realizo_puesto,
                ua.nombre AS unidad_nombre,
                ua.objetivo AS unidad_objetivo,
                ap.descripcion AS actividad_desc,
                l.nombre AS lugar_nombre,
                te.nombre_entregable,
                dom.calle,
                dom.numero_exterior,
                dom.numero_interior,
                cp.cp AS codigo_postal,
                d.nombre AS delegacion_nombre,
                sd.nombre AS subdelegacion_nombre
            FROM carpeta c
            INNER JOIN registro_actividad ra ON ra.id = c.registro_actividad_id
            INNER JOIN usuario u ON u.id = c.realizo_id
            INNER JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
            LEFT JOIN actividad_programada ap ON ap.id = ra.actividad_programada_id
            INNER JOIN lugar l ON l.id = ra.lugar_id
            INNER JOIN tipo_entregable te ON te.id = ra.tipo_entregable_id
            INNER JOIN domicilio dom ON dom.id = ra.domicilio_id
            LEFT JOIN codigo_postal cp ON cp.id = dom.codigo_postal_id
            LEFT JOIN subdelegacion sd ON sd.id = cp.subdelegacion_id
            LEFT JOIN delegacion d ON d.id = sd.delegacion_id
            WHERE c.registro_actividad_id = ?
            LIMIT 1
        ";
        $stmt = $this->db->query($sql);
        $stmt->execute([$registroId]);
        return $stmt->fetch();
    }

    /**
     * Guarda (inserta o actualiza) una carpeta.
     * - Si se pasa 'id', actualiza.
     * - Si no se pasa 'id', pero existe carpeta para el registro, la actualiza.
     * - Si no existe, inserta y devuelve el nuevo ID.
     */
    public function guardar($datos)
    {
        // Si no se pasa 'id' pero existe una carpeta para este registro, la obtenemos
        if (empty($datos['id']) && !empty($datos['registro_actividad_id'])) {
            $existente = $this->obtenerPorRegistroActividadId($datos['registro_actividad_id']);
            if ($existente) {
                $datos['id'] = $existente['id'];
            }
        }

        // Si ahora tenemos 'id', actualizamos
        if (!empty($datos['id'])) {
            $sql = "
                UPDATE carpeta SET
                    registro_actividad_id = ?,
                    logo_toluca = ?,
                    direccion_entrega = ?,
                    link_mapa = ?,
                    fecha_entrega = ?,
                    realizo_id = ?,
                    autorizado_por_id = ?,
                    firma = ?,
                    estado = ?,
                    justificacion_fuera_tiempo = ?
                WHERE id = ?
            ";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['registro_actividad_id'],
                $datos['logo_toluca'] ?? null,
                $datos['direccion_entrega'] ?? null,
                $datos['link_mapa'] ?? null,
                $datos['fecha_entrega'] ?? date('Y-m-d'),
                $datos['realizo_id'],
                $datos['autorizado_por_id'] ?? null,
                $datos['firma'] ?? null,
                $datos['estado'] ?? 'pendiente',
                $datos['justificacion_fuera_tiempo'] ?? null,
                $datos['id']
            ]);
            return $result ? $datos['id'] : false;
        } else {
            // Insertar nueva carpeta
            $sql = "
                INSERT INTO carpeta (
                    registro_actividad_id,
                    logo_toluca,
                    direccion_entrega,
                    link_mapa,
                    fecha_entrega,
                    realizo_id,
                    autorizado_por_id,
                    firma,
                    estado,
                    justificacion_fuera_tiempo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['registro_actividad_id'],
                $datos['logo_toluca'] ?? null,
                $datos['direccion_entrega'] ?? null,
                $datos['link_mapa'] ?? null,
                $datos['fecha_entrega'] ?? date('Y-m-d'),
                $datos['realizo_id'],
                $datos['autorizado_por_id'] ?? null,
                $datos['firma'] ?? null,
                $datos['estado'] ?? 'pendiente',
                $datos['justificacion_fuera_tiempo'] ?? null
            ]);
            if ($result) {
                // Obtener el último ID insertado (funciona con PDO)
                return $this->db->lastInsertId();
            }
            return false;
        }
    }

    /**
     * Actualiza solo el estado de la carpeta.
     */
    public function actualizarEstado($id, $estado)
    {
        $sql = "UPDATE carpeta SET estado = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$estado, $id]);
    }

    // ============================================================
    // MÉTODOS PARA REVISIÓN
    // ============================================================

    public function obtenerPendientesRevision()
    {
        $sql = "
            SELECT 
                c.*, 
                ra.id AS registro_id, 
                u.nombre AS usuario_nombre, 
                ua.nombre AS unidad_nombre
            FROM carpeta c
            JOIN registro_actividad ra ON ra.id = c.registro_actividad_id
            JOIN usuario u ON u.id = ra.usuario_id
            JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
            WHERE c.estado IN ('entregado', 'revisado')
            ORDER BY c.fecha_entrega DESC
        ";
        $stmt = $this->db->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPendientesRevisionPorUnidades($unidadesIds)
    {
        if (empty($unidadesIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unidadesIds), '?'));
        $sql = "
            SELECT 
                c.*, 
                ra.id AS registro_id, 
                u.nombre AS usuario_nombre, 
                ua.nombre AS unidad_nombre
            FROM carpeta c
            JOIN registro_actividad ra ON ra.id = c.registro_actividad_id
            JOIN usuario u ON u.id = ra.usuario_id
            JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
            WHERE c.estado IN ('entregado', 'revisado')
              AND ra.unidad_administrativa_id IN ($placeholders)
            ORDER BY c.fecha_entrega DESC
        ";
        $stmt = $this->db->query($sql);
        $stmt->execute($unidadesIds);
        return $stmt->fetchAll();
    }

    // ============================================================
    // MÉTODO PARA OBTENER FIRMAS (usado en PowerPoint)
    // ============================================================

    /**
     * Obtiene las firmas necesarias para la carpeta.
     * Devuelve el realizador (quien creó la carpeta) y los cargos fijos
     * de Coordinador de Apoyo Técnico y Delegado Administrativo.
     *
     * @param int $carpetaId
     * @return array
     */
    public function obtenerFirmasPorId($carpetaId)
    {
        // Obtener el usuario que realizó la carpeta
        $sql = "
            SELECT 
                c.id,
                u.nombre AS realizo_nombre,
                u.puesto AS realizo_puesto
            FROM carpeta c
            JOIN usuario u ON u.id = c.realizo_id
            WHERE c.id = ?
        ";
        $stmt = $this->db->query($sql);
        $stmt->execute([$carpetaId]);
        $result = $stmt->fetch();

        // Nombres y cargos fijos (según la lógica del sistema)
        $coordinador_nombre = 'Mtro. Omar Ruiz Castillo';
        $coordinador_puesto = 'Coordinador de Apoyo Técnico';
        $delegado_nombre = 'Lcdo. Marco Antonio Guadarrama López';
        $delegado_puesto = 'Delegado Administrativo';

        if (!$result) {
            return [
                'realizo_nombre'        => '',
                'realizo_puesto'        => '',
                'coordinador_nombre'    => $coordinador_nombre,
                'coordinador_puesto'    => $coordinador_puesto,
                'delegado_nombre'       => $delegado_nombre,
                'delegado_puesto'       => $delegado_puesto
            ];
        }

        return [
            'realizo_nombre'        => $result['realizo_nombre'] ?? '',
            'realizo_puesto'        => $result['realizo_puesto'] ?? '',
            'coordinador_nombre'    => $coordinador_nombre,
            'coordinador_puesto'    => $coordinador_puesto,
            'delegado_nombre'       => $delegado_nombre,
            'delegado_puesto'       => $delegado_puesto
        ];
    }
}