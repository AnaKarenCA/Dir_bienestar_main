<?php
class Notificacion extends Model
{
    public function crear($datos)
    {
        $sql = "INSERT INTO notificaciones (usuario_id, carpeta_id, mensaje) VALUES (?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$datos['usuario_id'], $datos['carpeta_id'], $datos['mensaje']]);
    }
    public function noLeidas($usuarioId)
    {
        $sql = "SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = ? AND leida = 0";
        $stmt = $this->db->query($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetch()['total'] ?? 0;
    }
    // ... otros métodos
}