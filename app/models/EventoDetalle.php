<?php

class EventoDetalle extends Model
{
    public function obtenerTodosConCarpeta()
    {
        $sql = "SELECT ed.*, c.id as carpeta_id, c.direccion_entrega, c.fecha_entrega
                FROM evento_detalle ed
                LEFT JOIN carpeta c ON c.id = ed.carpeta_id
                ORDER BY ed.fecha_evento DESC";
        $stmt = $this->db->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM evento_detalle WHERE id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function obtenerPorCarpetaId($carpetaId)
    {
        if (!$carpetaId) return null;
        $sql = "SELECT * FROM evento_detalle WHERE carpeta_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$carpetaId]);
        return $stmt->fetch();
    }

    public function obtenerPorCarpetaIdCompleto($carpetaId)
    {
        if (!$carpetaId) return null;
        $sql = "SELECT * FROM evento_detalle WHERE carpeta_id = ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $stmt->execute([$carpetaId]);
        return $stmt->fetch();
    }

    public function guardar($datos)
    {
        if (isset($datos['id']) && $datos['id']) {
            // UPDATE
            $sql = "UPDATE evento_detalle SET 
                        carpeta_id = ?,
                        nombre_evento = ?,
                        fecha_evento = ?,
                        objetivo = ?,
                        justificacion = ?,
                        vestimenta = ?,
                        imagen_diseno = ?,
                        descripcion_meta = ?,
                        link_mapa = ?,
                        imagen_fondo = ?,
                        imagen_lugar = ?,
                        imagen_maps = ?,
                        imagen_croquis = ?,
                        aprobado_por = ?,
                        responsable_evento = ?,
                        coordinacion_evento = ?,
                        maestra_ceremonias = ?,
                        num_spots = ?,
                        invitados_especiales = ?,
                        modulos_jornada = ?,
                        requerimientos_internos = ?,
                        requerimientos_externos = ?,
                        comunicacion_social = ?,
                        delegacion_admin_resumen = ?,
                        fecha_entrega = ?,
                        firma = ?
                    WHERE id = ?";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['carpeta_id'],
                $datos['nombre_evento'],
                $datos['fecha_evento'],
                $datos['objetivo'] ?? null,
                $datos['justificacion'] ?? null,
                $datos['vestimenta'] ?? null,
                $datos['imagen_diseno'] ?? null,
                $datos['descripcion_meta'] ?? null,
                $datos['link_mapa'] ?? null,
                $datos['imagen_fondo'] ?? null,
                $datos['imagen_lugar'] ?? null,
                $datos['imagen_maps'] ?? null,
                $datos['imagen_croquis'] ?? null,
                $datos['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
                $datos['responsable_evento'] ?? '',
                $datos['coordinacion_evento'] ?? '',
                $datos['maestra_ceremonias'] ?? '',
                $datos['num_spots'] ?? 5,
                $datos['invitados_especiales'] ?? '[]',
                $datos['modulos_jornada'] ?? '[]',
                $datos['requerimientos_internos'] ?? '[]',
                $datos['requerimientos_externos'] ?? '[]',
                $datos['comunicacion_social'] ?? '',
                $datos['delegacion_admin_resumen'] ?? '',
                $datos['fecha_entrega'] ?? null,
                $datos['firma'] ?? null,
                $datos['id']
            ]);
            return $result ? $datos['id'] : false;
        } else {
            // INSERT
            $sql = "INSERT INTO evento_detalle (
                        carpeta_id, nombre_evento, fecha_evento, objetivo, justificacion, vestimenta,
                        imagen_diseno, descripcion_meta, link_mapa, imagen_fondo, imagen_lugar, imagen_maps,
                        imagen_croquis, aprobado_por, responsable_evento, coordinacion_evento,
                        maestra_ceremonias, num_spots,
                        invitados_especiales, modulos_jornada, requerimientos_internos, requerimientos_externos,
                        comunicacion_social, delegacion_admin_resumen, fecha_entrega, firma
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->query($sql);
            $result = $stmt->execute([
                $datos['carpeta_id'],
                $datos['nombre_evento'],
                $datos['fecha_evento'],
                $datos['objetivo'] ?? null,
                $datos['justificacion'] ?? null,
                $datos['vestimenta'] ?? null,
                $datos['imagen_diseno'] ?? null,
                $datos['descripcion_meta'] ?? null,
                $datos['link_mapa'] ?? null,
                $datos['imagen_fondo'] ?? null,
                $datos['imagen_lugar'] ?? null,
                $datos['imagen_maps'] ?? null,
                $datos['imagen_croquis'] ?? null,
                $datos['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
                $datos['responsable_evento'] ?? '',
                $datos['coordinacion_evento'] ?? '',
                $datos['maestra_ceremonias'] ?? '',
                $datos['num_spots'] ?? 5,
                $datos['invitados_especiales'] ?? '[]',
                $datos['modulos_jornada'] ?? '[]',
                $datos['requerimientos_internos'] ?? '[]',
                $datos['requerimientos_externos'] ?? '[]',
                $datos['comunicacion_social'] ?? '',
                $datos['delegacion_admin_resumen'] ?? '',
                $datos['fecha_entrega'] ?? null,
                $datos['firma'] ?? null
            ]);
            if ($result) {
                return $this->db->getConnection()->lastInsertId();
            }
            return false;
        }
    }
}