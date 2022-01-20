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

        // Autenticando Base de datos S-HTEX
        $con=conectar();

        // Importando datos de S-HTEX DB
        $sql1 = "SELECT *  FROM inventarios_productos";
        $query1 =mysqli_query($con,$sql1);

        // Contando las filas del stock de S-HTEX DB
        if ($result=mysqli_query($con,$sql1)) {

            // Numero de filas de S-HTEXDB
            $rowcount=mysqli_num_rows($result);
            echo 'Numero de productos en SHTEX: ', $rowcount; 
            echo"<br>";
        }

        // Inciando methos GET
        $listaProductosXcantidad = (array) $client->products->get_count();
        $listaProductos = (array) $client->products->get();

        // Numero de productos en la Base de Datos Woocommerce
        $cantidadProductos = $listaProductosXcantidad['count'];
        echo 'Numero de referencias de Woocomerce: ',$cantidadProductos;
        echo"<br>";

        // Importando stock de Woocommerce	
        $listaProductosPaginas = (array) $listaProductos['http'];         //Lista de productos JSON
        $response = (array) $listaProductosPaginas['response'];           // Metadatos de Lista de productos JSON
        $headers = (array) $response['headers'];                          // Headers de los Metadatos
        $paginas = $headers['x-wc-totalpages'];                           // Numero de paginas X productos Woocommerce
        $paginas = (int)$paginas;                                         // Variable numeros de paginas Principal

        // Obtenemos la lista filtrada del STOCk de Woocommerce
        for ($i = 1; $i <= $paginas; $i++) {                              // Vista de productos X paginas

            // variables de control
            $q = 0;
            $objPaginas = ['page' => $i];                                 // Numero de paginas disponibles
            $lista = (array) $woocommerce->get('products',$objPaginas);   // Lista completa STOCK Woocommerce

            // Lista de stock Woocommerce filtrada
            while ($q <= 9) {

                // Si algun campo esta vacio rompe el ciclo
                if(empty($lista[$q])){
                    break;
                }

                $producto = (array) $lista[$q];                                   // Lista principal

                $SKUProductosWoocommerce[] = (array) $producto['sku'];

                // Obteniendo datos
                $listaProductosPadreVariant1 = (array) $client->products->get($producto['id']);
                $listaProductosVariants[] = (array) $listaProductosPadreVariant1['product'];        // Lista de Productos
                
                $listaProductosPadreVariant = (array) $client->products->get($producto['id']);
                $listaProductosVariants = (array) $listaProductosPadreVariant['product']; 
                $productosVariant[] = (array) $listaProductosVariants['variations'];                // Lista de variantes
                
                $j = 1;                                                                             // Variable de control

                // Separar los proctos simples de las variantes
                if(empty($productosVariant[0])){
                
                    // Filtracion de variables
                    $idWoocommer = $producto['id'];                            // ID referencia Producto Normal Woocommerce                            
                    $skuWoocommer = $producto['sku'];                          // SKU referencia Producto Normal Woocommerce
                    $cantidadWoocommer = $producto['stock_quantity'];          // STOCK referencia Producto Normal Woocommerce

                    // Creando matriz de objetos Arrays
                    $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);   // Productos Simples 
                    $tallas[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);              // Segmentando Tallas

                }else{

                    // Sustrayendo variables
                    for($h = 0; $h < $j; $h++){
                        $j++;                                               // Variable de control

                        // Si ya no hay mas Variantes rompe
                        if(empty($productosVariant[$h])){
                            break;
                        }

                        // Definiendo datos
                        $intoVariacionXProducto = (array) $productosVariant[$h]; 
                        
                        // SKU producto padre
                        
                        $idPadre = $listaProductosVariants['id'];
                        $SKUPabre = $listaProductosVariants['sku'];
                        // Agregando matrices pendientes
                        unset($woocommercerArrayvariant);
                        
                        for($hh = 0; $hh < 21; $hh++){

                            // Si ya no hay mas Variantes rompe
                            if(empty($intoVariacionXProducto[$hh])){
                                break;
                            }

                            $intoVariacionXProductoDates = (array) $intoVariacionXProducto[$hh];

                            $idWoocommer =  $intoVariacionXProductoDates['id'];                         // ID producto variante
                            $skuWoocommer =  $intoVariacionXProductoDates['sku'];                       // SKU producto variante
                            $cantidadWoocommer =  $intoVariacionXProductoDates['stock_quantity'];       // STOCK producto variante
                            $precioWoocommer =  $intoVariacionXProductoDates['regular_price'];          // PRECIO producto variante
                            // Creando matriz de objetos Arrays

                            $woocommercerArray[] = array($idPadre,$SKUPabre, $idWoocommer, $skuWoocommer,$cantidadWoocommer,$precioWoocommer);  // Matriz contadora
                            $woocommercerArrayvariant[] = array($idPadre,$idWoocommer,$SKUPabre,$skuWoocommer,$cantidadWoocommer,$precioWoocommer);// Matriz de variantes
    
                        }

                        
                    }
                    
                    $tallas[] = $woocommercerArrayvariant;                    // Agregando matrices pendientes
                    unset($woocommercerArrayvariant);
                        // Reseteando Matriz
                }

                $q++;                                                         // Variante de control
            }
        }

        // Obtenemos la lista filtrada del stock de S_HTEX DB
        while ($stockPrimaryDB =mysqli_fetch_array($query1)){              // Vista de productos S_HTEX DB

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

            // Generando matriz de productos
            $primaryDBArray[] = array("$idPrimaryDB", "$skuPrimaryDB",array("$cantidadT6","$cantidadT8","$cantidadT10","$cantidadT12","$cantidadT14","$cantidadT16","$cantidadT18","$cantidadT20","$cantidadT26","$cantidadT28","$cantidadT30","$cantidadT32","$cantidadT34","$cantidadT36","$cantidadT38","$cantidadTS","$cantidadTM","$cantidadTL","$cantidadTXL","$cantidadTU","$cantidadTEST"));

        }


        //print_r($tallas);                                            // Lista datos filtrados Tallas X SKU's' X STOCK X ID's X price Woocommerce
        //print_r($primaryDBArray);                                    // Tallas de productos variantes S_HTEX DB

        //print_r($listaProductosVariants);                            // Lista Productos Woocommerce
        //print_r($productosVariant);                                  // Lista de variantes Woocommerce
        //print_r($woocommercerArray);   Arreglar                              // Contador Tallas X productos Woocommerce
        //print_r($listaProductosPadreVariant);                        // Vista Todo Woocommerce        
        //print_r($SKUProductosWoocommerce);                           // Lista de SKU de Woocommerce

        //$rowcount;                                                   // Numero de filas en S_HTEX DB
        //$cantidadProductos                                           // Numero de filas en Woocommerce


        // Cantidad de productos en invenrario Woocommerce
        $numeroProductosCompleto = count($woocommercerArray);              // Numero completo productos en Woocommerce
        //echo 'Numero de productos en Woocommerce: ',$numeroProductosCompleto;
        //echo"<br>";
        
        // Iniciando comprobasiones de STOCK
        $SKU1XSKU2 = 0;
        $VUELTA = 0;
        for($i = 0; $i< $rowcount +1; $i++){


            if($VUELTA != 0 ){

                if($SKU1XSKU2 == 0){
                    
                    if(empty($SKUWoocommerce)){
                        break;
                    }

                    echo 'El codigo #: ', $SKUPrimaryDB, ' No existe en Woocommerce';
                    echo "<br>";

                    $data = [
                        'name' => 'test',
                        'type' => 'variable',
                        'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
                        'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
                        'categories' => [
                        ],
                        'images' => [
                        ],
                        'attributes' => [
                            [
                                'name' => 'Size',
                                'position' => 1,
                                'visible' => true,
                                'variation' => true,
                                'options' => [
                                    'talla10',
                                    'talla12',
                                    'talla14',
                                    'talla16',
                                    'talla18',
                                    'talla20',
                                    'talla26',
                                    'talla28',
                                    'talla30',
                                    'talla32',
                                    'talla34',
                                    'talla36',
                                    'talla38',
                                    'talla6',
                                    'talla8',
                                    'tallaest',
                                    'tallal',
                                    'tallam',
                                    'tallas',
                                    'tallau',
                                    'tallaxl'
                                ]
                            ]
                        ],
                        'default_attributes' => [
                            [
                                'name' => 'Size',
                                'option' => 'S'
                            ]
                        ]
                    ];

                    if(empty(print_r($woocommerce->post('products', $data)))){

                        echo 'La referencia no se agrego a Woocommerce';
                        echo "<br>";

                    }else{

                        echo 'La referencia se agrego a Woocommerrce Procediendo a Udtatearla';
                        echo "<br>";

                        

                    }

                    break;
                }

            }

            $SKUPrimaryDB = $primaryDBArray[$i][1];    // SKU REFERENCIAS CORRIDAS PrimaryDB

            $SKU1XSKU2 = 0;
            for($ii=0; $ii < $cantidadProductos; $ii++){

                if(empty($tallas[$ii][0][2])){
                    break;
                }

                $ggg = (array) $tallas[$ii][0][2];        // SKU REFERENCIAS CORRIDAS Woocommerce
                $SKUWoocommerce = $ggg[2];

                if($SKUPrimaryDB === $SKUWoocommerce){

                    $SKU1XSKU2 = 1;

                    break;

                }else{
                    

                }
            }
            $VUELTA = 1;
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
    // funcion para actualizar primaryDB X STOCk Woocommerce
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

        // Autenticando Base de datos Woocommerce
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

        // Autenticando Base de datos S-HTEX
        $con=conectar();

        // Importando datos de S-HTEX DB
        $sql1 = "SELECT *  FROM inventarios_productos";
        $query1 =mysqli_query($con,$sql1);

        // Contando las filas del stock de S-HTEX DB
        if ($result=mysqli_query($con,$sql1)) {

            // Numero de filas de S-HTEXDB
            $rowcount=mysqli_num_rows($result);
            echo 'Numero de referencias en SHTEX: ', $rowcount; 
            echo"<br>";
        }

        // Inciando methos GET
        $listaProductosXcantidad = (array) $client->products->get_count();
        $listaProductos = (array) $client->products->get();

        // Numero de productos en la Base de Datos Woocommerce
        $cantidadProductos = $listaProductosXcantidad['count'];
        echo 'Numero de referencias encontradas de Woocomerce: ',$cantidadProductos;
        echo"<br>";

        // Importando stock de Woocommerce	
        $listaProductosPaginas = (array) $listaProductos['http'];         //Lista de productos JSON
        $response = (array) $listaProductosPaginas['response'];           // Metadatos de Lista de productos JSON
        $headers = (array) $response['headers'];                          // Headers de los Metadatos
        $paginas = $headers['x-wc-totalpages'];                           // Numero de paginas X productos Woocommerce
        $paginas = (int)$paginas;                                         // Variable numeros de paginas Principal

        // Obtenemos la lista filtrada del STOCk de Woocommerce
        for ($i = 1; $i <= $paginas; $i++) {                              // Vista de productos X paginas

            // variables de control
            $q = 0;
            $objPaginas = ['page' => $i];                                 // Numero de paginas disponibles
            $lista = (array) $woocommerce->get('products',$objPaginas);   // Lista completa STOCK Woocommerce

            // Lista de stock Woocommerce filtrada
            while ($q <= 9) {

                // Si algun campo esta vacio rompe el ciclo
                if(empty($lista[$q])){
                    break;
                }

                $a = (array) $lista[$q];                                   // Variable principal

                // Obteniendo datos
                $listaProductosPadreVariant = (array) $client->products->get($a['id']);     // Lista de referencias
                $listaProductosVariants = (array) $listaProductosPadreVariant['product'];   // Lista de variantes X producto
                $productosVariant = (array) $listaProductosVariants['variations'];          // Lista de variantes
                $j = 1;                                                                     // Variable de control

                //print_r($listaProductosPadreVariant);                      // Vista Todo
                //print_r($listaProductosVariants);                          // Vista de productos
                //print_r($productosVariant);                                // Vista de variantes

                // Separar los proctos simples de las variantes
                if(empty($productosVariant[0])){
                
                    // Filtracion de variables
                    $idWoocommer = $a['id'];                            // ID referencia Producto Normal Woocommerce                            
                    $skuWoocommer = $a['sku'];                          // SKU referencia Producto Normal Woocommerce
                    $cantidadWoocommer = $a['stock_quantity'];          // STOCK referencia Producto Normal Woocommerce

                    // Creando matriz de objetos Arrays
                    $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);   // Productos Simples 
                    $tallas[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);              // Segmentando Tallas

                }else{

                    // Sustrayendo variables
                    for($h = 0; $h < $j; $h++){

                        $j++;                                               // Variable de control

                        // Si ya no hay mas Variantes rompe
                        if(empty($productosVariant[$h])){
                            break;
                        }

                        // Definiendo datos
                        $b = $listaProductosVariants['sku'];                 // SKU de productos Padres
                        $a = (array) $productosVariant[$h];                  // Obteniendo variantes 

                        $idWoocommer = $a['id'];                             // ID producto variante
                        $skuWoocommer = $a['sku'];                           // SKU producto variante
                        $cantidadWoocommer = $a['stock_quantity'];           // STOCK producto variante

                        // Creando matriz de objetos Arrays
                        $woocommercerArray[] = array($idWoocommer, $skuWoocommer,$cantidadWoocommer);  // Matriz contadora
                        $woocommercerArrayvariant[] = array($b, $idWoocommer, $skuWoocommer,$cantidadWoocommer);// Matriz de variantes
    
                    }

                    $tallas[] = $woocommercerArrayvariant;                    // Agregando matrices pendientes
                    $woocommercerArrayvariant = NULL;                         // Reseteando Matriz
                }

                $q++;                                                         // Variante de control
            }
        }

        // Obtenemos la lista filtrada del stock de S_HTEX DB
        while ($stockPrimaryDB =mysqli_fetch_array($query1)){              // Vista de productos S_HTEX DB

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

            // Generando matriz de productos
            $primaryDBArray[] = array("$idPrimaryDB", "$skuPrimaryDB",array("$cantidadT6","$cantidadT8","$cantidadT10","$cantidadT12","$cantidadT14","$cantidadT16","$cantidadT18","$cantidadT20","$cantidadT26","$cantidadT28","$cantidadT30","$cantidadT32","$cantidadT34","$cantidadT36","$cantidadT38","$cantidadTS","$cantidadTM","$cantidadTL","$cantidadTXL","$cantidadTU","$cantidadTEST"));

        }

        //print_r($primaryDBArray);                                        // Arrays de productos X tallas S_HTEX DB
        //print_r($woocommercerArray);                                     // Array contador de productos
        //print_r($tallas);                                                // Array de Productos X Variantes

        // Cantidad de productos en invenrario Woocommerce
        $numeroProductosCompleto = count($woocommercerArray);              // Numero completo productos en Woocommerce
        echo 'Numero de Referencias X talla en Woocommerce: ',$numeroProductosCompleto;
        echo"<br>";

        $listaRecorrida = 0;                                                // Variable de control

        // Iniciando comprobasiones de STOCK
        for($i = 0; $i < $cantidadProductos + 1; $i++){                     // Corremos la lista de Woocommerce X SKU
            
            // Verificar si una lista de variantes ya a sido recorrida
            if($listaRecorrida !== 0){

                // Verificar si la referencia de variantes a sido ubicada en S_HTEX DB
                if($referenciaHallada == 0){

                    echo 'Referencia inexistente en S-Htex DB';
                    echo "<br>";
                    echo 'Procediendo a actualizar referencia #: ', $skuPadreW;    // SKU padre de producto Woocommerce
                    echo "<br>";

                    // Si la referencia no fue hallada en S_HTEX DB Crearla
                    $sql="INSERT INTO inventarios_productos VALUES ('', '$skuPadreW', '', '', '', '', '', '', '', '', '', '', '', '', '', '$stockTalla6', '$stockTalla8', '$stockTalla10', '$stockTalla12', '$stockTalla14', '$stockTalla16', '$stockTalla18', '$stockTalla20', '$stockTalla26', '$stockTalla28', '$stockTalla30', '$stockTalla32', '$stockTalla34', '$stockTalla36', '$stockTalla38', '$stockTallaS', '$stockTallaM', '$stockTallaL', '$stockTallaXL', '$stockTallaU', '$stockTallaEST', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')";
                    $query= mysqli_query($con,$sql);

                    if($query){

                        // si la referencia fue creada
                        echo'Referencia #: ',$skuPadreW,' lista!';
                        echo "<br>";
                            
                    }else {

                        // Si la referencia no fue creada
                        echo'Referencia #: ',$skuPadreW,' No fue actualizada!';
                        echo "<br>";
                    }

                }
            }

            // Si alguna referencia es vacia rompe
            if(empty($tallas[$i])){
                break;
            }

            // Definiendo variables de los productos variantes
            $tallasVariantes = (array)$tallas[$i];                          // Lista de datos de los productos variantes

            // Verificar si la lista es simple o variante
            if(empty($tallas[$i][0][0])){
                
                // Si la lista es simple entra aqui
                echo 'El producto carece de tallas por lo tanto contacte a un administrador';
                break;

            }else{

                // Obteniendo datos
                $skuPadreW = $tallasVariantes[0][0];                         // SKU Padre de los productos variantes Woocommerce

                $stockTalla10 = $tallasVariantes[0][3];                      // STOCK Talla 10 Woocommerce
                $stockTalla12 = $tallasVariantes[1][3];                      // STOCK Talla 12 Woocommerce
                $stockTalla14 = $tallasVariantes[2][3];                      // STOCK Talla 14 Woocommerce
                $stockTalla16 = $tallasVariantes[3][3];                      // STOCK Talla 16 Woocommerce
                $stockTalla18 = $tallasVariantes[4][3];                      // STOCK Talla 18 Woocommerce
                $stockTalla20 = $tallasVariantes[5][3];                      // STOCK Talla 20 Woocommerce
                $stockTalla26 = $tallasVariantes[6][3];                      // STOCK Talla 26 Woocommerce
                $stockTalla28 = $tallasVariantes[7][3];                      // STOCK Talla 28 Woocommerce
                $stockTalla30 = $tallasVariantes[8][3];                      // STOCK Talla 30 Woocommerce
                $stockTalla32 = $tallasVariantes[9][3];                      // STOCK Talla 32 Woocommerce
                $stockTalla34 = $tallasVariantes[10][3];                     // STOCK Talla 34 Woocommerce
                $stockTalla36 = $tallasVariantes[11][3];                     // STOCK Talla 36 Woocommerce
                $stockTalla38 = $tallasVariantes[12][3];                     // STOCK Talla 38 Woocommerce
                $stockTalla6 = $tallasVariantes[13][3];                      // STOCK Talla 6 Woocommerce
                $stockTalla8 = $tallasVariantes[14][3];                      // STOCK Talla 8 Woocommerce
                $stockTallaEST = $tallasVariantes[15][3];                    // STOCK Talla EST Woocommerce
                $stockTallaL = $tallasVariantes[16][3];                      // STOCK Talla L Woocommerce
                $stockTallaM = $tallasVariantes[17][3];                      // STOCK Talla M Woocommerce
                $stockTallaS = $tallasVariantes[18][3];                      // STOCK Talla S Woocommerce
                $stockTallaU = $tallasVariantes[19][3];                      // STOCK Talla U Woocommerce
                $stockTallaXL = $tallasVariantes[20][3];                     // STOCK Talla XL Woocommerce

                $referenciaHallada = 0;                               // Verifica si la referencia n a sido hallada
                $listaRecorrida = 0;                                  // verifica si la lista de productos de S_HTEX a sido completada

                // Corremos la lista de productos o referencia en n Tallas
                for($ii = 0; $ii < $rowcount +1; $ii++){

                    // si no hay producto rompe
                    if(empty($primaryDBArray[$ii])){
                        break;
                    }

                    // Corremos los SKU de las referencia de S_HTEX DB
                    $skuDB = $primaryDBArray[$ii][1];

                    // Verificamos los SKU que son iguales de Woocommerce o S_HTEX
                    if($skuPadreW === $skuDB){

                        $referenciaHallada = 1;                        // Asignamos si la referencia fue encontrada en la lista de productos de S_HTEX DB
                        
                        // Actualiza los datos de tallas X STOCK de variantes de Woocommerce mientras SKU S_HTEX DB sea igual a SKU padre de Woocommerce
                        $sqlUP="UPDATE inventarios_productos SET talla6='$stockTalla6',talla8='$stockTalla8',talla10='$stockTalla10',talla12='$stockTalla12',talla14='$stockTalla14',talla16='$stockTalla16',talla18='$stockTalla18',talla20='$stockTalla20',talla26='$stockTalla26',talla28='$stockTalla28',talla30='$stockTalla30',talla32='$stockTalla32',talla34='$stockTalla34',talla36='$stockTalla36',talla38='$stockTalla38',tallas='$stockTallaS',tallam='$stockTallaM',tallal='$stockTallaL',tallaxl='$stockTallaXL',tallau='$stockTallaU',tallaest='$stockTallaEST' WHERE referencia ='$skuPadreW'";
                        $queryUP=mysqli_query($con,$sqlUP);

                        // Verificamos la consulta
                        if($queryUP){

                            // Si la actualizacion fue exitosa
                            echo "Base de datos al dia!";
                            echo "<br>";

                        }else{

                            // si la actualizacion no se realizo
                            echo "Base de datos no se pudo actualizar / Woocommerce!";
                            echo "<br>";

                        }

                    }else{                                      // Si la referencia no fue Hallada dentro de S_HTEX DB
                        
                        // Verificamos si la lista de producto de S_HTEX DB fue recorrida en su totalidad
                        if($listaRecorrida != NULL){

                            // Referencia de Woocommerce no esta en la lista de S_HTEX DB
                            echo 'Referencia no encontrada #: ',$skuPadreW;
                            echo "<br>";
                            
                        }else{                                   // Si la lista no ha sido recorrida sigue buscando

                            // Buscando referencias
                            echo 'Actualizando';
                            echo "<br>";

                        }
                    }
                }

                // Establecemos que la lista de S_HTEX DB ya fue recorrida
                $listaRecorrida = 1;

            }
        }

        // Iniciando metodos de catcheo de errores
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

// Instanciando Funciones 
$a = new APIMetodos();
$a->updateStockToWoocommerceDB();

?>