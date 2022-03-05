<?php
//Archivo de coneccion para mongodb
require 'mongodb/vendor/autoload.php';

class Conexion{

    public function Conectar(){
        $connection = new MongoDB\Client('mongodb://localhost:27017');
        return $connection->alertaempresas;//seleccionando la base de datos
    }

    public function Insertar($document, $collectionString, $database){
        $collection = $database->$collectionString;//Seleccionando una coleccion
        return $collection->insertOne($document);
    }

    public function Update($Buscador, $documentActualizador, $collectionString, $database){
        $collection = $database->$collectionString;//Seleccionando una coleccion
        return $collection->updateMany($Buscador, $documentActualizador);
    }

    public function Buscar($document, $opciones, $collectionString, $database){
        $collection = $database->$collectionString;//Seleccionando una coleccion
        return $collection->find()->toArray();
    }
}

?>