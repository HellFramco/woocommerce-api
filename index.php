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

            // Numero de productos en la Base de Datos 
            $cantidadProductos = $listaProductosXcantidad['count']; // Variable Principal

            // Numero de paginas de productos	
            $listaProductosPaginas = (array) $listaProductos['http'];
            $response = (array) $listaProductosPaginas['response'];
            $headers = (array) $response['headers'];
            $paginas = $headers['x-wc-totalpages'];
            $paginas = (int)$paginas; // Variable Principal

            // Cantidad de productos en invenrario
            echo 'Numero de productos',$cantidadProductos;
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
    public function updateStockToPrimaryDBSimpleProduct(){

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

        $con=conectar();

        // Importando datos de primariDB
        $sql1 = "SELECT *  FROM stock";
        $query1 =mysqli_query($con,$sql1);
        
        // Contando las filas del stock de primaryDB
        if ($result=mysqli_query($con,$sql1)) {
            $rowcount=mysqli_num_rows($result);
            echo 'Numero de productos en SHTEX: ',$rowcount;
            echo"<br>";
        }

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

        // Obtenemos la lista filtrada del stock de Woocommerce
        for ($i = 1; $i <= $paginas; $i++) { // Vista de productos X paginas

            // variables de control
            $q = 0;
            $objPaginas = ['page' => $i];
            $lista = (array) $woocommerce->get('products',$objPaginas);

            while ($q <= 9) { // Lista de stock Woocommerce filtrada

                // Si algun campo esta vacio rompe el ciclo
                if(empty($lista[$q])){
                    break;
                }

                $a = (array) $lista[$q]; // Variable principal

                // Filtracion de variables
                $idWoocommer = $a['id'];
                $skuWoocommer = $a['sku'];
                $cantidadWoocommer = $a['stock_quantity'];

                // Lista de array filtrada
                $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);

                $q++;
            }
        }

        // Obtenemos la lista filtrada del stock de primaryDB
        while ($stockPrimaryDB =mysqli_fetch_array($query1)){

            // Si algun campo esta vacio rompe el ciclo
            if(empty($query1)){
                break;
            }

            // Filtracion de variables
            $idPrimaryDB = $stockPrimaryDB['id'];
            $skuPrimaryDB = $stockPrimaryDB['sku'];
            $cantidadPrimaryDB = $stockPrimaryDB['cantidad'];

            // Lista de array filtrada
            $primaryDBArray[] = array("$idPrimaryDB", "$skuPrimaryDB","$cantidadPrimaryDB");

        }
        
        $rownVar1 = NULL; // validando arranque de bucle
        $referenciaHallada = 1;

        // Validamos las diferencias desde el stock de Woocommerce hacia, primaryDB
        for($ii = 0; $ii < $cantidadProductos + 1; $ii++){ // Corremos la lista de Woocommerce X SKU
            
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
                        Header("Location: index.php");
            
                    }else {
                        echo"que noob";
                    }
                }
            }

            // Variables de control
            $rownVar = NULL; // Controla si ya a recorrido todo el stock de primaryDB

            // verificamos campos vacios
            if(empty($woocommercerArray[$ii][0])){
                break;
            }

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

                // Realizamos la comparacion

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

                        $sqlud="UPDATE stock SET cantidad='$stockW' WHERE sku='$stockW'";
                        $queryud=mysqli_query($con,$sqlud);

                        if($queryud){
                            echo "Stock actualizado";
                            Header("Location: index.php");
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

}

$a = new APIMetodos();
$a->updateStockToPrimaryDBSimpleProduct();

?>