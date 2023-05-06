<?php

function createDbConnection(){
    try{
        $dbcon = new PDO("mysql:host=127.0.0.1;dbname=chinook;charset=utf8","root","");
        return $dbcon;
    }catch( PDOException $e){
        echo $e->getMessage();
    }

    return null;
}
