<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargador de Información</title>
</head>
<body>
   <!--<form action="BuscarInformación.php" method="POST">-->

        <table style="border: 1px solid black">
            <tr>
                <td>Desde</td>
                <td>Hasta</td>
            </tr>
            <tr>
                <td><input type="date" name="desde"></td>
                <td><input type="date" name="hasta"></td>
            </tr>
        </table>

        <button onClick="ProcesarDatos()">Empezar</button>

    <!--</form>-->

</body> 
</html>

<script>
    function ProcesarDatos(){
        console.log("hola que tal");
    }
</script>