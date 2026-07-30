<?php

class Delegacion extends Model
{
    public function obtenerTodas()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Delegacion
            ORDER BY nombre
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }
}