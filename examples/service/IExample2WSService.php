<?php
namespace www\examplenamespace\cl\service;
/**
 * @generated The structure of this interface is generated by CloudKing
 * Interface IExample2WSService
 */
interface IExample2WSService {
	/**
	 * Description :get invoices
	 *
	 * @param mixed $idInvoice
	 *
	 * @return mixed
	 */
	public function GetInvoice($idInvoice);

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param
	 *
	 * @return mixed
	 */
	public function ping(&$ping_param);

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param
	 */
	public function pingshot($ping_param);

	/**
	 * Description :Prueba de conexion
	 *
	 * @param mixed $ping_param1
	 * @param mixed $ping_param2
	 *
	 * @return mixed
	 */
	public function doubleping($ping_param1, $ping_param2);

	/**
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $idProducto
	 *
	 * @return mixed
	 */
	public function GetProducto($idProducto);

	/**
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $Producto
	 *
	 * @return mixed
	 */
	public function InsertProducto($Producto);

	/**
	 * Description :obtiene los datos de una objeto
	 *
	 * @param mixed $Producto
	 *
	 * @return mixed
	 */
	public function UpdateProducto($Producto);

	/**
	 * Description :delete an product
	 *
	 * @param mixed $idProducto
	 *
	 * @return mixed
	 */
	public function DeleteProducto($idProducto);

	/**
	 * Description :Obtiene una lista de productos
	 *
	 *
	 * @return mixed
	 */
	public function GetProductos();

	public static function factoryProducto($idProduct=null, $nombre=null, $precio=null);

	public static function factoryInvoiceDetail($idInvoiceDetail=null, $idInvoice=null, $detail=null);

	public static function factoryArrayOfInvoiceDetail($InvoiceDetail=null);

	public static function factoryInvoice($idInvoice=null, $details=null);

	public static function factoryArrayOfProducto($Producto=null);
} // end class 
 