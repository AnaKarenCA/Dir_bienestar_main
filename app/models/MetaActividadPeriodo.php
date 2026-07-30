<?php

class MetaActividadPeriodo extends Model
{
    /**
     * Obtiene la meta para una actividad, unidad, año y período específico
     * (suma de los valores de los meses que componen el período)
     */
    public function obtenerMeta($actividadId, $unidadId, $anio, $periodoTipo, $periodoValor)
    {
        // Dependiendo del tipo de período, determinamos qué valores de periodo_valor sumar
        $valores = [];
        if ($periodoTipo === 'mensual') {
            $valores = [$periodoValor];
        } elseif ($periodoTipo === 'trimestral') {
            $mesInicio = ($periodoValor - 1) * 3 + 1;
            $valores = range($mesInicio, $mesInicio + 2);
        } elseif ($periodoTipo === 'semestral') {
            $mesInicio = ($periodoValor - 1) * 6 + 1;
            $valores = range($mesInicio, $mesInicio + 5);
        } elseif ($periodoTipo === 'anual') {
            $valores = range(1, 12);
        }

        if (empty($valores)) {
            return 0;
        }

        // Construir consulta para sumar las metas de esos meses
        $placeholders = implode(',', array_fill(0, count($valores), '?'));
        $sql = "SELECT SUM(meta_cantidad) as total 
                FROM meta_actividad_periodo 
                WHERE actividad_programada_id = ? 
                  AND unidad_administrativa_id = ? 
                  AND anio = ? 
                  AND periodo_tipo = 'mensual' 
                  AND periodo_valor IN ($placeholders)";
        $params = array_merge([$actividadId, $unidadId, $anio], $valores);
        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }
}