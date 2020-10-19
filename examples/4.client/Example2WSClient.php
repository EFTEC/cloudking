<?php
namespace examplenamespace;
use eftec\cloudking\CloudKingClient;

/**
 * Class Example2WSClient<br>
 * Example server SoapKing<br>
 * This code was generated automatically using CloudKing v2.5, Date:Sun, 18 Oct 2020 23:08:41 -0300 <br>
 * Using the web:http://localhost/currentproject/cloudking/examples/1.create/CreateWebService.php?source=phpclient
 */
class Example2WSClient {
    /** @var string The full url where is the web service */
    protected $url='http://localhost/currentproject/cloudking/examples/3.join/WebService.php';
    /** @var string The namespace of the web service */
    protected $tempuri='examplenamespace';
    /** @var string The last error. It is cleaned per call */
    public $lastError='';
    /** @var float=[1.1,1.2][$i] The SOAP used by default */
    protected $soap=1.1;
    /** @var CloudKingClient */
    public $service;
    /**
     * Example2WSClient constructor.
     *
     * @param string|null $url The full url (port) of the web service
     * @param string|null $tempuri The namespace of the web service
     * @param float|null $soap=[1.1,1.2][$i] The SOAP used by default
     */
    public function __construct($url=null, $tempuri=null, $soap=null) {
        $url!==null and $this->url = $url;
        $tempuri!==null and $this->tempuri = $tempuri;
        $soap!==null and $this->soap = $soap;
        $this->service=new CloudKingClient($this->soap,$this->tempuri);
    }


    /**
     * Descripcion :Prueba de conexion
     *
     * @param mixed $ping_param  (s:string)
     * @return mixed (s:string)
     * @noinspection PhpUnused */
    public function ping($ping_param) {
        $_param='';
        $_param.=$this->service->array2xml($ping_param,'ts:ping_param',false,false);
        $resultado=$this->service->loadurl($this->url,$_param,'ping');
        $this->lastError=$this->service->lastError;
        if(!is_array($resultado)) {
            return false; // error
        }
        return @$resultado['pingResult'];
    }

    /**
     * Descripcion :obtiene los datos de una objeto
     *
     * @param mixed $idProducto  (s:integer)
     * @return mixed (tns:Producto)
     * @noinspection PhpUnused */
    public function GetProducto($idProducto) {
        $_param='';
        $_param.=$this->service->array2xml($idProducto,'ts:idProducto',false,false);
        $resultado=$this->service->loadurl($this->url,$_param,'GetProducto');
        $this->lastError=$this->service->lastError;
        if(!is_array($resultado)) {
            return false; // error
        }
        return @$resultado['GetProductoResult'];
    }

    /**
     * Descripcion :obtiene los datos de una objeto
     *
     * @param mixed $Producto  (tns:Producto)
     * @return mixed (s:boolean)
     * @noinspection PhpUnused */
    public function InsertProducto($Producto) {
        $_param='';
        $_param.=$this->service->array2xml($Producto,'ts:Producto',false,false);
        $resultado=$this->service->loadurl($this->url,$_param,'InsertProducto');
        $this->lastError=$this->service->lastError;
        if(!is_array($resultado)) {
            return false; // error
        }
        return @$resultado['InsertProductoResult'];
    }

    /**
     * Descripcion :Obtiene una lista de productos
     *
     * @return mixed (tns:Producto)
     * @noinspection PhpUnused */
    public function GetProductos() {
        $_param='';
        $resultado=$this->service->loadurl($this->url,$_param,'GetProductos');
        $this->lastError=$this->service->lastError;
        if(!is_array($resultado)) {
            return false; // error
        }
        return @$resultado['GetProductosResult'];
    }
} // end Example2WSClient