<?php

class Subdelegacion extends Model
{
    public function obtenerPorDelegacion($delegacionId)
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Subdelegacion
            WHERE delegacion_id = ?
            ORDER BY nombre
        ");

        $stmt->execute([$delegacionId]);

        return $stmt->fetchAll();
    }
}