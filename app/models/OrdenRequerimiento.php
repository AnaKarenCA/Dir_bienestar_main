<?php

class OrdenRequerimiento extends Model
{
    /**
     * Obtiene requerimientos de un evento_detalle filtrados por tipo
     */
    public function obtenerPorEventoDetalleIdYTipo($eventoDetalleId, $tipo)
    {
        $sql = "SELECT * FROM orden_requerimiento WHERE evento_detalle_id = ? AND tipo = ? ORDER BY id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId, $tipo]);
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