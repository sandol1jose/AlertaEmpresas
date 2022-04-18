var cadena = `6361 - MC INMUEBLES 2022 SL.
Constitución. Comienzo de operaciones: 15.12.21. Objeto social: Artículo 2º La sociedad tiene por objeto social los siguientes: El
Arrendamiento y la Compra y Venta de toda clase de bienes, muebles e inmuebles (rústicos y urbanos) (CNAE 6820). La construcción,
reparación y conservación de toda clase de obras, sus instalaciones y mantenimiento (CNAE 4121). Los se. Domicilio: C/ DEL MAR 45
Esc.1 1ª - EDIFICIO VEGAMAR, OFICINA (VERA). Capital: 600.000,00 Euros. Declaración de unipersonalidad. Socio único:
INVERMARQUEZ 7 SL. Nombramientos. Adm. Unico: MARQUEZ CANO JOSE PEDRO. Datos registrales. T 2163 , F 77, S 8, H
AL 59799, I/A 1 (23.12.21).`;

let PalabrasFase1 = [
    'Constitución.',
    'Declaración de unipersonalidad.',
    'Nombramientos.',
    'Datos registrales.'
]

let PalabrasFase2 = [
    'Comienzo de operaciones:',
    'Objeto social:',
    'Domicilio:',
    'Capital:',
    'Socio único:',
    'Adm. Unico:'
];

var StringFinal = "";
var NumeroBORME = "6361";
var NombreEmpresa = "";

var Primero = NumeroBORME + " - "; //Primera Palabra de un rango determinado
var Ultimo; //Ultima palabra de un rango determinado
for(i=0; i<cadena.length; i++ ){
    StringFinal = StringFinal + cadena[i]; 

    if(StringFinal.includes(Primero) == true){

        for(j=0; j<PalabrasFase1.length; j++){
            if(StringFinal.includes(PalabrasFase1[j]) == true){
                if(Primero != PalabrasFase1[j]){
                    Ultimo = PalabrasFase1[j];
                    var empieza = StringFinal.indexOf(Primero) + Primero.length;
                    var termina = StringFinal.indexOf(Ultimo);
        
                    //SEGUNDA FASE
                    var TextoSeparado1 = "";
                    for(k=empieza; k<termina; k++){
                        TextoSeparado1 = TextoSeparado1 + StringFinal[k];
                        /*
                        for(m=0; m<PalabrasFase2.length; m++){
                            if(TextoSeparado1.includes(PalabrasFase2[m]) == true){
                                Primero2 = PalabrasFase2[m];
                                for(s=0; s<PalabrasFase2.length; s++){
                                    if(TextoSeparado1.includes(PalabrasFase2[s]) == true){
                                        if(Primero2 != PalabrasFase2[s]){
                                            Ultimo2 = PalabrasFase2[s];
                                            var empieza2 = TextoSeparado1.indexOf(Primero2) + Primero2.length;
                                            var termina2 = TextoSeparado1.indexOf(Ultimo2);
                                            var TextoSeparado2 = "";
                                            for(t=empieza2; t<termina2; t++){
                                                TextoSeparado2 = TextoSeparado2 + TextoSeparado1[t];
                                            }
                                            TextoSeparado1 = Ultimo;
                                            console.log(TextoSeparado2.trim());
                                            Primero2 = Ultimo2;
                                            Ultimo2 = "";
                                             
                                        }
                                    }
                                }
                            }
                        }*/
                    }
                    StringFinal = Ultimo;
                    console.log(TextoSeparado1.trim());
                    Primero = Ultimo;
                    Ultimo = "";
                    break;    
                }
            }else if(i+1 == cadena.length){
                var empieza = StringFinal.indexOf(Primero) + Primero.length;
                var termina = StringFinal.length;
                var TextoSeparado1 = "";
                for(k=empieza; k<termina; k++){
                    TextoSeparado1 = TextoSeparado1 + StringFinal[k];
                }
                StringFinal = Ultimo;
                Primero = Ultimo;
                console.log(TextoSeparado1.trim());
                Ultimo = "";
                break;
            }
        }
    }
}

