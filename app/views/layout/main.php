<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Sistema de Bienestar'; ?></title>
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<body>
<div class="dashboard">
    <div class="logos-col">
        <img src="/img/tol.png" alt="Toluca">
    </div>
    <div class="form-col">
        <?php echo $contenido; ?>
    </div>
</div>
<script src="/js/actividades.js"></script>
</body>
</html>