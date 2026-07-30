<?php

class OrdenDelDia extends Model
{
    /**
     * Obtiene todas las filas del orden del día para un evento_detalle específico,
     * calculando la duración en minutos (hora_fin - hora_inicio).
     *
     * @param int $eventoDetalleId
     * @return array
     */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT *, 
                       TIMESTAMPDIFF(MINUTE, hora_inicio, hora_fin) AS duracion_calculada
                FROM orden_del_dia 
                WHERE evento_detalle_id = ? 
                ORDER BY hora_inicio";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    /**
     * Reemplaza todas las filas del orden del día para un evento_detalle.
     * Elimina los registros antiguos e inserta los nuevos.
     *
     * @param int   $eventoDetalleId
     * @param array $ordenes  Array de arrays con claves: hora_inicio, hora_fin, responsable_id, otro_responsable, actividad
     * @return bool
     */
    public function guardarMultiple($eventoDetalleId, $ordenes)
    {
        // 1. Eliminar registros antiguos
        $sqlDel = "DELETE FROM orden_del_dia WHERE evento_detalle_id = ?";
        $stmtDel = $this->db->query($sqlDel);
        $stmtDel->execute([$eventoDetalleId]);

        // 2. Si no hay nuevos, terminar
        if (empty($ordenes)) {
            return true;
        }

        // 3. Insertar los nuevos
        $sql = "INSERT INTO orden_del_dia 
                (evento_detalle_id, hora_inicio, hora_fin, responsable_id, otro_responsable, actividad) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->query($sql);

        foreach ($ordenes as $o) {
            $stmt->execute([
                $eventoDetalleId,
                $o['hora_inicio'],
                $o['hora_fin'],
                $o['responsable_id'] ?? null,
                $o['otro_responsable'] ?? null,
                $o['actividad'] ?? ''
            ]);
        }

        return true;
    }
}