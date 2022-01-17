Biblioteca de cliente PHP de la API REST de WooCommerce REMASTERIZADA
=======================================

## Acerca de

Un contenedor de PHP para la API REST de WooCommerce.
Interactúe fácilmente con la API REST de WooCommerce usando esta biblioteca.

Se agradecen los comentarios y los informes de errores.

## Requisitos

PHP 5.2.x.

cURL WooCommerce 2.2 al menos en la tienda.

Composer.

## Empezando

Genere credenciales de API (Clave de consumidor y Secreto de consumidor) en WP Admin > Su perfil.

Inicie una configuracion de proyecto con composer
```git
composer init
```

Clone el repositorio a la carpeta de su proyecto en su maquina local.

Luego instal el paquete de dependencia 
```git
composer require automattic/woocommerce
```

## Configurar la biblioteca
```php
require_once( 'lib/woocommerce-api.php' );

$options = array(
	'ssl_verify'      => false,
);

try {

	$client = new WC_API_Client( 'http://your-store-url.com', $consumer_key, $consumer_secret, $options );

} catch ( WC_API_Client_Exception $e ) {

	echo $e->getMessage() . PHP_EOL;
	echo $e->getCode() . PHP_EOL;

	if ( $e instanceof WC_API_Client_HTTP_Exception ) {

		print_r( $e->get_request() );
		print_r( $e->get_response() );
	}
}
```

### Options

* `debug` (default `false`) - set to `true` to add request/response information to the returned data. This is particularly useful for troubleshooting errors.

* `return_as_array` (default `false`) - all methods return data as a `stdClass` by default, but you can set this option to `true` to return data as an associative array instead.

* `validate_url` (default `false`) - set this to `true` to verify that the URL provided has a valid, parseable WC API index, and optionally force SSL when supported.

* `timeout` (default `30`) - set this to control the HTTP timeout for requests.

* `ssl_verify` (default `true`) - set this to `false` if you don't want to perform SSL peer verification for every request.


### Error handling
Exceptions are thrown when errors are encountered, most will be instances of `WC_API_Client_HTTP_Exception` which has two additional methods, `get_request()` and `get_response()` -- these return the request and response objects to help with debugging.


## Methods

### Index

* `$client->index->get()` - get the API index

### Orders

* `$client->orders->get()` - get a list of orders
* `$client->orders->get( null, array( 'status' => 'completed' ) )` - get a list of completed orders
* `$client->orders->get( $order_id )` - get a single order


## Credit

Copyright (c) 2021 - ["HellFramco"](http://www.mb-developer.ml/), [Manuel Barrios](http://www.mb-developer.ml/) and other contributors

## License
Released under the [GPL3 license](http://www.gnu.org/licenses/gpl-3.0.html)
