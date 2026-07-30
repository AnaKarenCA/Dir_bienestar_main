<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Panel de Administración</title>

<link rel="stylesheet" href="/css/estilos.css">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

<style>

:root{

--vino:#800000;
--vino2:#9D2449;
--vino3:#611232;

--gris:#f5f5f5;
--gris2:#ececec;
--gris3:#d9d9d9;

--texto:#333;

--blanco:#fff;

--radio:14px;

--sombra:0 5px 16px rgba(0,0,0,.08);

}

*{

box-sizing:border-box;

margin:0;

padding:0;

font-family:Arial,Helvetica,sans-serif;

}

body{

background:#f3f3f3;

color:var(--texto);

}

/*==============================*/

.header-admin{

background:white;

box-shadow:0 2px 10px rgba(0,0,0,.08);

}

.encabezado{

display:flex;

justify-content:space-between;

align-items:center;

padding:18px 40px;

}

.identidad{

display:flex;

align-items:center;

gap:20px;

}

.identidad img{

height:65px;

width:auto;

}

.titulo-sistema h1{

font-size:30px;

color:var(--vino);

margin-bottom:6px;

}

.titulo-sistema p{

color:#777;

font-size:15px;

}

.usuario-admin{

text-align:right;

}

.usuario-admin h3{

color:var(--vino);

margin-bottom:4px;

}

.usuario-admin small{

color:#777;

}

.menu-superior{

background:var(--vino);

padding:0;

}

.menu-superior nav{

display:flex;

align-items:center;

justify-content:center;

}

.menu-superior nav ul{

display:flex;

list-style:none;

}

.menu-superior nav ul li{

margin:0;

}

.menu-superior nav ul li a{

display:block;

padding:15px 24px;

color:white;

text-decoration:none;

font-size:15px;

transition:.3s;

}

.menu-superior nav ul li a:hover{

background:var(--vino3);

}

/*==============================*/

.dashboard{

max-width:1700px;

margin:35px auto;

padding:0 30px;

}

.dashboard h2{

color:var(--vino);

margin-bottom:25px;

font-size:28px;

}

/*==============================*/

.cards{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(230px,1fr));

gap:22px;

margin-bottom:35px;

}

.card{

background:white;

border-radius:var(--radio);

padding:22px;

display:flex;

align-items:center;

gap:18px;

box-shadow:var(--sombra);

transition:.25s;

border-left:7px solid var(--vino);

}

.card:hover{

transform:translateY(-5px);

}

.card .material-symbols-outlined{

font-size:52px;

color:var(--vino);

}

.card h3{

font-size:34px;

margin-bottom:6px;

color:var(--vino);

}

.card p{

color:#666;

font-size:15px;

}

/*==============================*/

.grid{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(550px,1fr));

gap:25px;

}

.panel{

background:white;

border-radius:var(--radio);

box-shadow:var(--sombra);

overflow:hidden;

}

.panel-header{

background:var(--vino);

color:white;

padding:16px 22px;

display:flex;

align-items:center;

gap:10px;

}

.panel-header .material-symbols-outlined{

font-size:26px;

}

.panel-header h3{

font-size:20px;

font-weight:normal;

}

</style>

</head>

<body>

<header class="header-admin">

<div class="encabezado">

<div class="identidad">

<img src="/img/logo_d_bienestar.png" alt="Toluca">

<div class="titulo-sistema">

<h1>Sistema Integral de Actividades</h1>

<p>Panel de Administración</p>

</div>

</div>

<div class="usuario-admin">

<h3>

<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>

</h3>

<small>

Administrador del Sistema

</small>

<br><br>

<small>

<?= date('d/m/Y H:i') ?>

</small>

</div>

</div>

<div class="menu-superior">

<?php require APPROOT.'/views/partials/menu.php'; ?>

</div>

</header>

<div class="dashboard">

<h2>

Resumen General

</h2>

<div class="cards">

<div class="card">

<span class="material-symbols-outlined">

groups

</span>

<div>

<h3>

<?= $indicadores['usuarios_activos'] ?>

</h3>

<p>

Usuarios Activos

</p>

</div>

</div>

<div class="card">

<span class="material-symbols-outlined">

lock_person

</span>

<div>

<h3>

<?= $indicadores['usuarios_bloqueados'] ?>

</h3>

<p>

Usuarios Bloqueados

</p>

</div>

</div>

<div class="card">

<span class="material-symbols-outlined">

badge

</span>

<div>

<h3>

<?= $indicadores['usuarios_totales'] ?>

</h3>

<p>

Total de Usuarios

</p>

</div>

</div>

<div class="card">

<span class="material-symbols-outlined">

apartment

</span>

<div>

<h3>

<?= $indicadores['unidades'] ?>

</h3>

<p>

Unidades Administrativas

</p>

</div>

</div>

<div class="card">

<span class="material-symbols-outlined">

task

</span>

<div>

<h3>

<?= $indicadores['actividades'] ?>

</h3>

<p>

Actividades Programadas

</p>

</div>

</div>

<div class="card">

<span class="material-symbols-outlined">

event_note

</span>

<div>

<h3>

<?= $indicadores['registros'] ?>

</h3>

<p>

Registros de Actividades

</p>

</div>

</div>



<div class="card">

<span class="material-symbols-outlined">

inventory_2

</span>

<div>

<h3>

<?= $indicadores['inventario'] ?>

</h3>

<p>

Inventario Activo

</p>

</div>

</div>

</div>

<div class="grid">
<!-- ===================================================== -->
<!-- ÚLTIMOS USUARIOS -->
<!-- ===================================================== -->

<div class="panel">

    <div class="panel-header">

        <span class="material-symbols-outlined">
            groups
        </span>

        <h3>
            Últimos usuarios registrados
        </h3>

    </div>

    <table class="tabla-admin">

        <thead>

        <tr>

            <th>Nombre</th>

            <th>Correo</th>

            <th>Rol</th>

            <th>Unidad</th>

            <th>Estatus</th>

        </tr>

        </thead>

        <tbody>

        <?php if(empty($ultimosUsuarios)): ?>

            <tr>

                <td colspan="5" class="sin-registros">

                    No existen usuarios registrados.

                </td>

            </tr>

        <?php else: ?>

            <?php foreach($ultimosUsuarios as $u): ?>

                <tr>

                    <td><?= htmlspecialchars($u['nombre']) ?></td>

                    <td><?= htmlspecialchars($u['correo']) ?></td>

                    <td><?= htmlspecialchars($u['tipo_rol']) ?></td>

                    <td><?= htmlspecialchars($u['unidad'] ?? '-') ?></td>

                    <td>

                        <?php

                        $clase="activo";

                        if($u["estatus"]=="Bloqueado")
                            $clase="bloqueado";

                        if($u["estatus"]=="Inactivo")
                            $clase="inactivo";

                        ?>

                        <span class="estado <?= $clase ?>">

                            <?= $u["estatus"] ?>

                        </span>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>



<!-- ===================================================== -->
<!-- ACTIVIDADES -->
<!-- ===================================================== -->

<div class="panel">

    <div class="panel-header">

        <span class="material-symbols-outlined">

            event_note

        </span>

        <h3>

            Últimas actividades registradas

        </h3>

    </div>

    <table class="tabla-admin">

        <thead>

        <tr>

            <th>Fecha</th>

            <th>Código</th>

            <th>Actividad</th>

            <th>Responsable</th>

        </tr>

        </thead>

        <tbody>

        <?php if(empty($ultimasActividades)): ?>

            <tr>

                <td colspan="4" class="sin-registros">

                    No existen actividades.

                </td>

            </tr>

        <?php else: ?>

            <?php foreach($ultimasActividades as $a): ?>

                <tr>

                    <td>

                        <?= date("d/m/Y",strtotime($a["fecha_inicio"])) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($a["codigo"]) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($a["descripcion"]) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($a["nombre"]) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>



<!-- ===================================================== -->
<!-- INVENTARIO -->
<!-- ===================================================== -->

<div class="panel">

    <div class="panel-header">

        <span class="material-symbols-outlined">

            inventory_2

        </span>

        <h3>

            Inventario con bajo stock

        </h3>

    </div>

    <table class="tabla-admin">

        <thead>

        <tr>

            <th>Insumo</th>

            <th>Existencia</th>

            <th>Unidad</th>

            <th>Tipo</th>

        </tr>

        </thead>

        <tbody>

        <?php if(empty($inventarioBajo)): ?>

            <tr>

                <td colspan="4" class="sin-registros">

                    No existen registros.

                </td>

            </tr>

        <?php else: ?>

            <?php foreach($inventarioBajo as $i): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars($i["nombre_insumo"]) ?>

                    </td>

                    <td>

                        <strong>

                            <?= $i["stock_total"] ?>

                        </strong>

                    </td>

                    <td>

                        <?= htmlspecialchars($i["unidad"]) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($i["tipo"]) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</div>



<!-- ===================================================== -->
<!-- ACCESOS RÁPIDOS -->
<!-- ===================================================== -->

<div class="panel-accesos">

    <h2>

        Accesos rápidos

    </h2>

    <div class="grid-accesos">

        <a href="/Dir_bienestar/admin/usuarios">

            <span class="material-symbols-outlined">

                manage_accounts

            </span>

            <strong>

                Gestión de Usuarios

            </strong>

        </a>

        <a href="/Dir_bienestar/admin/inventario">

            <span class="material-symbols-outlined">

                inventory

            </span>

            <strong>

                Inventario Global

            </strong>

        </a>
        <a href="/Dir_bienestar/admin/tipos_entregable">
    <span class="material-symbols-outlined">category</span>
    <strong>Catálogos</strong>
</a>

        <a href="#">

            <span class="material-symbols-outlined">

                apartment

            </span>

            <strong>

                Catálogos

            </strong>

        </a>

        <a href="#">

            <span class="material-symbols-outlined">

                engineering

            </span>

            <strong>

                Asignación de Jefes

            </strong>

        </a>

        

    </div>

</div>
<style>

/*==============================*/
/* TABLAS */
/*==============================*/

.tabla-admin{

width:100%;

border-collapse:collapse;

font-size:14px;

background:#fff;

}

.tabla-admin thead{

background:#800000;

color:white;

}

.tabla-admin thead th{

padding:14px;

font-weight:600;

text-align:left;

letter-spacing:.4px;

}

.tabla-admin tbody td{

padding:13px 14px;

border-bottom:1px solid #ececec;

vertical-align:middle;

}

.tabla-admin tbody tr:nth-child(even){

background:#fafafa;

}

.tabla-admin tbody tr:hover{

background:#f7eeee;

transition:.2s;

}

.sin-registros{

text-align:center;

padding:40px !important;

color:#888;

font-style:italic;

}

/*==============================*/
/* BADGES */
/*==============================*/

.estado{

display:inline-block;

padding:6px 14px;

border-radius:25px;

font-size:12px;

font-weight:bold;

}

.estado.activo{

background:#198754;

color:white;

}

.estado.inactivo{

background:#ffc107;

color:#222;

}

.estado.bloqueado{

background:#dc3545;

color:white;

}

/*==============================*/
/* ACCESOS */
/*==============================*/

.panel-accesos{

margin-top:35px;

background:white;

border-radius:14px;

padding:30px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

}

.panel-accesos h2{

margin-bottom:25px;

color:#800000;

}

.grid-accesos{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(220px,1fr));

gap:22px;

}

.grid-accesos a{

background:#fafafa;

border:1px solid #ececec;

border-radius:14px;

padding:28px;

text-decoration:none;

display:flex;

flex-direction:column;

align-items:center;

justify-content:center;

transition:.25s;

color:#800000;

min-height:150px;

}

.grid-accesos a:hover{

background:#800000;

color:white;

transform:translateY(-6px);

box-shadow:0 8px 18px rgba(0,0,0,.15);

}

.grid-accesos .material-symbols-outlined{

font-size:52px;

margin-bottom:18px;

}

.grid-accesos strong{

text-align:center;

font-size:16px;

line-height:1.4;

}

/*==============================*/
/* SCROLL TABLAS */
/*==============================*/

.panel{

overflow-x:auto;

}

/*==============================*/
/* SCROLLBAR */
/*==============================*/

::-webkit-scrollbar{

width:10px;

height:10px;

}

::-webkit-scrollbar-track{

background:#efefef;

}

::-webkit-scrollbar-thumb{

background:#800000;

border-radius:20px;

}

::-webkit-scrollbar-thumb:hover{

background:#611232;

}

/*==============================*/
/* RESPONSIVE */
/*==============================*/

@media screen and (max-width:1400px){

.grid{

grid-template-columns:1fr;

}

}

@media screen and (max-width:900px){

.encabezado{

flex-direction:column;

align-items:flex-start;

gap:18px;

padding:20px;

}

.usuario-admin{

text-align:left;

}

.cards{

grid-template-columns:repeat(auto-fit,minmax(180px,1fr));

}

.identidad img{

height:55px;

}

.titulo-sistema h1{

font-size:24px;

}

.dashboard{

padding:20px;

}

.menu-superior nav{

overflow-x:auto;

justify-content:flex-start;

}

.menu-superior nav ul{

min-width:900px;

}

}

@media screen and (max-width:700px){

.cards{

grid-template-columns:1fr;

}

.grid-accesos{

grid-template-columns:1fr;

}

.tabla-admin{

font-size:13px;

}

.tabla-admin th,

.tabla-admin td{

padding:10px;

}

.card{

padding:18px;

}

.card h3{

font-size:28px;

}

.card .material-symbols-outlined{

font-size:42px;

}

}

/*==============================*/
/* EFECTOS */
/*==============================*/

.panel{

transition:.25s;

}

.panel:hover{

transform:translateY(-3px);

box-shadow:0 8px 18px rgba(0,0,0,.10);

}

.card{

cursor:default;

}

.card::after{

content:"";

position:absolute;

display:none;

}

/*==============================*/
/* TITULOS */
/*==============================*/

.panel-header{

border-bottom:4px solid #9d2449;

}

.panel-header h3{

letter-spacing:.4px;

}

/*==============================*/
/* LINKS */
/*==============================*/

a{

transition:.25s;

}

/*==============================*/
/* FOOTER */
/*==============================*/

.footer-admin{

margin-top:45px;

padding:20px;

text-align:center;

font-size:13px;

color:#777;

border-top:1px solid #ddd;

}

.footer-admin strong{

color:#800000;

}

</style>

<div class="footer-admin">

<strong>

Sistema Integral de Actividades

</strong>

<br>

Dirección General de Bienestar · Panel de Administración

</div>

</body>

</html>