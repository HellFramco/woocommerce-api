<?php
//header("Refresh: 300000; URL=index.php");
set_time_limit(0);

// Cargue de librerias
    require __DIR__ . '/vendor/autoload.php';
    require_once( 'lib/woocommerce-api.php' );
    include("conexion.php");

// Usar los parametros de la API de Woocommerce
use Automattic\WooCommerce\Client;

class APIMetodos{ //Creando clase de metodos

    // Funcion para traer la data del Stock de todos los productos de Woocommerce
    public function getPriceToFixing(){

        // Iniciando variables de autenticacion
            $options = array(
                'debug'           => true,
                'return_as_array' => false,
                'validate_url'    => false,
                'timeout'         => 0,
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

            // Accediendo Base de datos S-HTEX
                $con=conectar();

            // Importando datos de S-HTEX DB
                $sql1 = "SELECT * FROM `inventarios_productos` WHERE marca = 'DENIMDRY' OR marca = 'Denim Dry' OR marca = 'DENIM DRY' OR marca = 'DENIM DRY'";
                $query1 =mysqli_query($con,$sql1);
                
                while ($stockPrimaryDB =mysqli_fetch_array($query1)){
                        
                    print_r($stockPrimaryDB);
                    echo "<br>";
                    //print_r($stockPrimaryDB['referencia']);
                    //echo "<br>";

                    //$NewPrice = '55000';
                    //$NewPriceMajor = '43000';

                    /*

                    $sql="UPDATE inventarios_productos SET marca ='DENIM DRY' WHERE id_inventario='$stockPrimaryDB[0]'";
                    $query=mysqli_query($con,$sql);
    
                    if($query){
                        echo "Referencia #: ".$stockPrimaryDB[1].' fue refactorizada los precios ---> '.$NewPrice.' Precio normal / precio al mayor '.$NewPriceMajor;
                        echo "<br>";
                    }else{
                        echo "Referencia #: ".$stockPrimaryDB[1].' Cambio no';
                        echo "<br>";
                    }

                    */

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
    public function getOrdersFromWoocommerce(){

         // Iniciando variables de autenticacion
        $options = array(
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => 0,
            'ssl_verify'      => false,
        );

        try {

            // Accediendo API Woocommerce
                $client = new WC_API_Client(
                    'https://www.drabbalovers.co/',
                    'ck_7f574edb1041d0e71eadd69bb5092ee5c25dc5ca',
                    'cs_90e26e975b984c87d39cf31dbd3b913584be0557',
                    $options );
                
                $woocommerce = new Client(
                    'https://www.drabbalovers.co/', 
                    'ck_7f574edb1041d0e71eadd69bb5092ee5c25dc5ca', 
                    'cs_90e26e975b984c87d39cf31dbd3b913584be0557',
                [
                    'version' => 'wc/v3',
                ]);
            
            // Accediendo Base de datos S-HTEX
                $con=conectar();
            
            // Importando datos Woocommerce
                $pedidosWoocommerce = (array) $woocommerce->get('orders');

            // Importando datos de HTEX
                $sql1 = "SELECT * FROM pedidos_tienda";
                $query1 =mysqli_query($con,$sql1);

            // Numero pedidos en S-HTEX DB
                if ($result=mysqli_query($con,$sql1)) {

                    // Numero de filas de S-HTEXDB
                    $rowcount=mysqli_num_rows($result);
                    echo 'Numero de productos en SHTEX: ', $rowcount; 
                    echo"<br>";

                    
                    // Si la lista esta vacia inicializa la lista
                    if($rowcount == 0){
                        $primaryDBArray[] =NULL;
                    }
                
                }

            // Lista pedidos HTEX
                while ($stockPrimaryDB =mysqli_fetch_array($query1)){          // Vista de pedidos S_HTEX DB

                    // Filtracion de variables
                    $idPedidoDB = $stockPrimaryDB['id_pedido'];
                    $numeroPedidoDB = $stockPrimaryDB['numero_venta_w'];
                    $cuponPedido = $stockPrimaryDB['cupon'];
                    $nombreInfluenser = $stockPrimaryDB['nombre_influencer'];
                    $fechaPedido = $stockPrimaryDB['fecha_hora'];
                    $nombreCliente = $stockPrimaryDB['nombre_cliente'];
                    $documentoCliente = $stockPrimaryDB['documento_cliente'];
                    $direccionCliente = $stockPrimaryDB['direccion_cliente'];
                    $totalPedido = $stockPrimaryDB['total_venta'];

                    // Generando matriz de pedidos
                    $primaryDBArray[] = array("$idPedidoDB", "$numeroPedidoDB","$cuponPedido","$nombreInfluenser","$fechaPedido","$nombreCliente","$documentoCliente","$direccionCliente","$totalPedido");

                }

            $j = 1;                                                            // Variable de control paginas de pedidos Woocommerce
            //Lista pedidos Woocommerce
                for($i = 0; $i < $j; $i++){                                    // Vista pedidos X paginas Woocommerce

                    // Validar lista de pedidos vacia
                    if(empty($pedidosWoocommerce)){
                        break;
                    }

                    $q = 0;                                                                             // Variable de control pedidos X paginas
                    $objPaginas = ['page' => $j];                                                       // Numero de paginas disponibles
                    $pedidosWoocommerce = (array) $woocommerce->get('orders',$objPaginas);              // Lista completa pedidos Woocommerce

                    while($q <= 9){

                        // Si algun campo esta vacio rompe el ciclo
                        if(empty($pedidosWoocommerce[$q])){
                            break;
                        }

                        // Filtracion de variables
                        $numeroPedidoWoocommerce = $pedidosWoocommerce[$q]->number;
                        $cuponWoocommerce = $pedidosWoocommerce[$q]->coupon_lines[0]->code;
                        $nombreInfluenser = $pedidosWoocommerce[$q]->coupon_lines[0]->meta_data[0]->value->description;
                        $fechaPedidoWoocommerce = $pedidosWoocommerce[$q]->date_created;
                        $nombreClienteWoocommerce = $pedidosWoocommerce[$q]->billing->first_name.' '.$pedidosWoocommerce[$q]->billing->last_name;
                        $documentoClienteWoocommerce = $pedidosWoocommerce[$q]->meta_data[0]->value;
                        $direccionClienteWoocommerce = $pedidosWoocommerce[$q]->billing->address_1;
                        $totalVentaPedidoWoocommerce = $pedidosWoocommerce[$q]->total;
                        $prc = 1;

                        for($pr = 0; $pr < $prc; $pr++){

                            if(empty($pedidosWoocommerce[$q]->line_items[$pr])){ break;}

                            $nombreProducto = $pedidosWoocommerce[$q]->line_items[$pr]->name;
                            $cantidadAdquirida = $pedidosWoocommerce[$q]->line_items[$pr]->quantity;
                            $tallaProductoW = $pedidosWoocommerce[$q]->line_items[$pr]->meta_data[0]->value;
                            $SKUTallaProductoW = $pedidosWoocommerce[$q]->line_items[$pr]->sku;

                            $referenciasXPedido[] = array ("$nombreProducto","$cantidadAdquirida","$tallaProductoW","$SKUTallaProductoW");

                            $prc++;
                        }

                        // Controlando datos nulos
                        if(empty($cuponWoocommerce)){
                            $cuponWoocommerce = 'Ninguno';
                            $nombreInfluenser = 'Ninguno';
                        }

                        // Generando matriz de pedidos
                        if($pedidosWoocommerce[$q]->status == 'completed'){

                            $listaPedidosWoocommerce[] = array ("$numeroPedidoWoocommerce", "$cuponWoocommerce", "$nombreInfluenser", "$fechaPedidoWoocommerce", "$nombreClienteWoocommerce", "$documentoClienteWoocommerce", "$direccionClienteWoocommerce", "$totalVentaPedidoWoocommerce", $referenciasXPedido);
                            unset($referenciasXPedido);
                        }
                        $q++;
                    }

                    $j++;
                }

            //Control de variables internas

                //print_r($primaryDBArray);                                // Lista pedidos de HTEX
                //print_r($listaPedidosWoocommerce);                       // Lista pedidos Woocommerce
                //print_r($woocommerce->get('orders'));                    // Lista en bruto de pedidos Woocommerce
                //return;
            
            $w = 1;
            $ww = 1;
            $vuletaCheck = 0;
            $pedidoEncontrado = 0;

            // Iniciando comprobaciones de pedidos
            for($p = 0; $p < $w ; $p++){

                if($pedidoEncontrado == 0){

                    if($vuletaCheck == 1){
                        
                        $sqli="INSERT INTO pedidos_tienda (id_pedido, numero_venta_w, cupon, nombre_influencer, fecha_hora, nombre_cliente, documento_cliente,direccion_cliente, total_venta, estado) VALUES (NULL, '$numeroPW', '$cuponW', '$nombreInfluenserW', '$fechaPedidoW', '$nombreClienteW', '$documentoClienteW', '$direccionClienteW', '$totalPedidoW', 'Pendiente');";
                        $queryi= mysqli_query($con,$sqli);

                        if($queryi){

                            echo"El pedido se ha actualizado en la base de datos";
                                
                        }else {

                        }

                        $r = 1;
                        for($iii = 0; $iii < $r; $iii++){

                            if(empty($referenciasPW[$iii])){ break;}

                            $SKURPW = $referenciasPW[$iii][3];
                            $tallaRPW = $referenciasPW[$iii][2];
                            $cantidadRPW = $referenciasPW[$iii][1];

                            $sqli="INSERT INTO lista_productos_tienda (id_lista, numero_venta_w, referencia, talla, cantidad) VALUES (NULL, '$numeroPW', '$SKURPW', '$tallaRPW', '$cantidadRPW');";
                            $queryi= mysqli_query($con,$sqli);

                            if($queryi){

                                echo"Las referencias fueron anexadas";
                                echo "<br>";

                            }else {

                            }

                            switch($tallaRPW){
                                case 'talla6':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla6'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla6 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla8':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla8'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla8 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla10':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla10'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla10 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla12':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla12'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla12 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla14':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla14'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla14 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla16':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla16'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla16 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla18':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla18'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla18 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla20':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla20'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla20 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla26':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla26'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla26 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla28':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla28'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla2 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla30':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla30'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla30 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla32':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla32'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla32 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla34':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla34'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla34 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla36':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla36'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla36 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'talla38':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['talla38'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET talla38 = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallas':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallas'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallas = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallam':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallam'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallam = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallal':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallal'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallal = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallaxl':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallaxl'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallaxl = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallau':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallau'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallau = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                                case 'tallaest':

                                    $sql1 = "SELECT * FROM inventarios_productos WHERE referencia = '$SKURPW'";
                                    $query1 =mysqli_query($con,$sql1);

                                    while ($stockPrimaryDB =mysqli_fetch_array($query1)){

                                        $stockPDB = $stockPrimaryDB['tallaest'];
                                        $newStock = $stockPDB - $cantidadRPW;

                                        
                                        $sql="UPDATE inventarios_productos SET tallaest = '$newStock' WHERE referencia ='$SKURPW'";
                                        $query=mysqli_query($con,$sql);
                        
                                        if($query){
                                            echo 'La cantidad se ah actualizado';
                                            echo "<br>";
                                        }else{
                                            echo 'NO';
                                            echo "<br>";
                                        }

                                    }

                                    break;
                            }

                            $r++;
                        }

                    }

                }

                if(empty($listaPedidosWoocommerce[$p])){
                    break;
                }

                $numeroPW = $listaPedidosWoocommerce[$p][0];
                $cuponW = $listaPedidosWoocommerce[$p][1];
                $nombreInfluenserW = $listaPedidosWoocommerce[$p][2];
                $fechaPedidoW = $listaPedidosWoocommerce[$p][3];
                $nombreClienteW = $listaPedidosWoocommerce[$p][4];
                $documentoClienteW = $listaPedidosWoocommerce[$p][5];
                $direccionClienteW = $listaPedidosWoocommerce[$p][6];
                $totalPedidoW = $listaPedidosWoocommerce[$p][7];
                $referenciasPW = $listaPedidosWoocommerce[$p][8];
                $vuletaCheck = 0;

                for($pp = 0; $pp < $ww; $pp++){

                    if(empty($primaryDBArray[$pp][1])){ break;}

                    if($numeroPW == $primaryDBArray[$pp][1]){

                        $pedidoEncontrado = 1;
                        echo 'El pedido '.$numeroPW.' ya existe en HTEX';
                        echo "<br>";
                        break;

                    }else{

                        echo 'Buscando';
                        echo "<br>";

                    }

                    $ww++;
                }

                $vuletaCheck = 1;
                $w++;
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
    // Funcion para Refactorizar las referencias repetidas de S-HTEX DB
    public function getDuplicatesSKUInSHTEXDB(){

        // Iniciando variables de autenticacion
        $options = array(
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => 0,
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

            // Accediendo Base de datos S-HTEX
            $con=conectar();

            // Importando datos de S-HTEX DB
            //$sql1 = "SELECT id_inventario, referencia, reprogramacion, tipo, estado, ROW_NUMBER() OVER( PARTITION BY referencia ORDER BY referencia  ) AS rn FROM inventarios_productos";
            $sql1 = "SELECT * FROM inventarios_productos";
            $query1 =mysqli_query($con,$sql1);
            $e = 0;
            while ($stockPrimaryDB =mysqli_fetch_array($query1)){


                //print_r($stockPrimaryDB);
                //echo "<br>";
                    
                $NewSKU = $stockPrimaryDB[1].'-'.$stockPrimaryDB[0];

                //print_r($NewSKU);
                //echo "<br>";

                
                    $sql="UPDATE inventarios_productos SET referencia='$NewSKU' WHERE id_inventario='$stockPrimaryDB[0]'";
                    $query=mysqli_query($con,$sql);
    
                    if($query){
                        echo "Referencia #: ".$stockPrimaryDB[1].' Cambio a ---> '.$NewSKU;
                        echo "<br>";
                    }else{
                        echo "Referencia #: ".$stockPrimaryDB[1].' Cambio no';
                        echo "<br>";
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
    // Funcion para actualizar productos a la base de datos de Woocommerce
    public function updateStockToWoocommerceDB(){

        // Iniciando variables de autenticacion
            $options = array(
                'debug'           => true,
                'return_as_array' => false,
                'validate_url'    => false,
                'timeout'         => 0,
                'ssl_verify'      => false,
            );

        try {
            // Accediendo API Woocommerce
                $client = new WC_API_Client(
                'https://www.drabbalovers.co/',
                'ck_7f574edb1041d0e71eadd69bb5092ee5c25dc5ca',
                'cs_90e26e975b984c87d39cf31dbd3b913584be0557',
                $options );

                $woocommerce = new Client(
                'https://www.drabbalovers.co/', 
                'ck_7f574edb1041d0e71eadd69bb5092ee5c25dc5ca', 
                'cs_90e26e975b984c87d39cf31dbd3b913584be0557',
                [
                    'version' => 'wc/v3',
                ]);

            // Accediendo Base de datos S-HTEX
                $con=conectar();
            
            // Inciando methos GET
                $listaProductosXcantidad = (array) $client->products->get_count();
                $listaProductos = (array) $client->products->get();

            // Importando stock de Woocommerce	
                $listaProductosPaginas = (array) $listaProductos['http'];         //Lista de productos JSON
                $response = (array) $listaProductosPaginas['response'];           // Metadatos de Lista de productos JSON
                $headers = (array) $response['headers'];                          // Headers de los Metadatos
                $paginas =  $headers['x-wc-totalpages'];                           // Numero de paginas X productos Woocommerce
                $paginas = (int)$paginas;                                         // Variable numeros de paginas Principal
            
            // Importando datos de S-HTEX DB
                $sql1 = "SELECT * FROM inventarios_productos GROUP BY referencia HAVING COUNT(*)=1";
                $query1 =mysqli_query($con,$sql1);
            
            // Numero Referencias del stock de S-HTEX DB
                if ($result=mysqli_query($con,$sql1)) {

                    // Numero de filas de S-HTEXDB
                    $rowcount=mysqli_num_rows($result);
                    echo 'Numero de productos en SHTEX: ', $rowcount; 
                    echo"<br>";
                }
            
            // Numero Referencias en Woocommerce
                $cantidadProductos = $listaProductosXcantidad['count'];
                echo 'Numero de referencias de Woocomerce: ',$cantidadProductos;
                echo"<br>";
                
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

                            // Validacion de variable
                                echo 'Revice su inventario en Woocommerce la referencia #: ',$skuWoocommer,' Presenta
                                que es un producto simple (que no tiene variaciones de tallas)';
                                echo "<br>";
                                echo 'Elimine la referencia de Woocommerce o contactese con el personal para seguir';
                                echo "<br>";
                                echo 'ID Woocommerce:---> ',$idWoocommer;
                                echo "<br>";
                                echo 'SKU Woocommerce:---> ',$skuWoocommer;
                            
                            return;

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
                while ($stockPrimaryDB =mysqli_fetch_array($query1)){          // Vista de productos S_HTEX DB

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

                    $siluetaReferencia = $stockPrimaryDB['descripcion'];
                    $precioReferencia = $stockPrimaryDB['precio'];

                    // Generando matriz de productos
                    $primaryDBArray[] = array("$idPrimaryDB", "$skuPrimaryDB","$cantidadT6","$cantidadT8","$cantidadT10","$cantidadT12","$cantidadT14","$cantidadT16","$cantidadT18","$cantidadT20","$cantidadT26","$cantidadT28","$cantidadT30","$cantidadT32","$cantidadT34","$cantidadT36","$cantidadT38","$cantidadTS","$cantidadTM","$cantidadTL","$cantidadTXL","$cantidadTU","$cantidadTEST","$siluetaReferencia","$precioReferencia");

                }
            
            // Control de variables usadas por el sistema  FIXEAR CONTADOR PRODUCTOS WOOCOMMERCE
                //print_r($tallas);                                            // Lista datos filtrados Tallas X SKU's' X STOCK X ID's X price Woocommerce
                //print_r($primaryDBArray);                                    // Tallas de productos variantes S_HTEX DB
                //return;

                //$rowcount;                                                   // Numero de filas en S_HTEX DB
                //$cantidadProductos                                           // Numero de filas en Woocommerce

                //print_r($listaProductosVariants);                            // Lista Productos Woocommerce
                //print_r($productosVariant);                                  // Lista de variantes Woocommerce
                //print_r($woocommercerArray);  FIXEAR!                        // Contador Tallas X productos Woocommerce
                //print_r($listaProductosPadreVariant);                        // Vista Todo Woocommerce        
                //print_r($SKUProductosWoocommerce);                           // Lista de SKU de Woocommerce

            // Cantidad de productos en invenrario Woocommerce FEEDBACK ARREGLAR FIXEAR
                //$numeroProductosCompleto = count($woocommercerArray);              // Numero completo productos en Woocommerce
                //echo 'Numero de productos en Woocommerce: ',$numeroProductosCompleto;
                //echo"<br>";

            // Iniciando comprobasiones de STOCK

                $SKU1XSKU2 = 0;
                $VUELTA = 0;


                for($i = 0; $i < $rowcount +1; $i++){

                    // Creacion de referencias faltantes en Woocommerce
                        if($VUELTA != 0 ){

                            if($SKU1XSKU2 == 0){

                                echo 'El codigo #: ', $SKUPrimaryDB, ' No existe en Woocommerce';
                                echo "<br>";

                                // Creando datos de productos
                                if (empty($siluetaName)){
                                        $siluetaName = 'NULL';
                                }

                                $data = [
                                        'name' => $siluetaName,
                                        'type' => 'variable',
                                        'sku' => $SKUPrimaryDB,
                                        'regular_price' => $precioReferencia,
                                        'virtual' => true,
                                        'description' => '',
                                        'short_description' => '',
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
                                                    'talla6',
                                                    'talla8',
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
                                                    'tallas',
                                                    'tallam',
                                                    'tallal',
                                                    'tallaxl',
                                                    'tallau',
                                                    'tallaest'
                                                ]
                                            ]
                                        ],

                                    ];

                                if(empty($datosUpdate = $woocommerce->post('products', $data))){

                                    echo 'La referencia no se agrego a Woocommerce';
                                    echo "<br>";
                                    break;

                                }else{

                                    echo 'La referencia se agrego a Woocommerrce Procediendo a crear sus variantes';
                                    echo "<br>";

                                    $idUpdate = $datosUpdate->id;
                                    $SKUVariante = $datosUpdate->sku.'-'.$idUpdate;

                                    $data = [
                                        'create' => [
                                            [
                                                'description' => $siluetaName.' TALLA 6',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA6,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla6'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 8',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA8,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla8'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 10',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA10,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla10'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 12',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA12,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla12'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 14',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA14,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla14'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 16',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA16,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla16'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 18',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA18,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla18'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 20',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA20,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla20'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 26',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA26,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla26'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 28',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA28,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla28'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 30',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA30,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla30'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 32',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA32,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla32'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 34',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA34,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla34'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 36',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA36,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla36'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA 38',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLA38,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'talla38'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA S',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAS,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallas'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA M',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAM,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallam'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA L',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAL,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallal'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA XL',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAXL,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallaxl'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA U',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAU,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallau'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'description' => $siluetaName.' TALLA EST',
                                                'regular_price' => $precioReferencia,
                                                'virtual' => true,
                                                'manage_stock' => true,
                                                'stock_quantity' => $TALLAEST,
                                                'attributes' => [
                                                    [
                                                        'id' => 0,
                                                        'name' => 'Size',
                                                        'option' => 'tallaest'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                        if(empty( $varianteUpdate = $woocommerce->post('products/'.$idUpdate.'/variations/batch', $data))){

                                            echo 'Las variaciones no se agregaron';
                                            echo "<br>";
                                            break;

                                        }else{

                                            echo 'Las variaciones si se agregaron '.$idUpdate;
                                            echo "<br>";

                                            //header("Location: index.php");

                                    }
                                    
                                }
                                
                            }

                        }
                    // Obteniendo datos
                        $SKUPrimaryDB = $primaryDBArray[$i][1];    // SKU REFERENCIAS CORRIDAS PrimaryDB

                        $TALLA6 = $primaryDBArray[$i][2];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA8 = $primaryDBArray[$i][3];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA10 = $primaryDBArray[$i][4];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA12 = $primaryDBArray[$i][5];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA14 = $primaryDBArray[$i][6];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA16 = $primaryDBArray[$i][7];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA18 = $primaryDBArray[$i][8];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA20 = $primaryDBArray[$i][9];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA26 = $primaryDBArray[$i][10];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA28 = $primaryDBArray[$i][11];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA30 = $primaryDBArray[$i][12];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA32 = $primaryDBArray[$i][13];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA34 = $primaryDBArray[$i][14];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA36 = $primaryDBArray[$i][15];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLA38 = $primaryDBArray[$i][16];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAS = $primaryDBArray[$i][17];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAM = $primaryDBArray[$i][18];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAL = $primaryDBArray[$i][19];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAXL = $primaryDBArray[$i][20];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAU = $primaryDBArray[$i][21];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $TALLAEST = $primaryDBArray[$i][22];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        
                        $siluetaName = $primaryDBArray[$i][23];    // SKU REFERENCIAS CORRIDAS PrimaryDB
                        $precioReferencia = $primaryDBArray[$i][24];    // SKU REFERENCIAS CORRIDAS PrimaryDB

                    $SKU1XSKU2 = 0;

                    for($ii=0; $ii < $cantidadProductos + 1; $ii++){

                        $skuGenerade = (array) $tallas[$ii][0]; 
                        $IdPWoocommerce = $skuGenerade[0];              // SKU REFERENCIAS CORRIDAS Woocommerce
                        $SKUWoocommerce = $skuGenerade[2];             // Variables principal filtrada

                        if($SKUPrimaryDB === $SKUWoocommerce){         // Comprobacion de referencias 

                            echo 'Referencia #: '.$SKUPrimaryDB.' Encontrada procediendo a actualizar sus variables!';
                            echo "<br>";

                            $SKU1XSKU2 = 1;                            // Controlador de referencias encontradas

                                // INTEGRAR METODO DE ACTUALIZACION 

                                $skuGenerade = (array) $tallas[$ii][0];
                                $IDWoocommerceTalla6 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][1];
                                $IDWoocommerceTalla8 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][2];
                                $IDWoocommerceTalla10 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][3];
                                $IDWoocommerceTalla12 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][4];
                                $IDWoocommerceTalla14 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][5];
                                $IDWoocommerceTalla16 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][6];
                                $IDWoocommerceTalla18 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][7];
                                $IDWoocommerceTalla20 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][8];
                                $IDWoocommerceTalla26 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][9];
                                $IDWoocommerceTalla28 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][10];
                                $IDWoocommerceTalla30 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][11];
                                $IDWoocommerceTalla32 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][12];
                                $IDWoocommerceTalla34 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][13];
                                $IDWoocommerceTalla36 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][14];
                                $IDWoocommerceTalla38 = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][15];
                                $IDWoocommerceTallaS = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][16];
                                $IDWoocommerceTallaM = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][17];
                                $IDWoocommerceTallaL = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][18];
                                $IDWoocommerceTallaXL = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][19];
                                $IDWoocommerceTallaU = $skuGenerade[1]; 
                                $skuGenerade = (array) $tallas[$ii][20];
                                $IDWoocommerceTallaEST = $skuGenerade[1]; 

                                $data = [
                                    'update' => [
                                        [
                                            'id' => $IDWoocommerceTalla6,
                                            'stock_quantity' => $TALLA6,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla8,
                                            'stock_quantity' => $TALLA8,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla10,
                                            'stock_quantity' => $TALLA10,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla12,
                                            'stock_quantity' => $TALLA12,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla14,
                                            'stock_quantity' => $TALLA14,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla16,
                                            'stock_quantity' => $TALLA16,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla18,
                                            'stock_quantity' => $TALLA18,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla20,
                                            'stock_quantity' => $TALLA20,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla26,
                                            'stock_quantity' => $TALLA26,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla28,
                                            'stock_quantity' => $TALLA28,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla30,
                                            'stock_quantity' => $TALLA30,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla32,
                                            'stock_quantity' => $TALLA32,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla34,
                                            'stock_quantity' => $TALLA34,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla36,
                                            'stock_quantity' => $TALLA36,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTalla38,
                                            'stock_quantity' => $TALLA38,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaS,
                                            'stock_quantity' => $TALLAS,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaM,
                                            'stock_quantity' => $TALLAM,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaL,
                                            'stock_quantity' => $TALLAL,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaXL,
                                            'stock_quantity' => $TALLAXL,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaU,
                                            'stock_quantity' => $TALLAU,
                                            'regular_price' => $precioReferencia,
                                        ],
                                        [
                                            'id' => $IDWoocommerceTallaEST,
                                            'stock_quantity' => $TALLAEST,
                                            'regular_price' => $precioReferencia,
                                        ]
                                    ]
                                ];

                                if($woocommerce->post('products/'.$IdPWoocommerce.'/variations/batch', $data)){

                                    echo 'Las variables se actualizaron corectamente';
                                    echo "<br>";

                                }else{

                                    
                                    echo 'Hay un problema al actualizar esta referencia #: '.$SKUPrimaryDB.' Ubiquela y corrijala manualmente';
                                    echo "<br>";
                                };

                            break;

                        }else{

                            // validacion de datos

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
        'timeout'         => 0,
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

                $stockTalla6 = $tallasVariantes[0][3];                      // STOCK Talla 10 Woocommerce
                $stockTalla8 = $tallasVariantes[1][3];                      // STOCK Talla 12 Woocommerce
                $stockTalla10 = $tallasVariantes[2][3];                      // STOCK Talla 14 Woocommerce
                $stockTalla12 = $tallasVariantes[3][3];                      // STOCK Talla 16 Woocommerce
                $stockTalla14 = $tallasVariantes[4][3];                      // STOCK Talla 18 Woocommerce
                $stockTalla16 = $tallasVariantes[5][3];                      // STOCK Talla 20 Woocommerce
                $stockTalla18 = $tallasVariantes[6][3];                      // STOCK Talla 26 Woocommerce
                $stockTalla20 = $tallasVariantes[7][3];                      // STOCK Talla 28 Woocommerce
                $stockTalla26 = $tallasVariantes[8][3];                      // STOCK Talla 30 Woocommerce
                $stockTalla28 = $tallasVariantes[9][3];                      // STOCK Talla 32 Woocommerce
                $stockTalla30 = $tallasVariantes[10][3];                     // STOCK Talla 34 Woocommerce
                $stockTalla32 = $tallasVariantes[11][3];                     // STOCK Talla 36 Woocommerce
                $stockTalla34 = $tallasVariantes[12][3];                     // STOCK Talla 38 Woocommerce
                $stockTalla36 = $tallasVariantes[13][3];                      // STOCK Talla 6 Woocommerce
                $stockTalla38 = $tallasVariantes[14][3];                      // STOCK Talla 8 Woocommerce
                $stockTallaS = $tallasVariantes[15][3];                    // STOCK Talla EST Woocommerce
                $stockTallaM = $tallasVariantes[16][3];                      // STOCK Talla L Woocommerce
                $stockTallaL = $tallasVariantes[17][3];                      // STOCK Talla M Woocommerce
                $stockTallaXL = $tallasVariantes[18][3];                      // STOCK Talla S Woocommerce
                $stockTallaU = $tallasVariantes[19][3];                      // STOCK Talla U Woocommerce
                $stockTallaEST = $tallasVariantes[20][3];                     // STOCK Talla XL Woocommerce

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