<?php
session_start();
if (isset($_SESSION['ADMIN_CPANEL'])) {
	// Finalmente, destruir la sesión.
	session_destroy();
	
}
?>

<script type="text/javascript">
	window.location="../login.php";
</script>