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
        var fechaInicio = Date.parse(desde) / 1000;
        var fechaFin = Date.parse(hasta) / 1000;
        contador1 = 0;
        contador2 = 0;

        for(var i=fechaInicio; i<=fechaFin; i+=86400){
            var date = new Date(i * 1000);
            var dia = date.getDay();
            if(dia != 5 && dia != 6){//Verificando que no sea Sabado o Domingo
                var date2 = new Date((i+86400) * 1000);
                var fecha = moment(date2).format('YYYYMMDD');
                ProcesarDatosPHP(fecha);
            }
        }
	}

    function ProcesarDatosPHP(fecha){
        //var Departamento = $('#Departamento').val();
        $.ajax({
			type: "POST",
			url: "BuscarInformacion.php?tipo=1",
			data: {'fecha': fecha},
			dataType: "html",
			beforeSend: function(){
                contador1++;
                console.log(contador1 + " - Estamos procesando los datos... " + fecha);
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                /*if(data != ""){
                    console.log(data);
                    numero = numero + parseInt(data);
                }*/
                var dataActual = document.getElementById("txtSalida").innerHTML;
                document.getElementById("txtSalida").innerHTML = (dataActual + "<br> " + data);
                contador2++;
                console.log(contador2 + " - terminamos " + fecha);  
			}
		});
    }

    function Limpiar(){
        document.getElementById("txtSalida").innerHTML = "";
    }
</script>