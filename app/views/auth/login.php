<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>

<style>*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Century Gothic, Arial, sans-serif;
}

body{
    min-height:100vh;

    background:
        linear-gradient(
            rgba(0,0,0,.35),
            rgba(0,0,0,.35)
        ),
        url('img/Toluca-valor.jpg');

    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;

    display:flex;
    justify-content:center;
    align-items:center;
}

.contenedor-login{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

form{
    width:100%;
    max-width:450px;

    background-color:rgba(56,27,27,.75);

    padding:40px 30px;
    border-radius:10px;

    backdrop-filter:blur(4px);

    box-shadow:0 10px 25px rgba(0,0,0,.3);
}

h1{
    color:#fff;
    text-align:center;
    margin-bottom:25px;
    font-size:28px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:15px;

    border:none;
    border-radius:5px;

    font-size:16px;
    text-align:left;
}

button{
    width:100%;
    padding:12px;

    border:none;
    border-radius:5px;

    background:#800000;
    color:#fff;

    font-size:16px;
    cursor:pointer;

    transition:.3s;
}

button:hover{
    background:#c1121f;
}

.error{
    width:100%;
    margin-bottom:15px;

    padding:12px;

    background:#b71c1c;
    color:#fff;

    text-align:center;
    border-radius:5px;
}

@media(max-width:768px){

    form{
        max-width:95%;
        padding:30px 20px;
    }

    h1{
        font-size:22px;
    }
}</style>
</head>
<body>

<div class="contenedor-login">

    <form method="POST">

        <h1>Dirección General de Bienestar</h1>

        <?php if(isset($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <input
            type="email"
            name="correo"
            placeholder="Correo electrónico"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required
        >

        <button type="submit">
            Iniciar Sesión
        </button>

    </form>

</div>

</body>
</html>