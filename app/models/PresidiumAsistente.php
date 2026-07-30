<?php

class PresidiumAsistente extends Model
{
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT * FROM presidium_asistente WHERE evento_detalle_id = ? ORDER BY id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    public function guardarMultiple($eventoDetalleId, $presidium)
    {
        // 1. Eliminar antiguos
        $sqlDel = "DELETE FROM presidium_asistente WHERE evento_detalle_id = ?";
        $stmtDel = $this->db->query($sqlDel);
        $stmtDel->execute([$eventoDetalleId]);

        // 2. Insertar nuevos
        if (empty($presidium)) {
            return true;
        }

        $sql = "INSERT INTO presidium_asistente 
                (evento_detalle_id, tipo_presidium_id, nombre_invitado, cargo_invitado) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->query($sql);
        foreach ($presidium as $p) {
            $stmt->execute([
                $eventoDetalleId,
                $p['tipo_presidium_id'],
                $p['nombre_invitado'],
                $p['cargo_invitado']
            ]);
        }
        return true;
    }
}