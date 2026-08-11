<?php

class EventoFotos extends Model
{
    /** Obtiene las fotografías asociadas directamente al evento. */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT id, evento_detalle_id, tipo_foto, ruta_foto
                FROM evento_fotos
                WHERE evento_detalle_id = ?
                ORDER BY id";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    public function guardarPorEventoDetalleYTipo($eventoDetalleId, $tipoFoto, $rutaFoto)
    {
        $delete = $this->db->query("DELETE FROM evento_fotos WHERE evento_detalle_id = ? AND tipo_foto = ?");
        $delete->execute([$eventoDetalleId, $tipoFoto]);

        $insert = $this->db->query("INSERT INTO evento_fotos (evento_detalle_id, tipo_foto, ruta_foto) VALUES (?, ?, ?)");
        return $insert->execute([$eventoDetalleId, $tipoFoto, $rutaFoto]);
    }
}
