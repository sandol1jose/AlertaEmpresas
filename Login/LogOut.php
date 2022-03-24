<?php
session_start();
if (isset($_SESSION['Cliente'])) {
	// Finalmente, destruir la sesión.
	session_destroy();
	
}

//Eliminamos las Cookies
setcookie("COOKIE_CLIENTE_EMAIL", "", time() - 3600, "/");
setcookie("COOKIE_CLIENTE_PASS", "", time() - 3600, "/");

session_start();
$_SESSION['Alerta'] = "Logout";
?>

<script type="text/javascript">
	window.location="../index.php";
</script>