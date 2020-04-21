<?

use eftec\cloudking\CloudKingClient;

include "../vendor/autoload.php";


$x=new ExampleCLient();
echo "<pre>";
var_dump($x->GetProductos());
echo "</pre>";
/***************************************** Implementation *****************************************/
class ExampleCLient {
    var $url='http://localhost/currentproject/cloudking/examples/example2.php';
    var $tempuri='examplenamespace';

    // Descripcion :Prueba de conexion 
    // ping_param =  
    function ping($ping_param) {
        $_obj=new CloudKingClient();
        $_obj->soap='1.1';
        $_obj->tempuri=$this->tempuri;
        $_param='';
        $_param.=$_obj->array2xml($ping_param,'ts:ping_param',false,false);
        $resultado=$_obj->loadurl($this->url,$_param,'ping');
        return @$resultado['pingResult'];
    }

    // Descripcion :GetSin obtiene los datos de una SIN 
    // idProducto =  
    function GetProducto($idProducto) {
        $_obj=new CloudKingClient();
        $_obj->soap='1.1';
        $_obj->tempuri=$this->tempuri;
        $_param='';
        $_param.=$_obj->array2xml($idProducto,'ts:idProducto',false,false);
        $resultado=$_obj->loadurl($this->url,$_param,'GetProducto');
        return @$resultado['GetProductoResult'];
    }

    // Descripcion :Get_sin obtiene los datos de una SIN 
    function GetProductos() {
        $_obj=new CloudKingClient();
        $_obj->soap='1.1';
        $_obj->tempuri=$this->tempuri;
        $_param='';
        $resultado=$_obj->loadurl($this->url,$_param,'GetProductos');
        return @$resultado['GetProductosResult'];
    }
} // end Sin_WS
?>