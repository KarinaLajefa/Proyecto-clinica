<?php
session_start();
session_unset();    // Limpia todas las variables
session_destroy();  // Destruye la sesión por completo

// Redirige al inicio (que mostrará el login)
header("Location: index.php");
exit;
?>