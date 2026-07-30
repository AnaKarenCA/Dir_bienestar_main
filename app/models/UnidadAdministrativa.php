<?php

class UnidadAdministrativa extends Model
{
    public function obtenerTodas()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Unidad_administrativa
            ORDER BY nombre
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function obtenerPorId($id)
{
    $sql = "SELECT * FROM unidad_administrativa WHERE id = ?";
    $stmt = $this->db->query($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}
}