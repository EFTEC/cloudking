<?

use eftec\cloudking\CloudKing;

@session_start();

include "../vendor/autoload.php";
include "Example2WSService.php";

$service=new Example2WSService();
//$objws->GetSin


$FILE = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]; 
$NAMESPACE="examplenamespace";
$NAME_WS='Example2WS';


$ns=new CloudKing($FILE, $NAMESPACE, $NAME_WS);

$ns->soap12=false;
$ns->verbose=2;
$ns->allowed_format["POST"]=true;
//$ns->allowed_format["GET"]=false;
$ns->variable_type="array";
$ns->serviceInstance=$service;
$ns->description="Ejemplo servidor SOAPUI";

$ns->addfunction("ping",
	array(
	    CloudKing::argPrim('ping_param', 's:string'),
	),
	array(
        CloudKing::argPrim('return', 's:string')
	),
	"Descripcion :Prueba de conexion"
	);
$ns->addfunction("GetProducto",
                 [
        CloudKing::argPrim('idProducto', 's:integer')
                 ],
                 [
        CloudKing::argComplex('return', 'tns:Producto')
                 ],
                 "Descripcion :obtiene los datos de una objeto"
	);
$ns->addfunction("InsertProducto",
                 array(
                     CloudKing::argComplex('Producto', 'tns:Producto')
                 ),
                 array(
                     CloudKing::argPrim('return', 's:boolean')
                 ),
                 "Descripcion :obtiene los datos de una objeto"
);
$ns->addfunction("GetProductos",
                 [

                 ],
                 [
                     CloudKing::argList('return', 'tns:Producto', 0, 'unbounded')
                 ],
                 "Descripcion :Obtiene una lista de productos"
	);

// ***** type 
$ns->addtype("Producto",
             [
                 CloudKing::argPrim('idProduct','s:integer'),
                 CloudKing::argPrim('nombre','s:string'),
                 CloudKing::argPrim('precio','s:integer')
             ]);
$ns->addtype("ProductoArray",
             array(
                 CloudKing::argList('Producto','tns:Producto','0','unbounded')
             ));
	
$r=$ns->run();
echo $r;
exit;
