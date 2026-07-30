<?php

class ActividadProgramada extends Model
{
    public function obtenerPorUnidad($unidadId)
    {
        $stmt = $this->db->query("
            SELECT
                ap.id,
                ap.codigo,
                ap.descripcion,
                um.id AS unidad_medida_id,
                um.nombre AS unidad_medida
            FROM Actividad_programada ap
            INNER JOIN Unidad_de_medida um ON um.id = ap.unidad_medida_id
            WHERE ap.unidad_administrativa_id = ?
            ORDER BY CAST(ap.codigo AS UNSIGNED)
        ");
        $stmt->execute([$unidadId]);
        return $stmt->fetchAll();
    }
    public function obtenerTodasConCodigo()
{
    $stmt = $this->db->query("
        SELECT id, CONCAT(codigo, ' - ', descripcion) as nombre
        FROM Actividad_programada
        ORDER BY codigo
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}
}