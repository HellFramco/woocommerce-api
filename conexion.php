<?php
function conectar(){
    $host="localhost";
    $user="root";
    $pass="";

    $bd="drabbaje_app_ventas";

    $con=mysqli_connect($host,$user,$pass);

    mysqli_select_db($con,$bd);

    return $con;
}
?>