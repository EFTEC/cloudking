<?

use eftec\cloudking\CloudKingClient;

include "../vendor/autoload.php";


$x=new ExampleCLient();
echo "<pre>";
var_dump($x->GetProductos());
echo "</pre>";
/***************************************** Implementation *****************************************/
class ExampleCLient {
    var $url='http://pcjc:8080/EjemploWSService/EjemploWS';
    var $tempuri='http://ws.cocacola.cl';

    // Descripcion :Prueba de conexion 
    // ping_param =  
    function ping() {
        $_obj=new CloudKingClient();
        $_obj->soap='1.1';
        $_obj->tempuri=$this->tempuri;
        $_param='';
        
        $_param.=$_obj->array2xml([],'ts:modelClass',false,false);
        $resultado=$_obj->loadurl($this->url,$_param,'listModel');
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
        $resultado=$_obj->loadurl($this->url,$_param,'listModel');
        return @$resultado;
    }
} // end Sin_WS
?>