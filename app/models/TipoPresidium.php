<?php

class TipoPresidium extends Model
{
public function obtenerTodos()
{
    $stmt = $this->db->query("SELECT * FROM tipo_presidium ORDER BY nombre_tipo");
    $stmt->execute();
    return $stmt->fetchAll();
}
}