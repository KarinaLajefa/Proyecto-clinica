<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - FisioClínica</title>
<link rel="stylesheet" href="css/estilos.css">
</head>

<body class="login-body">

<div class="login-container">

<h1>FisioClínica</h1>
<p>Sistema de Gestión</p>

<form action="index.php" method="POST">

<label>Correo electrónico</label>
<input type="email" name="correo" required>

<label>Contraseña</label>
<input type="password" name="password" required>

<label>Tipo de usuario</label>

<select name="rol">

<option value="supervisor">Supervisor</option>
<option value="fisio">Fisioterapeuta</option>
<option value="recepcion">Recepción</option>
<option value="paciente">Paciente</option>

</select>

<button type="submit">Ingresar</button>

</form>

</div>

</body>
</html>