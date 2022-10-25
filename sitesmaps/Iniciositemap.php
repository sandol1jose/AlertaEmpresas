<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargador de Información</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<h1>Generar Site Map</h1>

    <table style="border: 1px solid black">
        <tr>
            <td>Desde</td>
            <td>Hasta</td>
        </tr>
        <tr>
            <td><input type="date" id="desde"></td>
            <td><input type="date" id="hasta"></td>
        </tr>
    </table>

    <button onClick="ProcesarDatos()">Empezar</button>
    <button onClick="Limpiar()">Limpiar</button>
    <br><br>

    <span id="txtSalida"></span>


</body> 
</html>

<script src="https://momentjs.com/downloads/moment.js"></script>
<script>
    
    var contador1 = 0;
    var contador2 = 0;
    var numero = 0;
	function ProcesarDatos(){
        var desde = document.getElementById("desde").value;
        var hasta = document.getElementById("hasta").value;
        var fechaInicio = Date.parse(desde);
        var fechaFin = Date.parse(hasta);
        ProcesarDatosPHP(fechaInicio, fechaFin)
	}

    function ProcesarDatosPHP(fechaInicio, fechaFin){
        $.ajax({
			type: "POST",
			url: "GenerarSiteMap.php",
			data: {'fechaInicio': fechaInicio, 'fechaFin': fechaFin},
			dataType: "html",
			beforeSend: function(){
                //console.log("Estamos procesando los datos.");
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                //console.log(data);
                console.log("listo");
			}
		});
    }

    function Limpiar(){
        document.getElementById("txtSalida").innerHTML = "";
    }
</script>