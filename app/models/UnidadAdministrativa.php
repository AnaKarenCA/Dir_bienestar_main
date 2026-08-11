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

    /**
     * Obtiene unidades administrativas por un array de IDs.
     * @param array $ids
     * @return array
     */
    public function obtenerPorIds($ids)
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM unidad_administrativa WHERE id IN ($placeholders) ORDER BY nombre";
        $stmt = $this->db->query($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }
}