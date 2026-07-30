<?php

class UnidadJefe extends Model
{
    public function obtenerJefeActual($unidadId)
    {
        $sql = "SELECT u.* 
                FROM unidad_jefe uj
                JOIN usuario u ON u.id = uj.usuario_id
                WHERE uj.unidad_administrativa_id = ? 
                  AND (uj.fecha_fin IS NULL OR uj.fecha_fin >= CURDATE())
                ORDER BY uj.fecha_inicio DESC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        $stmt->execute([$unidadId]);
        return $stmt->fetch();
    }
}