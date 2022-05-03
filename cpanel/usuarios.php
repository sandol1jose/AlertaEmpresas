<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<?php
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
    $options = ['limit' => 500];
    $Result = $collection->find([], $options)->toArray();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>

</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<div class="divBackGround">
    <div class="divContenedor">
        <span> 
            <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px"> 
            <b>Usuarios</b>
        </span>

        <h1>Usuarios</h1>
        <span>Estos son los usuarios registrados</span><br><br>

        <?php if($Result != NULL){ ?>
            <table>
                <thead>
                    <tr>
                        <td>Nombre</td>
                        <td>Apellido</td>
                        <td>Email</td>
                        <td>Empresa</td>
                        <td>Alertas</td>
                        <td>Accion</td>
                    </tr>
                </thead>
        
                <tbody>
        <?php foreach($Result as $Cliente){ ?>
        
            
                <tr>
                    <td><?php echo $Cliente["nombres"]; ?></td>
                    <td><?php echo $Cliente["apellidos"]; ?></td>
                    <td><?php echo $Cliente["email"]; ?></td>
                    <td><?php echo $Cliente["empresa"]; ?></td>
                    
                    <?php if(isset($Cliente["alertas"])){ ?>
                        <td class="center"><?php echo count($Cliente["alertas"]); ?></td>
                        <td class="center">
                            <button class="botonVer" onclick="location.href='EmpresasSeguidas.php?email=<?php echo $Cliente['email']; ?>'" >
                                Ver
                            </button>
                        </td>
                    <?php }else{ ?>
                        <td class="center">0</td>
                        <td class="center"></td>
                    <?php } ?>
                </tr>
            
            
        <?php
            }
        }
        ?>
        </tbody>

        </table>
    </div>
</div>
</body>
</html>