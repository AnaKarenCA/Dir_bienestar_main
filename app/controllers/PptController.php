public function getEventData($id) {
    // Obtener el evento_detalle (o registro_actividad) con sus relaciones
    $evento = $this->model('EventoDetalle')->find($id);
    // Obtener carpeta, lugar, domicilio, etc.
    $carpeta = $this->model('Carpeta')->findByEvento($id);
    $registro = $this->model('RegistroActividad')->findByEvento($id);
    $presidium = $this->model('PresidiumAsistente')->getByEvento($id);
    $requerimientos = $this->model('OrdenRequerimiento')->getByEvento($id);
    // ... etc.
    // Construir array con todos los datos formateados
    $data = [
        'nombre_evento' => $evento['nombre_evento'],
        'fecha_inicio' => $registro['fecha_inicio'],
        'fecha_fin' => $registro['fecha_fin'],
        'hora_inicio' => $registro['hora_inicio'],
        'hora_fin' => $registro['hora_fin'],
        'lugar' => $this->getLugarNombre($registro['lugar_id']),
        'domicilio' => $this->getDomicilioCompleto($registro['domicilio_id']),
        // ... y así para cada campo
        'presidium' => $presidium,
        'requerimientos' => $requerimientos,
        // etc.
    ];
    header('Content-Type: application/json');
    echo json_encode($data);
}