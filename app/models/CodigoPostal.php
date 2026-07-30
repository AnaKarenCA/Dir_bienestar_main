<?php

class CodigoPostal extends Model
{
    public function obtenerPorSubdelegacion($subdelegacionId)
    {
        $stmt = $this->db->query("
            SELECT id, cp
            FROM Codigo_postal
            WHERE subdelegacion_id = ?
            ORDER BY cp
        ");
        $stmt->execute([$subdelegacionId]);
        return $stmt->fetchAll();
    }
public function obtenerPorDelegacion($delegacionId)
{
    $stmt = $this->db->query("
        SELECT cp.id, cp.cp, cp.subdelegacion_id, sd.nombre AS subdelegacion_nombre
        FROM Codigo_postal cp
        LEFT JOIN Subdelegacion sd ON sd.id = cp.subdelegacion_id
        WHERE cp.delegacion_id = ? OR cp.subdelegacion_id IN (
            SELECT id FROM Subdelegacion WHERE delegacion_id = ?
        )
        ORDER BY cp.cp
    ");
    $stmt->execute([$delegacionId, $delegacionId]);
    return $stmt->fetchAll();
}

}