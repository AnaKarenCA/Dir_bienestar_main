<?php

class Domicilio extends Model
{
    public function crear($datos)
{
    $stmt = $this->db->query("
        INSERT INTO Domicilio (calle, numero_exterior, numero_interior, codigo_postal_id)
        VALUES (?, ?, ?, ?)
    ");
    $result = $stmt->execute([
        $datos['calle'],
        $datos['numero_exterior'],
        $datos['numero_interior'],
        $datos['codigo_postal_id'] // puede ser null
    ]);
    if ($result) {
        return $this->db->getConnection()->lastInsertId();
    }
    return false;
}
public function obtenerPorId($id)
{
    $sql = "SELECT * FROM domicilio WHERE id = ?";
    $stmt = $this->db->query($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}
}