<?php

use eftec\cloudking\CloudKing;

@session_start();

include __DIR__.'/../../vendor/autoload.php';

class Definition {
    public static $service;

    /**
     * You must create this file manually.
     *
     * @param bool $gui if true then it shows the web gui
     */
    public static function init($gui=true) {
        $FILE = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $NAMESPACE = 'examplenamespace';
        $NAME_WS = 'Example2WS';

        self::$service = new CloudKing($FILE, $NAMESPACE, $NAME_WS);
        self::$service->allowed_input['gui']=$gui; // set to false to disable the web gui.
        self::$service->soap12 = false;
        self::$service->verbose = 2;
        self::$service->allowed_format['POST'] = true;
        //self::$service->allowed_format["GET"]=false;
        self::$service->variable_type = 'array';
        self::$service->serviceInstance = null;
        self::$service->description = 'Example server SoapKing';

        self::$service->addfunction('ping',
            [
                CloudKing::argPrim('ping_param', 's:string'),
            ],
            [
                CloudKing::argPrim('return', 's:string')
            ],
            'Descripcion :Prueba de conexion'
        );
        self::$service->addfunction('GetProducto',
            [
                CloudKing::argPrim('idProducto', 's:integer')
            ],
            [
                CloudKing::argComplex('return', 'tns:Producto')
            ],
            'Descripcion :obtiene los datos de una objeto'
        );
        self::$service->addfunction('InsertProducto'
            , array(CloudKing::argComplex('Producto', 'tns:Producto'))
            , array(CloudKing::argPrim('return', 's:boolean'))
            , 'Descripcion :obtiene los datos de una objeto'
        );
        self::$service->addfunction('GetProductos',
            [

            ],
            [
                CloudKing::argList('return', 'tns:Producto', 0, 'unbounded')
            ],
            'Descripcion :Obtiene una lista de productos'
        );

        // ***** type 
        self::$service->addtype('Producto',
            [
                CloudKing::argPrim('idProduct', 's:integer'),
                CloudKing::argPrim('nombre', 's:string'),
                CloudKing::argPrim('precio', 's:integer')
            ]);
        self::$service->addtype('ProductoArray',
            [
                CloudKing::argList('Producto', 'tns:Producto', '0', 'unbounded')
            ]);
    }
    public static function run() {
        $r = self::$service->run();
        echo $r;
    }
}



