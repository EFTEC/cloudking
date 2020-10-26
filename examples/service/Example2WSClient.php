<?php
/** @noinspection DuplicatedCode
 * @noinspection UnknownInspectionInspection
 */

namespace www\examplenamespace\cl;
use eftec\cloudking\CloudKingClient;

/**
 * Class Example2WSClient<br>
 * Example server SoapKing<br>
 * This code was generated automatically using CloudKing v3.0, Date:Mon, 26 Oct 2020 18:13:02 -0300 <br>
 * Using the web:http://localhost/currentproject/cloudking/examples/Server.php?source=phpclient
 */
class Example2WSClient {
	/** @var string The full url where is the web service */
	protected $url='http://localhost/currentproject/cloudking/examples/Server.php';
	/** @var string The namespace of the web service */
	protected $tempuri='http://www.examplenamespace.cl/';
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
	 * Description :get invoices
	 *
	 * @param mixed $idInvoice the id of the invoice to get (s:int) 
	 * @return mixed (tns:Invoice)
	 * @noinspection PhpUnused */
	public function GetInvoice($idInvoice) {
		$_param='';
		$_param.=$this->service->array2xml($idInvoice,'ts:idInvoice',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'GetInvoice');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		return @$resultado['GetInvoiceResult'];
	}

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param  (s:string) 
	 * @return mixed (s:string)
	 * @noinspection PhpUnused */
	public function ping(&$ping_param) {
		$_param='';
		$_param.=$this->service->array2xml($ping_param,'ts:ping_param',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'ping');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		$ping_param=@$resultado['ping_param'];
		return @$resultado['pingResult'];
	}

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param  (s:string) 
	 * @return mixed (void)
	 * @noinspection PhpUnused */
	public function pingshot($ping_param) {
		$_param='';
		$_param.=$this->service->array2xml($ping_param,'ts:ping_param',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'pingshot');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		return @$resultado['pingshotResult'];
	}

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param1  (s:string) 
	 * @param mixed $ping_param2  (s:string) 
	 * @return mixed (s:string)
	 * @noinspection PhpUnused */
	public function doubleping($ping_param1, $ping_param2) {
		$_param='';
		$_param.=$this->service->array2xml($ping_param1,'ts:ping_param1',false,false);
		$_param.=$this->service->array2xml($ping_param2,'ts:ping_param2',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'doubleping');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		return @$resultado['doublepingResult'];
	}

	/**
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $idProducto id del producto (s:int) 
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
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $Producto  (tns:Producto) 
	 * @return mixed (s:int)
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
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $Producto  (tns:Producto) 
	 * @return mixed (s:boolean)
	 * @noinspection PhpUnused */
	public function UpdateProducto($Producto) {
		$_param='';
		$_param.=$this->service->array2xml($Producto,'ts:Producto',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'UpdateProducto');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		return @$resultado['UpdateProductoResult'];
	}

	/**
	 * Description :delete an product
	 *
	 * @param mixed $idProducto id del producto (s:int) 
	 * @return mixed (s:boolean)
	 * @noinspection PhpUnused */
	public function DeleteProducto($idProducto) {
		$_param='';
		$_param.=$this->service->array2xml($idProducto,'ts:idProducto',false,false);
		$resultado=$this->service->loadurl($this->url,$_param,'DeleteProducto');
		$this->lastError=$this->service->lastError;
		if(!is_array($resultado)) {
			return false; // error
		}
		return @$resultado['DeleteProductoResult'];
	}

	/**
	 * Description :Obtiene una lista de productos
	 *
	 * @return mixed (tns:ArrayOfProducto)
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
