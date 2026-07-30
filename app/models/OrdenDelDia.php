<?php

class OrdenDelDia extends Model
{
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT * FROM orden_del_dia WHERE evento_detalle_id = ? ORDER BY hora_inicio";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    public function guardarMultiple($eventoDetalleId, $ordenes)
    {
        // 1. Eliminar registros antiguos
        $sqlDel = "DELETE FROM orden_del_dia WHERE evento_detalle_id = ?";
        $stmtDel = $this->db->query($sqlDel);
        $stmtDel->execute([$eventoDetalleId]);

        // 2. Insertar nuevos
        if (empty($ordenes)) {
            return true;
        }

        $sql = "INSERT INTO orden_del_dia 
                (evento_detalle_id, hora_inicio, hora_fin, responsable_id, actividad) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->query($sql);

        foreach ($ordenes as $o) {
            $stmt->execute([
                $eventoDetalleId,
                $o['hora_inicio'],
                $o['hora_fin'],
                $o['responsable_id'],
                $o['actividad']
            ]);
        }

        return true;
    }
}