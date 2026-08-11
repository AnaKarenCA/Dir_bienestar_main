<?php

class OrdenInvitado extends Model
{
    /**
     * Obtiene todos los invitados de un evento_detalle
     */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT oi.*
                FROM orden_invitado oi
                INNER JOIN orden_del_dia odd ON odd.id = oi.orden_del_dia_id
                WHERE odd.evento_detalle_id = ?
                ORDER BY oi.id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    /**
     * Elimina todos los invitados de un evento_detalle
     */
    public function eliminarPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "DELETE FROM orden_invitado WHERE evento_detalle_id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$eventoDetalleId]);
    }

    /**
     * Inserta un nuevo invitado
     */
    public function insertar($datos)
    {
        $sql = "INSERT INTO orden_invitado (evento_detalle_id, nombre, cargo) VALUES (?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['evento_detalle_id'],
            $datos['nombre'],
            $datos['cargo']
        ]);
    }
}
