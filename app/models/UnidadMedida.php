<?php

class UnidadMedida extends Model
{
    public function obtenerTodas()
    {
        $stmt = $this->db->query("SELECT * FROM Unidad_de_medida ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una unidad de medida por su nombre exacto.
     * @param string $nombre
     * @return array|false
     */
    public function obtenerPorNombre($nombre)
    {
        $stmt = $this->db->query("SELECT * FROM Unidad_de_medida WHERE nombre = ? LIMIT 1");
        $stmt->execute([$nombre]);
        return $stmt->fetch();
    }
}