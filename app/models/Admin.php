<?php

class Admin extends Model
{
    /**
     * ===============================
     * INDICADORES DEL DASHBOARD
     * ===============================
     */

    public function obtenerIndicadores(): array
    {
        return [
            'usuarios_activos'      => $this->contarUsuariosPorEstado('Activo'),
            'usuarios_bloqueados'   => $this->contarUsuariosPorEstado('Bloqueado'),
            'usuarios_totales'      => $this->contarUsuarios(),
            'unidades'              => $this->contarTabla('unidad_administrativa'),
            'actividades'           => $this->contarTabla('actividad_programada'),
            'registros'             => $this->contarTabla('registro_actividad'),
            'eventos'               => $this->contarTabla('evento_detalle'),
            'inventario'            => $this->contarInventarioActivo()
        ];
    }

    /**
     * ===============================
     * USUARIOS
     * ===============================
     */

    private function contarUsuarios(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) total
            FROM usuario
        ");

        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    private function contarUsuariosPorEstado(string $estado): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) total
            FROM usuario
            WHERE estatus = ?
        ");

        $stmt->execute([$estado]);

        return (int)$stmt->fetch()['total'];
    }

    /**
     * ===============================
     * INVENTARIO
     * ===============================
     */

    private function contarInventarioActivo(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) total
            FROM inventario_insumo
            WHERE activo = 1
        ");

        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    /**
     * ===============================
     * TABLAS GENERALES
     * ===============================
     */

    private function contarTabla(string $tabla): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) total
            FROM {$tabla}
        ");

        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    /**
     * ===============================
     * ÚLTIMOS USUARIOS
     * ===============================
     */

    public function obtenerUltimosUsuarios(int $limite = 5): array
    {
        $limite = (int)$limite;

        $stmt = $this->db->query("
            SELECT
                u.nombre,
                u.correo,
                u.estatus,
                r.tipo_rol,
                ua.nombre AS unidad
            FROM usuario u
            INNER JOIN rol r
                ON r.id = u.rol_id
            LEFT JOIN unidad_administrativa ua
                ON ua.id = u.unidad_administrativa_id
            ORDER BY u.id DESC
            LIMIT {$limite}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * ===============================
     * ÚLTIMAS ACTIVIDADES
     * ===============================
     */

    public function obtenerUltimasActividades(int $limite = 5): array
    {
        $limite = (int)$limite;

        $stmt = $this->db->query("
            SELECT
                ra.fecha_inicio,
                ap.codigo,
                ap.descripcion,
                u.nombre
            FROM registro_actividad ra
            INNER JOIN actividad_programada ap
                ON ap.id = ra.actividad_programada_id
            INNER JOIN usuario u
                ON u.id = ra.usuario_id
            ORDER BY ra.fecha_inicio DESC,
                     ra.id DESC
            LIMIT {$limite}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * ===============================
     * ÚLTIMOS EVENTOS
     * ===============================
     */

    public function obtenerUltimosEventos(int $limite = 5): array
    {
        $limite = (int)$limite;

        $stmt = $this->db->query("
            SELECT
                nombre_evento,
                fecha_evento,
                responsable_evento
            FROM evento_detalle
            ORDER BY fecha_evento DESC,
                     id DESC
            LIMIT {$limite}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * ===============================
     * INVENTARIO CON BAJO STOCK
     * ===============================
     */

    public function obtenerInventarioBajo(int $stock = 10): array
    {
        $stmt = $this->db->query("
            SELECT
                nombre_insumo,
                medida,
                unidad,
                stock_total,
                tipo
            FROM inventario_insumo
            WHERE activo = 1
              AND stock_total <= ?
            ORDER BY stock_total ASC,
                     nombre_insumo ASC
        ");

        $stmt->execute([$stock]);

        return $stmt->fetchAll();
    }
}