<?php

class Usuario extends Model
{
    // ==============================
    // MÉTODOS DE AUTENTICACIÓN Y BÚSQUEDA
    // ==============================

    /**
     * Buscar usuario por correo electrónico
     */
    public function buscarPorCorreo($correo)
    {
        $stmt = $this->db->query("
            SELECT *
            FROM Usuario
            WHERE correo = ?
            LIMIT 1
        ");
        $stmt->execute([$correo]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por ID con datos relacionados (rol y unidad)
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->db->query("
            SELECT
                u.*,
                r.tipo_rol,
                ua.nombre AS unidad
            FROM Usuario u
            INNER JOIN Rol r ON r.id = u.rol_id
            LEFT JOIN Unidad_administrativa ua ON ua.id = u.unidad_administrativa_id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los usuarios (con rol y unidad)
     */
    public function obtenerTodos()
    {
        $stmt = $this->db->query("
            SELECT u.*, r.tipo_rol, ua.nombre AS unidad_nombre
            FROM usuario u
            INNER JOIN rol r ON r.id = u.rol_id
            LEFT JOIN unidad_administrativa ua ON ua.id = u.unidad_administrativa_id
            ORDER BY u.nombre
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuarios de una unidad específica (con opción de excluir un ID)
     */
    public function obtenerPorUnidad($unidadId, $excluirId = null)
    {
        $sql = "SELECT u.*, r.tipo_rol 
                FROM usuario u 
                INNER JOIN rol r ON r.id = u.rol_id 
                WHERE u.unidad_administrativa_id = ?";
        $params = [$unidadId];
        
        if ($excluirId) {
            $sql .= " AND u.id != ?";
            $params[] = $excluirId;
        }
        
        $sql .= " ORDER BY u.nombre";
        
        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==============================
    // MÉTODOS PARA ADMINISTRACIÓN
    // ==============================

    /**
     * Obtener usuarios con filtros, paginación y ordenamiento
     */
    public function obtenerConFiltros($filtros)
    {
        $sql = "SELECT u.*, r.tipo_rol, ua.nombre AS unidad_nombre
                FROM usuario u
                INNER JOIN rol r ON r.id = u.rol_id
                LEFT JOIN unidad_administrativa ua ON ua.id = u.unidad_administrativa_id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
            $params[] = '%' . $filtros['busqueda'] . '%';
            $params[] = '%' . $filtros['busqueda'] . '%';
        }

        if (!empty($filtros['rol_id'])) {
            $sql .= " AND u.rol_id = ?";
            $params[] = $filtros['rol_id'];
        }

        if (!empty($filtros['unidad_id'])) {
            $sql .= " AND u.unidad_administrativa_id = ?";
            $params[] = $filtros['unidad_id'];
        }

        if (!empty($filtros['estatus'])) {
            $sql .= " AND u.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        $orden = $filtros['orden'] ?? 'u.nombre';
        $direccion = $filtros['direccion'] ?? 'ASC';
        $sql .= " ORDER BY $orden $direccion";

        if (isset($filtros['limite']) && isset($filtros['offset'])) {
            $limite = (int)$filtros['limite'];
            $offset = (int)$filtros['offset'];
            $sql .= " LIMIT $limite OFFSET $offset";
        }

        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar total de usuarios con filtros (para paginación)
     */
    public function contarConFiltros($filtros)
    {
        $sql = "SELECT COUNT(*) as total
                FROM usuario u
                INNER JOIN rol r ON r.id = u.rol_id
                LEFT JOIN unidad_administrativa ua ON ua.id = u.unidad_administrativa_id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
            $params[] = '%' . $filtros['busqueda'] . '%';
            $params[] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['rol_id'])) {
            $sql .= " AND u.rol_id = ?";
            $params[] = $filtros['rol_id'];
        }
        if (!empty($filtros['unidad_id'])) {
            $sql .= " AND u.unidad_administrativa_id = ?";
            $params[] = $filtros['unidad_id'];
        }
        if (!empty($filtros['estatus'])) {
            $sql .= " AND u.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear($datos)
    {
        $hash = password_hash($datos['clave'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuario (nombre, correo, clave, puesto, rol_id, unidad_administrativa_id, estatus)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $hash,
            $datos['puesto'] ?? null,
            $datos['rol_id'],
            $datos['unidad_administrativa_id'] ?? null,
            $datos['estatus'] ?? 'Activo'
        ]);
    }

    /**
     * Actualizar usuario (sin contraseña)
     */
    public function actualizarUsuario($id, $datos)
    {
        $sql = "UPDATE usuario SET nombre = ?, correo = ?, puesto = ?, rol_id = ?, unidad_administrativa_id = ?, estatus = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['puesto'] ?? null,
            $datos['rol_id'],
            $datos['unidad_administrativa_id'] ?? null,
            $datos['estatus'],
            $id
        ]);
    }

    /**
     * Cambiar contraseña de un usuario (por administrador)
     */
    public function cambiarContrasenaAdmin($id, $nueva)
    {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET clave = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Bloquear usuario
     */
    public function bloquear($id)
    {
        $sql = "UPDATE usuario SET estatus = 'Bloqueado' WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Desbloquear usuario
     */
    public function desbloquear($id)
    {
        $sql = "UPDATE usuario SET estatus = 'Activo' WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$id]);
    }

    // ==============================
    // MÉTODOS PARA PERFIL DE USUARIO
    // ==============================

    /**
     * Actualizar perfil del usuario (nombre, correo, puesto)
     */
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE usuario SET nombre = ?, correo = ?, puesto = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$datos['nombre'], $datos['correo'], $datos['puesto'], $id]);
    }

    /**
     * Cambiar contraseña (para el propio usuario)
     */
    public function cambiarContrasena($id, $nueva)
    {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET clave = ? WHERE id = ?";
        $stmt = $this->db->query($sql);
        return $stmt->execute([$hash, $id]);
    }
/**
 * Obtiene las unidades administrativas accesibles para este usuario.
 *
 * @param int|null $id Si no se pasa, usa el ID del usuario actual.
 * @return array|null
 */
public function obtenerUnidadesAccesibles($id = null)
{
    if ($id === null) {
        $id = $this->id ?? 0;
    }
    $usuario = $this->obtenerPorId($id);
    if (!$usuario) return [];
    // Necesitamos la conexión. La obtenemos de la propiedad $this->db del modelo.
    // Como estamos en el modelo Usuario, podemos usar $this->db.
    return PermissionHelper::getUnidadesAccesibles($this->db, $usuario);
}
/**
 * Devuelve la conexión a la base de datos.
 * @return Database
 */
public function getDb()
{
    return $this->db;
}
}