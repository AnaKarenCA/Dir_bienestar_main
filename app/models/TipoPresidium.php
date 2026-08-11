<?php

class TipoPresidium extends Model
{
public function obtenerPorId($id)
{
    $stmt = $this->db->query("SELECT * FROM tipo_presidium WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

public function obtenerTodos()
{
    $stmt = $this->db->query("SELECT * FROM tipo_presidium ORDER BY nombre_tipo");
    $stmt->execute();
    return $stmt->fetchAll();
}
}
