<?php
class HistorialCarpeta extends Model
{
    public function guardar($datos)
    {
        $sql = "INSERT INTO historial_carpeta (carpeta_id, usuario_id, accion, estado_anterior, estado_nuevo, comentario, diapositiva)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['carpeta_id'],
            $datos['usuario_id'],
            $datos['accion'],
            $datos['estado_anterior'] ?? null,
            $datos['estado_nuevo'] ?? null,
            $datos['comentario'] ?? null,
            $datos['diapositiva'] ?? null
        ]);
    }

    public function obtenerPorCarpeta($carpetaId)
    {
        $sql = "SELECT h.*, u.nombre AS usuario_nombre
                FROM historial_carpeta h
                JOIN usuario u ON u.id = h.usuario_id
                WHERE h.carpeta_id = ?
                ORDER BY h.fecha DESC";
        $stmt = $this->db->query($sql);
        $stmt->execute([$carpetaId]);
        return $stmt->fetchAll();
    }
}