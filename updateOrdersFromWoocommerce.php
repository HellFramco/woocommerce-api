<?php
set_time_limit(0);

// Cargue de librerias
    require __DIR__ . '/vendor/autoload.php';
    require_once( 'lib/woocommerce-api.php' );
    include("conexion.php");

// Usar los parametros de la API de Woocommerce
use Automattic\WooCommerce\Client;

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
            echo '<h1>Numero de pedidos completados en SHTEX: ', $rowcount; 
            echo"</h1><br>";

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

            header("Refresh: 3; URL=updateOrdersFromWoocommerce.php");

        }catch ( WC_API_Client_Exception $e ) {

            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;
            
            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
            
                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }

?>