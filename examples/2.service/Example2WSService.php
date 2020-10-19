<?php

class Example2WSService {
    function getDB() {
        $r=@file_get_contents('data.txt');
        if($r===false) {
            return [];
        }
        return unserialize($r);
    }
    function saveDB($content) {
        $values=$this->getDB();
        $values[]=$content;
        $r=file_put_contents('data.txt',serialize($values));
    }


    function ping($ping_param) {
        try {


            return $ping_param;
        } catch (Exception $_exception) {
            return(array("soap:Fault"=>'Caught exception: '. $_exception->getMessage()));
        }
    }

    function GetProducto($idProducto) {
        try {

            $all=$this->getDB();
            $_Producto=null;
            foreach($all as $item) {
                if(@$item['idProducto']===$idProducto) {
                    $_Producto=$item;
                    break;
                }
            }


            return $_Producto;
        } catch (Exception $_exception) {
            return(array("soap:Fault"=>'Caught exception: '. $_exception->getMessage()));
        }
    }

    function InsertProducto($Producto) {

            $all=$this->getDB();
            $all[]=$Producto;
            $this->saveDB($all);
            return count($all);
       
    }

    function GetProductos() {
     
        return $this->getDB();
      
    }

    function factoryProducto($idProduct=0,$nombre='',$precio=0) {
        $_Producto['idProduct']=$idProduct;
        $_Producto['nombre']=$nombre;
        $_Producto['precio']=$precio;

    }

    function factoryProductoArray($Producto=array()) {
        $_ProductoArray['Producto']=$Producto;

    }
} // end class 

/************************************ Complex Types (Classes) ************************************/

class Producto {
    var $idProduct; // s:integer
    var $nombre; // s:string
    var $precio; // s:integer
}

class ProductoArray {
    var $Producto; // tns:Producto
}