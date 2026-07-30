<?php

class Rol extends Model
{
    public function obtenerTodos()
    {
        $stmt = $this->db->query("SELECT * FROM rol ORDER BY tipo_rol");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function obtenerPorId($id)
    {
        $stmt = $this->db->query("SELECT * FROM rol WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}