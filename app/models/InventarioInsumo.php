<?php

class InventarioInsumo extends Model
{
    public function obtenerTodos($soloActivos = true)
    {
        $sql = "SELECT * FROM inventario_insumo";
        if ($soloActivos) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre_insumo";
        $stmt = $this->db->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->query("SELECT * FROM inventario_insumo WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function agregar($datos)
    {
        $sql = "INSERT INTO inventario_insumo (nombre_insumo, medida, unidad, stock_total, tipo, activo) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['nombre_insumo'],
            $datos['medida'] ?? null,
            $datos['unidad'] ?? null,
            $datos['stock_total'] ?? 0,
            $datos['tipo'] ?? 'Interno'
        ]);
    }

    public function actualizar($id, $datos)
    {
        $sql = "UPDATE inventario_insumo 
                SET nombre_insumo = ?, medida = ?, unidad = ?, stock_total = ?, tipo = ?
                WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['nombre_insumo'],
            $datos['medida'] ?? null,
            $datos['unidad'] ?? null,
            $datos['stock_total'] ?? 0,
            $datos['tipo'] ?? 'Interno',
            $id
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->query("UPDATE inventario_insumo SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restaurar($id)
    {
        $stmt = $this->db->query("UPDATE inventario_insumo SET activo = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
public function obtenerActivos()
{
    $sql = "SELECT * FROM inventario_insumo WHERE activo = 1 ORDER BY nombre_insumo";
    $stmt = $this->db->query($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
}