<?php
// Asegurar variables de sesión
$nombre = $_SESSION['usuario_nombre'] ?? 'Invitado';
$puesto = $_SESSION['usuario_puesto'] ?? '';
$rolId = $_SESSION['usuario_rol_id'] ?? 0;

// Obtener menús desde la base de datos
$menus = [];
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=sistemaactividades;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE activo = 1 ORDER BY orden ASC");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        $rolesPermitidos = array_map('trim', explode(',', $item['roles']));
        if (in_array($rolId, $rolesPermitidos)) {
            $menus[] = $item;
        }
    }
} catch (PDOException $e) {
    $menus = [
        ['titulo' => 'REGISTRO', 'url' => '/Dir_bienestar/dashboard/index'],
        ['titulo' => 'CALENDARIO', 'url' => '/Dir_bienestar/calendario/index'],
        ['titulo' => 'REPORTES', 'url' => '/Dir_bienestar/reporte/index'],
    ];
}

$current = $_SERVER['REQUEST_URI'];
?>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; padding-top:75px; }
    .top-menu {
        position:fixed; top:0; left:0; width:100%; z-index:9999;
        display:flex; align-items:center; justify-content:center; gap:10px;
        background:rgba(255,255,255,0.7); backdrop-filter:blur(10px);
        -webkit-backdrop-filter:blur(10px); padding:12px 20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.1); flex-wrap:wrap;
    }
    .top-menu > a {
        text-decoration:none; color:#000; font-weight:bold;
        padding:12px 16px; border-radius:25px; transition:.25s;
        white-space:nowrap;
    }
    .top-menu > a:hover {
        background:linear-gradient(to bottom,#990321,#6D1426);
        color:white; transform:scale(1.05); box-shadow:0 3px 15px rgba(0,0,0,0.3);
    }
    .top-menu > a.active {
        background:linear-gradient(to bottom,#990321,#6D1426);
        color:white;
    }

    /* ===== SECCIÓN USUARIO CON DROPDOWN ===== */
    .user-section {
        position:relative;
        display:flex; align-items:center; gap:12px;
        margin-left:15px; padding-left:15px;
        border-left:1px solid rgba(0,0,0,0.15);
        cursor:pointer;
        user-select:none;
    }
    .user-section:hover .user-dropdown {
        display:block;
    }
    .user-avatar {
        width:42px; height:42px; border-radius:50%; object-fit:cover;
        border:2px solid #990321;
    }
    .user-info {
        display:flex; flex-direction:column; align-items:flex-start; justify-content:center; line-height:1.2;
    }
    .user-name { font-weight:600; color:#222; font-size:0.9rem; }
    .user-puesto { font-weight:400; color:#555; font-size:0.7rem; margin-top:1px; }

    .logout-btn {
        text-decoration:none; background:linear-gradient(to bottom,#990321,#6D1426);
        color:white; font-weight:bold; padding:8px 14px;
        border-radius:25px; transition:.25s; font-size:0.8rem;
        margin-left:5px;
    }
    .logout-btn:hover { transform:scale(1.05); }

    /* ===== DROPDOWN MENU ===== */
    .user-dropdown {
        display:none;
        position:absolute;
        top:calc(100% + 10px);
        right:0;
        background:white;
        min-width:240px;
        border-radius:16px;
        box-shadow:0 10px 30px rgba(0,0,0,0.2);
        padding:8px 0;
        z-index:10000;
        border:1px solid rgba(0,0,0,0.05);
        animation: fadeSlide 0.2s ease;
    }
    .user-dropdown::before {
        content:'';
        position:absolute;
        top:-8px;
        right:20px;
        width:16px;
        height:16px;
        background:white;
        transform:rotate(45deg);
        border-left:1px solid rgba(0,0,0,0.05);
        border-top:1px solid rgba(0,0,0,0.05);
    }
    .user-dropdown .dropdown-header {
        padding:10px 16px 8px;
        border-bottom:1px solid #eee;
        margin-bottom:4px;
    }
    .user-dropdown .dropdown-header strong {
        display:block;
        color:#222;
        font-size:0.9rem;
    }
    .user-dropdown .dropdown-header span {
        color:#777;
        font-size:0.7rem;
    }
    .user-dropdown a {
        display:flex;
        align-items:center;
        gap:12px;
        padding:10px 16px;
        color:#333;
        text-decoration:none;
        font-size:0.85rem;
        font-weight:500;
        transition:0.1s;
        border-left:3px solid transparent;
    }
    .user-dropdown a:hover {
        background:#f5f0eb;
        border-left-color:#990321;
    }
    .user-dropdown .divider {
        height:1px;
        background:#eee;
        margin:4px 12px;
    }
    .top-menu > a{
        display:flex;
        align-items:center;
        gap:8px;
    }
    .menu-icon{
        font-size:20px;
    }
    @keyframes fadeSlide {
        from { opacity:0; transform:translateY(-8px); }
        to { opacity:1; transform:translateY(0); }
    }

    @media (max-width:850px) {
        .top-menu { padding:8px 10px; gap:5px; }
        .top-menu > a { padding:8px 10px; font-size:.7rem; }
        .user-section { margin-left:5px; padding-left:8px; gap:6px; }
        .user-avatar { width:30px; height:30px; }
        .user-name { font-size:0.7rem; }
        .user-puesto { font-size:0.55rem; }
        .logout-btn { padding:6px 10px; font-size:.7rem; }
        .user-dropdown { right:-10px; min-width:200px; }
    }
</style>
<link rel="stylesheet"
href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:FILL@0" />
<nav class="top-menu">
    <?php foreach ($menus as $item): ?>
        <?php 
            $active = (strpos($current, $item['url']) !== false) ? 'active' : '';
            if ($item['url'] === '#') $active = '';
        ?>
        <a class="<?= $active ?>" href="<?= htmlspecialchars($item['url']) ?>">
            <?php if(!empty($item['icono'])): ?>
                <span class="material-symbols-outlined menu-icon">
                    <?= htmlspecialchars($item['icono']) ?>
                </span>
            <?php endif; ?>
            <?= htmlspecialchars($item['titulo']) ?>
        </a>
    <?php endforeach; ?>

    <!-- SECCIÓN USUARIO (trigger del dropdown) -->
    <div class="user-section">
        <img class="user-avatar" src="/img/user.png" alt="Avatar" 
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%23990321%22/%3E%3Ccircle cx=%2250%22 cy=%2238%22 r=%2212%22 fill=%22%23F9CCA0%22/%3E%3Cpath fill=%22%23FDF3E0%22 d=%22M25,68 Q50,82 75,68 Q68,80 50,84 Q32,80 25,68%22/%3E%3C/svg%3E'">
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($nombre) ?></span>
            <?php if (!empty($puesto)): ?>
                <span class="user-puesto"><?= htmlspecialchars($puesto) ?></span>
            <?php endif; ?>
        </div>
        
        <!-- DROPDOWN -->
        <div class="user-dropdown">
            <!-- Información resumida del usuario -->
            <div class="dropdown-header">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <span><?= htmlspecialchars($puesto) ?></span>
            </div>

            <!-- Opciones según rol -->
            <?php if (in_array($rolId, [2, 5])): // Jefes y Coordinadores ?>
                <a href="/Dir_bienestar/empleados/index">
                    <span class="material-symbols-outlined">groups</span> Empleados a cargo
                </a>
                <a href="/Dir_bienestar/inventario/index">
                    <span class="material-symbols-outlined">inventory_2</span> Inventario de Insumos
                </a>
                <a href="/Dir_bienestar/eventos/revision">
                    <span class="material-symbols-outlined">rate_review</span> Revisión de Eventos
                </a>
                <div class="divider"></div>
            <?php endif; ?>

            <?php if ($rolId == 1): // Administrador ?>
                <a href="/Dir_bienestar/admin/dashboard">
                    <span class="material-symbols-outlined">settings</span> Panel de Administración
                </a>
                <a href="/Dir_bienestar/admin/usuarios">
                    <span class="material-symbols-outlined">group</span> Gestionar Usuarios
                </a>
                <a href="/Dir_bienestar/admin/inventario">
                    <span class="material-symbols-outlined">inventory</span> Inventario Global
                </a>
                <div class="divider"></div>
            <?php endif; ?>

            <!-- Opciones comunes para TODOS -->
            <a href="/Dir_bienestar/usuario/perfil">
                <span class="material-symbols-outlined">account_circle</span> Mi perfil
            </a>
            
            <div class="divider"></div>
            <a href="/Dir_bienestar/auth/logout" style="color:#990321;">
                <span class="material-symbols-outlined">exit_to_app</span> Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSection = document.querySelector('.user-section');
        const dropdown = document.querySelector('.user-dropdown');
        if (userSection && dropdown) {
            // Toggle con click
            userSection.addEventListener('click', function(e) {
                if (!e.target.closest('.logout-btn')) {
                    dropdown.classList.toggle('show');
                    e.stopPropagation();
                }
            });
            // Cerrar al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!userSection.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
</script>

<style>
    /* Desactivar hover para usar solo click */
    .user-section:hover .user-dropdown {
        display: none;
    }
    .user-dropdown.show {
        display: block !important;
    }
</style>