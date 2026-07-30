<?php

class TipoEntregable extends Model
{
    public function obtenerTodos()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Tipo_entregable
            ORDER BY nombre_entregable
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Tipo_entregable
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function agregar($nombre)
    {
        $stmt = $this->db->query("
            INSERT INTO Tipo_entregable (nombre_entregable)
            VALUES (?)
        ");
        return $stmt->execute([$nombre]);
    }

    public function actualizar($id, $nombre)
    {
        $stmt = $this->db->query("
            UPDATE Tipo_entregable
            SET nombre_entregable = ?
            WHERE id = ?
        ");
        return $stmt->execute([$nombre, $id]);
    }

    public function contarUsos($id)
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM registro_actividad
            WHERE tipo_entregable_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}