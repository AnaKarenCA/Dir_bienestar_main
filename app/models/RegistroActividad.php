<?php

class RegistroActividad extends Model
{
    /**
     * Mapeo de jerarquías de unidades (padre -> hijos)
     * Se usa para que los jefes de área vean las actividades de sus unidades hijas
     */
    private const JERARQUIAS = [
        5  => [6, 7],      // UAP (5) -> DGV (6), DIC (7)
        8  => [11, 9, 10], // DPSAI (8) -> DPS (11), DMV (9), DAI (10)
        15 => [16, 17],    // DAJRCS (15) -> DVIJ (16), DRCS (17)
        12 => [13, 14],    // DCSIND (12) -> DPSC (13), DAGSV (14)
    ];

    /**
     * Guarda un nuevo registro de actividad
     */
    public function guardar($datos)
    {
        $stmt = $this->db->query("
            INSERT INTO Registro_actividad
            (
                usuario_id,
                unidad_administrativa_id,
                fecha_inicio,
                fecha_fin,
                hora_inicio,
                hora_fin,
                lugar_id,
                domicilio_id,
                unidad_medida_id,
                beneficiarios_asistentes,
                descripcion,
                tipo_entregable_id,
                actividad_programada_id
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");
        return $stmt->execute([
            $datos['usuario_id'],
            $datos['unidad_administrativa_id'],
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
            $datos['hora_inicio'],
            $datos['hora_fin'],
            $datos['lugar_id'],
            $datos['domicilio_id'],
            $datos['unidad_medida_id'],
            $datos['beneficiarios_asistentes'],
            $datos['descripcion'],
            $datos['tipo_entregable_id'],
            $datos['actividad_programada_id']
        ]);
    }

    /**
     * Obtiene las unidades que un jefe de área puede ver
     * (su propia unidad + las hijas según jerarquía)
     *
     * @param int $unidadId ID de la unidad del usuario
     * @return array Lista de IDs de unidades permitidas
     */
    public function obtenerUnidadesPermitidas($unidadId)
    {
        $unidades = [$unidadId];
        if (isset(self::JERARQUIAS[$unidadId])) {
            $unidades = array_merge($unidades, self::JERARQUIAS[$unidadId]);
        }
        return $unidades;
    }

    /**
     * Obtiene actividades con filtros y aplica permisos según el usuario
     *
     * @param array      $filters Filtros (year, month, responsable, unidad_id, etc.)
     * @param array|null $usuario Datos del usuario logueado (opcional)
     * @return array Lista de actividades
     */
   public function obtenerConFiltros($filters, $usuario = null)
{
    $sql = "
        SELECT 
            ra.id,
            ra.fecha_inicio AS fecha_inicio,
            ra.fecha_fin AS fecha_fin,
            ra.hora_inicio AS hora_inicio,
            ra.hora_fin AS hora_fin,
            u.nombre AS responsable,
            u.puesto AS puesto_responsable,
            ua.nombre AS unidad_nombre,
            ap.descripcion AS actividad_desc,
            ap.codigo AS actividad_codigo,
            ra.beneficiarios_asistentes AS cantidad,
            ra.descripcion AS descripcion_actividad,
            l.nombre AS lugar_nombre,
            COALESCE(d.nombre, dd.nombre) AS delegacion_nombre,
            sd.nombre AS subdelegacion_nombre,
            CONCAT(dom.calle, ' ', dom.numero_exterior, 
                   IFNULL(CONCAT(' Int. ', dom.numero_interior), '')) AS domicilio_completo,
            cp.cp AS codigo_postal
        FROM Registro_actividad ra
        INNER JOIN Usuario u ON u.id = ra.usuario_id
        INNER JOIN Unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
        LEFT JOIN Actividad_programada ap ON ap.id = ra.actividad_programada_id
        INNER JOIN Lugar l ON l.id = ra.lugar_id
        INNER JOIN Domicilio dom ON dom.id = ra.domicilio_id
        LEFT JOIN Codigo_postal cp ON cp.id = dom.codigo_postal_id
        LEFT JOIN Subdelegacion sd ON sd.id = cp.subdelegacion_id
        LEFT JOIN Delegacion d ON d.id = sd.delegacion_id
        LEFT JOIN Delegacion dd ON dd.id = cp.delegacion_id
        WHERE 1=1
    ";
    $params = [];

    // Filtros básicos
    if (!empty($filters['year']) && !empty($filters['month'])) {
        $sql .= " AND YEAR(ra.fecha_inicio) = ? AND MONTH(ra.fecha_inicio) = ?";
        $params[] = $filters['year'];
        $params[] = $filters['month'];
    }
    if (!empty($filters['fecha_dia'])) {
        $sql .= " AND ra.fecha_inicio = ?";
        $params[] = $filters['fecha_dia'];
    }
    if (!empty($filters['responsable'])) {
        $sql .= " AND u.nombre LIKE ?";
        $params[] = '%' . $filters['responsable'] . '%';
    }
    if (!empty($filters['unidad_id'])) {
        $sql .= " AND ua.id = ?";
        $params[] = $filters['unidad_id'];
    }
    if (!empty($filters['lugar_id'])) {
        $sql .= " AND l.id = ?";
        $params[] = $filters['lugar_id'];
    }
    if (!empty($filters['delegacion_id'])) {
        $sql .= " AND d.id = ?";
        $params[] = $filters['delegacion_id'];
    }
    if (!empty($filters['actividad_id'])) {
        $sql .= " AND ap.id = ?";
        $params[] = $filters['actividad_id'];
    }
    if (!empty($filters['domicilio'])) {
        $sql .= " AND (dom.calle LIKE ? OR dom.numero_exterior LIKE ? OR cp.cp LIKE ?)";
        $params[] = '%' . $filters['domicilio'] . '%';
        $params[] = '%' . $filters['domicilio'] . '%';
        $params[] = '%' . $filters['domicilio'] . '%';
    }

    // ============================================================
    // APLICAR PERMISOS SEGÚN ROL DEL USUARIO
    // ============================================================
    if ($usuario) {
        $rolId = $usuario['rol_id'] ?? null;
        if ($rolId == 3) {
            // Personal: solo sus propias actividades
            $sql .= " AND ra.usuario_id = ?";
            $params[] = $usuario['id'];
        } elseif ($rolId == 2) {
            // Jefe de área: su unidad + hijas + actividades de empleados de su unidad
            $unidadId = $usuario['unidad_administrativa_id'];
            $unidadIds = $this->obtenerUnidadesPermitidas($unidadId);
            
            if (!empty($unidadIds)) {
                $placeholders = implode(',', array_fill(0, count($unidadIds), '?'));
                // Condición A: unidades permitidas (propia + hijas)
                $sql .= " AND (ra.unidad_administrativa_id IN ($placeholders)";
                $params = array_merge($params, $unidadIds);
                
                // Condición B: actividades de empleados (rol=3) que pertenecen a la unidad del jefe
                $sql .= " OR ra.usuario_id IN (SELECT id FROM usuario WHERE unidad_administrativa_id = ? AND rol_id = 3))";
                $params[] = $unidadId;
            } else {
                $sql .= " AND 1=0";
            }
        }
        // Rol 1 (Admin) y 5 (Coordinador) ven todo, no se añaden condiciones
    }

    $sql .= " ORDER BY ra.fecha_inicio ASC, ra.hora_inicio ASC";

    $stmt = $this->db->query($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

    /**
     * Obtiene conteo de actividades por actividad programada para un período
     */
    public function obtenerConteoPorActividad($year, $periodo, $periodoValor, $unidadId = null, $actividadId = null)
    {
        // Construir filtro de fechas según período
        $fechaInicio = null;
        $fechaFin = null;

        switch ($periodo) {
            case 'mensual':
                $mes = $periodoValor;
                $fechaInicio = "$year-$mes-01";
                $fechaFin = date("Y-m-t", strtotime($fechaInicio));
                break;
            case 'trimestral':
                $trimestre = $periodoValor;
                $mesInicio = ($trimestre - 1) * 3 + 1;
                $fechaInicio = "$year-$mesInicio-01";
                $fechaFin = date("Y-m-t", strtotime("$year-" . ($mesInicio + 2) . "-01"));
                break;
            case 'semestral':
                $semestre = $periodoValor;
                $mesInicio = ($semestre - 1) * 6 + 1;
                $fechaInicio = "$year-$mesInicio-01";
                $fechaFin = date("Y-m-t", strtotime("$year-" . ($mesInicio + 5) . "-01"));
                break;
            case 'anual':
                $fechaInicio = "$year-01-01";
                $fechaFin = "$year-12-31";
                break;
            default:
                return [];
        }

        $sql = "
            SELECT 
                ap.descripcion AS actividad,
                COUNT(ra.id) AS total
            FROM Registro_actividad ra
            INNER JOIN Actividad_programada ap ON ap.id = ra.actividad_programada_id
            WHERE ra.fecha_inicio BETWEEN ? AND ?
        ";
        $params = [$fechaInicio, $fechaFin];

        if ($unidadId) {
            $sql .= " AND ra.unidad_administrativa_id = ?";
            $params[] = $unidadId;
        }
        if ($actividadId) {
            $sql .= " AND ra.actividad_programada_id = ?";
            $params[] = $actividadId;
        }

        $sql .= " GROUP BY ap.descripcion";

        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();

        $conteo = [];
        foreach ($result as $row) {
            $conteo[$row['actividad']] = (int)$row['total'];
        }
        return $conteo;
    }

    /**
     * Cuenta los registros de una actividad en un rango de fechas
     */
    public function contarPorActividadYPeriodo($actividadId, $fechaInicio, $fechaFin)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM registro_actividad 
                WHERE actividad_programada_id = ? 
                  AND fecha_inicio BETWEEN ? AND ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$actividadId, $fechaInicio, $fechaFin]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Obtiene registros de actividad a partir de un ID de evento_detalle
     */
    public function obtenerPorEventoDetalleId($eventoDetalleId)
    {
        $sql = "SELECT ra.* FROM registro_actividad ra
                JOIN carpeta c ON c.registro_actividad_id = ra.id
                JOIN evento_detalle ed ON ed.carpeta_id = c.id
                WHERE ed.id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$eventoDetalleId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene registros con datos de carpeta (para listado de eventos)
     */
    public function obtenerRegistrosConCarpeta()
    {
        $sql = "SELECT 
                    ra.*,
                    u.nombre AS usuario_nombre,
                    u.puesto AS usuario_puesto,
                    ua.nombre AS unidad_nombre,
                    ua.objetivo AS unidad_objetivo,
                    ap.descripcion AS actividad_desc,
                    l.nombre AS lugar_nombre,
                    dom.calle,
                    dom.numero_exterior,
                    dom.numero_interior,
                    cp.cp AS codigo_postal,
                    d.nombre AS delegacion_nombre,
                    sd.nombre AS subdelegacion_nombre,
                    c.id AS carpeta_id,
                    c.direccion_entrega,
                    c.fecha_entrega,
                    c.firma,
                    te.nombre_entregable
                FROM registro_actividad ra
                INNER JOIN usuario u ON u.id = ra.usuario_id
                INNER JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = ra.actividad_programada_id
                INNER JOIN lugar l ON l.id = ra.lugar_id
                INNER JOIN domicilio dom ON dom.id = ra.domicilio_id
                LEFT JOIN codigo_postal cp ON cp.id = dom.codigo_postal_id
                LEFT JOIN subdelegacion sd ON sd.id = cp.subdelegacion_id
                LEFT JOIN delegacion d ON d.id = sd.delegacion_id
                LEFT JOIN carpeta c ON c.registro_actividad_id = ra.id
                LEFT JOIN tipo_entregable te ON te.id = ra.tipo_entregable_id
                WHERE ra.tipo_entregable_id = 1
                ORDER BY ra.fecha_inicio DESC";
        $stmt = $this->db->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un registro completo por su ID
     */
    public function obtenerRegistroCompletoPorId($id)
    {
        $sql = "SELECT 
                    ra.*,
                    u.nombre AS usuario_nombre,
                    u.puesto AS usuario_puesto,
                    ua.nombre AS unidad_nombre,
                    ua.objetivo AS unidad_objetivo,
                    ap.descripcion AS actividad_desc,
                    ap.codigo AS actividad_codigo,
                    l.nombre AS lugar_nombre,
                    dom.calle,
                    dom.numero_exterior,
                    dom.numero_interior,
                    cp.cp AS codigo_postal,
                    d.nombre AS delegacion_nombre,
                    sd.nombre AS subdelegacion_nombre
                FROM registro_actividad ra
                INNER JOIN usuario u ON u.id = ra.usuario_id
                INNER JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = ra.actividad_programada_id
                INNER JOIN lugar l ON l.id = ra.lugar_id
                INNER JOIN domicilio dom ON dom.id = ra.domicilio_id
                LEFT JOIN codigo_postal cp ON cp.id = dom.codigo_postal_id
                LEFT JOIN subdelegacion sd ON sd.id = cp.subdelegacion_id
                LEFT JOIN delegacion d ON d.id = sd.delegacion_id
                WHERE ra.id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cuenta registros por tipo de entregable (global)
     */
    public function contarPorTipoEntregable($tipoEntregableId)
    {
        $sql = "SELECT COUNT(*) as total FROM registro_actividad WHERE tipo_entregable_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$tipoEntregableId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Cuenta registros por tipo de entregable y unidad administrativa
     */
    public function contarPorTipoEntregableYUnidad($tipoEntregableId, $unidadId)
    {
        $sql = "SELECT COUNT(*) as total FROM registro_actividad WHERE tipo_entregable_id = ? AND unidad_administrativa_id = ?";
        $stmt = $this->db->query($sql);
        $stmt->execute([$tipoEntregableId, $unidadId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Obtiene registros por tipo de entregable (global)
     */
    public function obtenerPorTipoEntregable($tipoEntregableId)
    {
        $sql = "SELECT 
                    ra.*,
                    u.nombre AS usuario_nombre,
                    u.puesto AS usuario_puesto,
                    ua.nombre AS unidad_nombre,
                    ap.descripcion AS actividad_desc,
                    l.nombre AS lugar_nombre,
                    te.nombre_entregable,
                    c.id AS carpeta_id,
                    c.estado AS carpeta_estado,
                    c.fecha_entrega,
                    c.firma
                FROM registro_actividad ra
                INNER JOIN usuario u ON u.id = ra.usuario_id
                INNER JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = ra.actividad_programada_id
                LEFT JOIN lugar l ON l.id = ra.lugar_id
                INNER JOIN tipo_entregable te ON te.id = ra.tipo_entregable_id
                LEFT JOIN carpeta c ON c.registro_actividad_id = ra.id
                WHERE ra.tipo_entregable_id = ?
                ORDER BY ra.fecha_inicio DESC";
        $stmt = $this->db->query($sql);
        $stmt->execute([$tipoEntregableId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene registros por tipo de entregable y unidad administrativa
     */
    public function obtenerPorTipoEntregableYUnidad($tipoEntregableId, $unidadId)
    {
        $sql = "SELECT 
                    ra.*,
                    u.nombre AS usuario_nombre,
                    u.puesto AS usuario_puesto,
                    ua.nombre AS unidad_nombre,
                    ap.descripcion AS actividad_desc,
                    l.nombre AS lugar_nombre,
                    te.nombre_entregable,
                    c.id AS carpeta_id,
                    c.estado AS carpeta_estado,
                    c.fecha_entrega,
                    c.firma
                FROM registro_actividad ra
                INNER JOIN usuario u ON u.id = ra.usuario_id
                INNER JOIN unidad_administrativa ua ON ua.id = ra.unidad_administrativa_id
                LEFT JOIN actividad_programada ap ON ap.id = ra.actividad_programada_id
                LEFT JOIN lugar l ON l.id = ra.lugar_id
                INNER JOIN tipo_entregable te ON te.id = ra.tipo_entregable_id
                LEFT JOIN carpeta c ON c.registro_actividad_id = ra.id
                WHERE ra.tipo_entregable_id = ? AND ra.unidad_administrativa_id = ?
                ORDER BY ra.fecha_inicio DESC";
        $stmt = $this->db->query($sql);
        $stmt->execute([$tipoEntregableId, $unidadId]);
        return $stmt->fetchAll();
    }
}