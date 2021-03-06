<?php /** @noinspection TypeUnsafeComparisonInspection */

namespace www\examplenamespace\cl\service;
// file:Example2WSService.php
/**
 * @generated The structure of this class is generated by CloudKing
 * Class Example2WSService
 */
class Example2WSService implements IExample2WSService {
    /**
     * @return array[]=[self::factoryProducto()]
     */
    function getDB() {
        $r=@file_get_contents('data.txt');
        if($r===false) {
            return [];
        }
        return unserialize($r);
    }
    function saveDB($content) {
        @file_put_contents('data.txt',serialize($content));
    }
	/**
	 * @inheritDoc
	 */
	public function GetInvoice($idInvoice) {
        $obj= self::factoryInvoice(1
            ,[
                self::factoryInvoiceDetail(1,1,'detail1')
                ,
                self::factoryInvoiceDetail(2,1,'detail2')]);
        return $obj;
	}


	/**
	 * @inheritDoc
	 */
	public function ping(&$ping_param) {
		// todo:custom implementation goes here
        $ping_param='reference '.$ping_param;
        return 'some result';
	}


	/**
	 * @inheritDoc
	 */
	public function pingshot($ping_param) {
	}


	/**
	 * @inheritDoc
	 */
	public function doubleping($ping_param1, $ping_param2) {
		return $ping_param1.' - '.$ping_param2;
	}


	/**
	 * @inheritDoc
     * @noinspection TypeUnsafeComparisonInspection
     */
	public function GetProducto($idProducto) {
        $products=$this->getDB();
        foreach($products as $k=>$product) {
            if($idProducto==$product['idProduct']) {
                return $product;
            }
        }
        return null;
	}


	/**
	 * @inheritDoc
	 */
	public function InsertProducto($Producto) {
		$products=$this->getDB();
        foreach($products as $k=>$product) {
            if($Producto['idProduct']==$product['idProduct']) {
                return 0;
            }
        }
		$products[]=$Producto;
		$this->saveDB($products);
		return 1;
	}


	/**
	 * @inheritDoc
	 */
	public function GetProductos() {
        return $this->getDB();
	}

    public function UpdateProducto($Producto) {
        $products=$this->getDB();
        $found=0;
        foreach($products as $k=>$product) {
            if($Producto['idProduct']==$product['idProduct']) {
                $products[$k]=$Producto;
                $found=1;
                break;
            }
        }
        $this->saveDB($products);
        return $found;
    }

    public function DeleteProducto($idProducto) {
        $products=$this->getDB();
        $found=0;
        foreach($products as $k=>$product) {
            if($idProducto==$product['idProduct']) {
                array_splice($products,$k,1);
                $found=1;
                break;
            }
        }
        $this->saveDB($products);
        return $found;
    }

	public static function factoryProducto($idProduct=null, $nombre=null, $precio=null) {
		$_Producto['idProduct']=$idProduct;
		$_Producto['nombre']=$nombre;
		$_Producto['precio']=$precio;
		return $_Producto;
	}

	public static function factoryInvoiceDetail($idInvoiceDetail=null, $idInvoice=null, $detail=null) {
		$_InvoiceDetail['idInvoiceDetail']=$idInvoiceDetail;
		$_InvoiceDetail['idInvoice']=$idInvoice;
		$_InvoiceDetail['detail']=$detail;
		return $_InvoiceDetail;
	}

	public static function factoryArrayOfInvoiceDetail($InvoiceDetail=null) {
		$_ArrayOfInvoiceDetail['InvoiceDetail']=$InvoiceDetail;
		return $_ArrayOfInvoiceDetail;
	}

	public static function factoryInvoice($idInvoice=null, $details=null) {
		$_Invoice['idInvoice']=$idInvoice;
		$_Invoice['details']=$details;
		return $_Invoice;
	}

	public static function factoryArrayOfProducto($Producto=null) {
		$_ArrayOfProducto['Producto']=$Producto;
		return $_ArrayOfProducto;
	}


} // end class 
 