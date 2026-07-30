<?php

class MenuItems extends Model
{
    /**
     * Obtiene los elementos del menú según el rol del usuario
     * @param int $rolId ID del rol del usuario
     * @return array
     */
    public function obtenerPorRol($rolId)
    {
        $stmt = $this->db->query("
            SELECT *
            FROM menu_items
            WHERE activo = 1
              AND FIND_IN_SET(?, roles)
            ORDER BY orden ASC
        ");
        $stmt->execute([$rolId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los elementos del menú (sin filtrar por rol)
     */
    public function obtenerTodos()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM menu_items
            WHERE activo = 1
            ORDER BY orden ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}