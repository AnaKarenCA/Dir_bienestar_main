<?php

class OrdenRequerimiento extends Model
{
    /**
     * Obtiene requerimientos de un evento_detalle filtrados por tipo
     */
    public function obtenerPorEventoDetalleIdYTipo($eventoDetalleId, $tipo)
    {
        $sql = "SELECT r.*, i.nombre_insumo, i.medida, i.unidad
                FROM orden_requerimiento r
                INNER JOIN orden_del_dia odd ON odd.id = r.orden_del_dia_id
                LEFT JOIN inventario_insumo i ON i.id = r.inventario_insumo_id
                WHERE odd.evento_detalle_id = ? AND r.tipo_requerimiento = ?
                ORDER BY r.id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId, $tipo]);
        return $stmt->fetchAll();
    }

    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT r.*, i.nombre_insumo, i.medida, i.unidad
                FROM orden_requerimiento r
                INNER JOIN orden_del_dia odd ON odd.id = r.orden_del_dia_id
                LEFT JOIN inventario_insumo i ON i.id = r.inventario_insumo_id
                WHERE odd.evento_detalle_id = ?
                ORDER BY r.tipo_requerimiento, r.id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    /**
     * Elimina todos los requerimientos de un evento_detalle
     */
    public function eliminarPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "DELETE FROM orden_requerimiento WHERE evento_detalle_id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$eventoDetalleId]);
    }

    /**
     * Inserta un nuevo requerimiento
     */
    public function insertar($datos)
    {
        $sql = "INSERT INTO orden_requerimiento 
                (evento_detalle_id, tipo, cantidad, insumo_id, medida, unidad) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['evento_detalle_id'],
            $datos['tipo'],
            $datos['cantidad'],
            $datos['insumo_id'],
            $datos['medida'],
            $datos['unidad']
        ]);
    }
}
