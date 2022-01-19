<?php

// Cargue de librerias
require __DIR__ . '/vendor/autoload.php';
require_once( 'lib/woocommerce-api.php' );
include("conexion.php");

// Usar los parametros de la API de Woocommerce
use Automattic\WooCommerce\Client;

class APIMetodos{ //Creando clase de metodos

    // Funcion para traer la data del Stock de todos los productos de Woocommerce
    public function getStockToWoocommerceDB(){

        // Iniciando variables de autenticacion
        $options = array(
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => 30,
            'ssl_verify'      => false,
        );

        try {

            // Autenticando 
            $client = new WC_API_Client(
            'https://www.drabbalovers.co/',
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00',
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            $options );

            $woocommerce = new Client(
            'https://www.drabbalovers.co/', 
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00', 
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            [
                'version' => 'wc/v3',
            ]);

            // Inciando methos GET
            $listaProductosXcantidad = (array) $client->products->get_count();
            $listaProductos = (array) $client->products->get();

            // Numero de productos en la Base de Datos Woocommece
            $cantidadProductos = $listaProductosXcantidad['count']; // Variable Principal

            // Importando stock de Woocommerce	
            $listaProductosPaginas = (array) $listaProductos['http']; //Lista de productos JSON
            $response = (array) $listaProductosPaginas['response']; // Metadatos de Lista de productos JSON
            $headers = (array) $response['headers']; // Headers de los Metadatos
            $paginas = $headers['x-wc-totalpages']; // Numero de paginas X productos Woocommerce
            $paginas = (int)$paginas; // Variable numeros de paginas Principal

            // Cantidad de productos en invenrario Woocommerce
            echo 'Numero de productos en Woocommerce: ',$cantidadProductos;
            echo"<br>";

            for ($i = 1; $i <= $paginas; $i++) { // Vista de productos X paginas

                // variables de control
                $q = 0;
                $objPaginas = ['page' => $i];
                $lista = (array) $woocommerce->get('products',$objPaginas);

                while ($q <= 9) { // Vista de productos

                    if(empty($lista[$q])){
                        break;
                    }
                    $a = (array) $lista[$q];

                    echo 'ID:',$a['id'],' - ','SKU:',$a['sku'],' - ','CANTIDAD:',$a['stock_quantity'];
                    echo"<br>";
                    
                    $q++;

                }
                
            }

        }catch ( WC_API_Client_Exception $e ) {

            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;
            
            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
            
                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }
    }
    // Funcion para crear o exportar productos de la base de datos de Woocommerce
    public function exportStockToWoocommerceDB(){

         // Iniciando variables de autenticacion
        $options = array(
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => 30,
            'ssl_verify'      => false,
        );

        try {

            // Autenticando 
            $client = new WC_API_Client(
            'https://www.drabbalovers.co/',
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00',
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            $options );

            $woocommerce = new Client(
            'https://www.drabbalovers.co/', 
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00', 
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            [
                'version' => 'wc/v3',
            ]);

            $client->products->create( array( 'title' => 'Test Product','sku' => '00010', 'type' => 'simple', 'regular_price' => '9.99', 'description' => 'test' ) );

        }catch ( WC_API_Client_Exception $e ) {

            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;
            
            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
            
                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }
    }
    // Funcion para eliminar productos de la base de datos de Woocommerce
    public function deleteStockToWoocommerceDB(){

        // Iniciando variables de autenticacion
        $options = array(
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => 30,
            'ssl_verify'      => false,
        );

        try {

           // Autenticando 
            $client = new WC_API_Client(
            'https://www.drabbalovers.co/',
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00',
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            $options );

            $woocommerce = new Client(
            'https://www.drabbalovers.co/', 
            'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00', 
            'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
            [
                'version' => 'wc/v3',
            ]);

            $client->products->delete( '2345', true );

        }catch ( WC_API_Client_Exception $e ) {

            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;

            if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }
    }
    // Funcion para actualizar productos a la base de datos de Woocommerce
    public function updateStockToWoocommerceDB(){

        // Iniciando variables de autenticacion
        $options = array(
        'debug'           => true,
        'return_as_array' => false,
        'validate_url'    => false,
        'timeout'         => 30,
        'ssl_verify'      => false,
        );

        try {

        // Autenticando 
        $client = new WC_API_Client(
        'https://www.drabbalovers.co/',
        'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00',
        'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
        $options );

        $woocommerce = new Client(
        'https://www.drabbalovers.co/', 
        'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00', 
        'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
        [
            'version' => 'wc/v3',
        ]);

        $client->products->update( '2333', array( 'title' => 'hermoso' ) );

        }catch ( WC_API_Client_Exception $e ) {

        echo $e->getMessage() . PHP_EOL;
        echo $e->getCode() . PHP_EOL;
        
        if ( $e instanceof WC_API_Client_HTTP_Exception ) {
        
            print_r( $e->get_request() );
            print_r( $e->get_response() );
        }
        }
    }
    // funcion para actualizar primaryDB X productos simples
    public function updateStockToPrimaryDB(){

        // Iniciando variables de autenticacion
        $options = array(
        'debug'           => true,
        'return_as_array' => false,
        'validate_url'    => false,
        'timeout'         => 30,
        'ssl_verify'      => false,
        );

        try {

        // Autenticando Bases de datos
        $client = new WC_API_Client(
        'https://www.drabbalovers.co/',
        'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00',
        'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
        $options );

        $woocommerce = new Client(
        'https://www.drabbalovers.co/', 
        'ck_3e6abf70b8566f628bd50ffc70dd38779caefb00', 
        'cs_2a4c74eb3f5ee2e70f57a47d540c9d28c3f19c70',
        [
            'version' => 'wc/v3',
        ]);

        $con=conectar();

        // Importando datos de primariDB
        $sql1 = "SELECT *  FROM inventarios_productos";
        $query1 =mysqli_query($con,$sql1);
        
        // Contando las filas del stock de primaryDB
        if ($result=mysqli_query($con,$sql1)) {

            $rowcount=mysqli_num_rows($result);  // Numero de filas de S-HTEXDB
            echo 'Numero de productos en SHTEX: ', $rowcount; 
            echo"<br>";
        }

        // Inciando methos GET
        $listaProductosXcantidad = (array) $client->products->get_count();
        $listaProductos = (array) $client->products->get();

        // Numero de productos en la Base de Datos Woocommerce
        $cantidadProductos = $listaProductosXcantidad['count'];  // Productos del stock Woocomerce
        echo 'Numero de productos en stock de Woocomerce: ',$cantidadProductos;
        echo"<br>";

        // Importando stock de Woocommerce	
        $listaProductosPaginas = (array) $listaProductos['http'];  //Lista de productos JSON
        $response = (array) $listaProductosPaginas['response'];  // Metadatos de Lista de productos JSON
        $headers = (array) $response['headers'];  // Headers de los Metadatos
        $paginas = $headers['x-wc-totalpages'];  // Numero de paginas X productos Woocommerce
        $paginas = (int)$paginas;  // Variable numeros de paginas Principal

        // Obtenemos la lista filtrada del stock de Woocommerce
        for ($i = 1; $i <= $paginas; $i++) {  // Vista de productos X paginas

            // variables de control
            $q = 0;
            $objPaginas = ['page' => $i];
            $lista = (array) $woocommerce->get('products',$objPaginas);

            while ($q <= 9) {  // Lista de stock Woocommerce filtrada

                // Si algun campo esta vacio rompe el ciclo
                if(empty($lista[$q])){
                    break;
                }

                $a = (array) $lista[$q]; // Variable principal

                $listaProductosPadreVariant = (array) $client->products->get($a['id']);
                $listaProductosVariants = (array) $listaProductosPadreVariant['product'];
                $productosVariant = (array) $listaProductosVariants['variations'];
                $j = 1;

                //print_r($listaProductosPadreVariant);
                //print_r($listaProductosVariants);
                //print_r($productosVariant);

                if(empty($productosVariant[0])){
                
                    // Filtracion de variables
                    $idWoocommer = $a['id'];
                    $skuWoocommer = $a['sku'];
                    $cantidadWoocommer = $a['stock_quantity'];

                    $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);
                    $tallas[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);

                }else{

                    for($h = 0; $h < $j; $h++){

                        $j++;
    
                        if(empty($productosVariant[$h])){
                            break;
                        }

                        $b = $listaProductosVariants['sku'];
                        $a = (array) $productosVariant[$h];

                        $idWoocommer = $a['id'];
                        $skuWoocommer = $a['sku'];
                        $cantidadWoocommer = $a['stock_quantity'];

                        // Lista de array filtrada
                        $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);
                        $woocommercerArrayvariant[] = array($b, $idWoocommer, $skuWoocommer,$cantidadWoocommer);
    
                    }

                    $tallas[] = $woocommercerArrayvariant;
                    $woocommercerArrayvariant = NULL;
                }

                $q++;
            }
        }

        // Obtenemos la lista filtrada del stock de S_HTEX DB
        while ($stockPrimaryDB =mysqli_fetch_array($query1)){

            // Si algun campo esta vacio rompe el ciclo
            if(empty($query1)){
                break;
            }

            // Filtracion de variables
            $idPrimaryDB = $stockPrimaryDB['id_inventario'];
            $skuPrimaryDB = $stockPrimaryDB['referencia'];
            $cantidadT6 = $stockPrimaryDB['talla6'];
            $cantidadT8 = $stockPrimaryDB['talla8'];
            $cantidadT10 = $stockPrimaryDB['talla10'];
            $cantidadT12 = $stockPrimaryDB['talla12'];
            $cantidadT14 = $stockPrimaryDB['talla14'];
            $cantidadT16 = $stockPrimaryDB['talla16'];
            $cantidadT18 = $stockPrimaryDB['talla18'];
            $cantidadT20 = $stockPrimaryDB['talla20'];
            $cantidadT26 = $stockPrimaryDB['talla26'];
            $cantidadT28 = $stockPrimaryDB['talla28'];
            $cantidadT30 = $stockPrimaryDB['talla30'];
            $cantidadT32 = $stockPrimaryDB['talla32'];
            $cantidadT34 = $stockPrimaryDB['talla34'];
            $cantidadT36 = $stockPrimaryDB['talla36'];
            $cantidadT38 = $stockPrimaryDB['talla38'];
            $cantidadTS = $stockPrimaryDB['tallas'];
            $cantidadTM = $stockPrimaryDB['tallam'];
            $cantidadTL = $stockPrimaryDB['tallal'];
            $cantidadTXL = $stockPrimaryDB['tallaxl'];
            $cantidadTU = $stockPrimaryDB['tallau'];
            $cantidadTEST = $stockPrimaryDB['tallaest'];

            // Lista de array filtrada
            $primaryDBArray[] = array("$idPrimaryDB", "$skuPrimaryDB",array("$cantidadT6","$cantidadT8","$cantidadT10","$cantidadT12","$cantidadT14","$cantidadT16","$cantidadT18","$cantidadT20","$cantidadT26","$cantidadT28","$cantidadT30","$cantidadT32","$cantidadT34","$cantidadT36","$cantidadT38","$cantidadTS","$cantidadTM","$cantidadTL","$cantidadTXL","$cantidadTU","$cantidadTEST"));

        }

        //print_r($primaryDBArray);  // Arrays de productos X tallas S_HTEX DB
        //print_r($woocommercerArray);  // Array contador de productos
        //print_r($tallas);  // Array de Productos X Variantes

        // Cantidad de productos en invenrario Woocommerce
        $numeroProductosCompleto = count($woocommercerArray);
        echo 'Numero de productos en Woocommerce: ',$numeroProductosCompleto;
        echo"<br>";
        $listaRecorrida = 0;
        for($i = 0; $i < $cantidadProductos + 1; $i++){ // Corremos la lista de Woocommerce X SKU
            
              
            if($listaRecorrida !== 0){

                if($referenciaHallada == 0){

                    echo 'Referencia inexistente en S-Htex DB';
                    echo "<br>";
                    echo 'Procediendo a actualizar referencia #: ', $skuPadreW;
                    echo "<br>";

                    $sql="INSERT INTO inventarios_productos VALUES ('', '$skuPadreW', '', '', '', '', '', '', '', '', '', '', '', '', '', '$stockTalla6', '$stockTalla8', '$stockTalla10', '$stockTalla12', '$stockTalla14', '$stockTalla16', '$stockTalla18', '$stockTalla20', '$stockTalla26', '$stockTalla28', '$stockTalla30', '$stockTalla32', '$stockTalla34', '$stockTalla36', '$stockTalla38', '$stockTallaS', '$stockTallaM', '$stockTallaL', '$stockTallaXL', '$stockTallaU', '$stockTallaEST', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')";
                    $query= mysqli_query($con,$sql);

                    if($query){

                        echo'Referencia #: ',$skuPadreW,' lista!';
                            
                    }else {
                        echo"noob";
                    }

                }
            }

            if(empty($tallas[$i])){
                break;
            }

            $tallasVariantes = (array)$tallas[$i];
            

            if(empty($tallas[$i][0][0])){
                
            }else{

                $skuPadreW = $tallasVariantes[0][0];

                $stockTalla10 = $tallasVariantes[0][3];
                $stockTalla12 = $tallasVariantes[1][3];
                $stockTalla14 = $tallasVariantes[2][3];
                $stockTalla16 = $tallasVariantes[3][3];
                $stockTalla18 = $tallasVariantes[4][3];
                $stockTalla20 = $tallasVariantes[5][3];
                $stockTalla26 = $tallasVariantes[6][3];
                $stockTalla28 = $tallasVariantes[7][3];
                $stockTalla30 = $tallasVariantes[8][3];
                $stockTalla32 = $tallasVariantes[9][3];
                $stockTalla34 = $tallasVariantes[10][3];
                $stockTalla36 = $tallasVariantes[11][3];
                $stockTalla38 = $tallasVariantes[12][3];
                $stockTalla6 = $tallasVariantes[13][3];
                $stockTalla8 = $tallasVariantes[14][3];
                $stockTallaEST = $tallasVariantes[15][3];
                $stockTallaL = $tallasVariantes[16][3];
                $stockTallaM = $tallasVariantes[17][3];
                $stockTallaS = $tallasVariantes[18][3];
                $stockTallaU = $tallasVariantes[19][3];
                $stockTallaXL = $tallasVariantes[20][3];

                $referenciaHallada = 0;
                $listaRecorrida = 0;

                for($ii = 0; $ii < $rowcount +1; $ii++){

                    if(empty($primaryDBArray[$ii])){
                        break;
                    }

                    $skuDB = $primaryDBArray[$ii][1];

                    if($skuPadreW === $skuDB){

                        $referenciaHallada = 1;
                        
                        $sqlUP="UPDATE inventarios_productos SET talla6='$stockTalla6',talla8='$stockTalla8',talla10='$stockTalla10',talla12='$stockTalla12',talla14='$stockTalla14',talla16='$stockTalla16',talla18='$stockTalla18',talla20='$stockTalla20',talla26='$stockTalla26',talla28='$stockTalla28',talla30='$stockTalla30',talla32='$stockTalla32',talla34='$stockTalla34',talla36='$stockTalla36',talla38='$stockTalla38',tallas='$stockTallaS',tallam='$stockTallaM',tallal='$stockTallaL',tallaxl='$stockTallaXL',tallau='$stockTallaU',tallaest='$stockTallaEST' WHERE referencia ='$skuPadreW'";
                        $queryUP=mysqli_query($con,$sqlUP);

                        if($queryUP){
                            echo "Base de datos al dia!";
                        }else{
                            echo "noob";
                        }
                        



                    }else{
                        
                        if($listaRecorrida != NULL){

                            echo 'Referencia no encontrada #: ',$skuPadreW;
                            echo "<br>";
                            
                        }else{  

                            echo 'Actualizando';
                            echo "<br>";

                        }

                    }

                }

                $listaRecorrida = 1;

            }

        }



        /*
        // Validamos las diferencias desde el stock de Woocommerce hacia, primaryDB
        for($ii = 0; $ii < $numeroProductosCompleto + 1; $ii++){ // Corremos la lista de Woocommerce X SKU
            
            // verificamos si una referencia existe en primaryDB
            if($rownVar1 !== NULL){

                if($referenciaHallada != 1){
        
                }else{
        
                    echo "La referencia #: ",$skuW," No esta en su primaryDB";
                    echo "<br>";

                    

                    $sql="INSERT INTO stock VALUES('','$idW','$skuW','$stockW')";
                    $query= mysqli_query($con,$sql);
                            
                    if($query){
        
                        echo"Producto añadido con exito";

                    }else {
                        echo"que noob";
                    }
                }
            }

            // Variables de control
            $rownVar = NULL; // Controla si ya a recorrido todo el stock de primaryDB

            // verificamos campos vacios
            if(empty($woocommercerArray[$ii])){
                break;
            }

            if(empty($woocommercerArray[$ii][0][0])){
                
                
                echo 'Array producto normal';

                // Obteniendo datos
                $idW = $woocommercerArray[$ii][0];
                $skuW = $woocommercerArray[$ii][1];
                $stockW = $woocommercerArray[$ii][2];
                $rownVar1 = 1; // validando arranque de bucle

                for($qq = 0; $qq <= $rowcount ; $qq++){ // comparamos los SKU de las listas

                    // verificamos campos vacios
                    if(empty($primaryDBArray[$qq][0])){
                        break;
                    }

                    // Obteniendo datos
                    $skuPDB = $primaryDBArray[$qq][1];
                    $stockPDB = (int) $primaryDBArray[$qq][2];

                    // Comprobamos SKU iguales
                    if($skuW === $skuPDB){

                        $rownVar = 1;
                        $referenciaHallada = 2;

                        // Comprobamos Stock diferentes
                        if($stockW !== $stockPDB){

                            echo "El SKU de Woocommerce se ha encontrado en primaryDB!";
                            echo "<br>";
                            echo "Para la referencia #: ",$skuW," -de Woocommerce Tiene la cantidad total de: ",$stockW;
                            echo "<br>";
                            echo "Mientras que para la referencia #: ",$skuPDB," -de PDB Tiene la cantidad total de: ",$stockPDB;
                            echo "<br>";
    
                            $sqlud="UPDATE stock SET cantidad='$stockW' WHERE sku='$skuPDB'";
                            $queryud=mysqli_query($con,$sqlud);
    
                            if($queryud){
                                echo "Stock actualizado";
                            }else{
                                echo "Opp algo salimal que noob";
                            }
    
                            break;
    
                        }else{
    
                            echo "El Stock de Woocommerce se esta actualizando en su PrimaryDB!";
                            echo "<br>";
                            echo "El producto: ",$skuW," esta actualizado!";
                            echo "<br>";
                            break;
                        }
    
                    }else{
    
                        $referenciaHallada = 1;
    
                        // Validacion de referencias
                        if($rownVar !== NULL){
                            
                            echo " Referencia al dia!";
                            echo "<br>";
                            break;
    
                        }else{
    
                            echo "Buscando registros!";
                            echo "<br>";
    
                        }
                    }
                }
            

            }else{
                
            }

        }
        */

        }catch ( WC_API_Client_Exception $e ) {

        echo $e->getMessage() . PHP_EOL;
        echo $e->getCode() . PHP_EOL;
        
            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
        
            print_r( $e->get_request() );
            print_r( $e->get_response() );
        }
        }
    }

}

$a = new APIMetodos();
$a->updateStockToPrimaryDB();

?>