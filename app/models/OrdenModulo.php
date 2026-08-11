<?php

class OrdenModulo extends Model
{
    /**
     * Obtiene todos los módulos de un evento_detalle
     */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT om.*
                FROM orden_modulo om
                INNER JOIN orden_del_dia odd ON odd.id = om.orden_del_dia_id
                WHERE odd.evento_detalle_id = ?
                ORDER BY om.id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    /**
     * Elimina todos los módulos de un evento_detalle
     */
    public function eliminarPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "DELETE FROM orden_modulo WHERE evento_detalle_id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$eventoDetalleId]);
    }

    /**
     * Inserta un nuevo módulo
     */
    public function insertar($datos)
    {
        $sql = "INSERT INTO orden_modulo (evento_detalle_id, institucion, servicio) VALUES (?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['evento_detalle_id'],
            $datos['institucion'],
            $datos['servicio']
        ]);
    }
}
