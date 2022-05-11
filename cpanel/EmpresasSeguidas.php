<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<?php
    if(!isset($_GET["email"])){
        header('Location: usuarios'); //Agregamos el producto
    }

    
    $Email = $_GET["email"];


    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
    $filter = ['email' => $Email];
    $Result = $collection->findOne($filter);

    $collection2 = $database->empresas;
?>

<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas Seguidas</title>

    <style>
        table{
            width: auto;
        }
    </style>

</head>
<body>


<div class="divBackGround">
    <div class="divContenedor">
    <span> 
        <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">  
        <a href="usuarios.php">Usuarios</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">  
        <b>Empresas Seguidas</b>
    </span>


    <h1>Empresas Seguidas</h1>
    <span>Empresas Seguidas por <b><?php echo $Email; ?></b> </span><br><br>

    <?php 
        if($Result['alertas'] != NULL){ ?>

            <table>
                <thead>
                    <tr>
                        <td>Empresa</td>
                        <td>Estado</td>
                    </tr>
                </thead>
            <tbody>
            
    <?php
            foreach($Result['alertas'] as $alerta){
                $filter2 = ['_id' => new MongoDB\BSON\ObjectID($alerta["id_empresa"])];
                $Result2 = $collection2->findOne($filter2);
                if($Result2 != NULL){ 
                    if($alerta["estado"] == 1) $Estado = "Activa";
                    if($alerta["estado"] == 0) $Estado = "Inactiva";?>

                    <tr>
                        <td><?php echo $Result2["nombre_comercial"]; ?></td>
                        <td class="center"><?php echo $Estado; ?></td>
                    </tr>
    <?php
                }
            }?>
            </tbody>
            </table>
<?php   }
    ?>

    </div>
</div>
</body>
</html>