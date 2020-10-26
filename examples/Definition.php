<?php /** @noinspection PhpUnhandledExceptionInspection */

use eftec\cloudking\CloudKing;

@session_start();

include __DIR__ . '/../vendor/autoload.php';

class Definition {
    public static $service;

    /**
     * You must create this file manually.
     *
     * @param bool $gui if true then it shows the web gui
     */
    public static function init($gui=true) {
        $FILE = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $NAMESPACE = 'http://www.examplenamespace.cl/';
        $NAME_WS = 'Example2WS';

        self::$service = new CloudKing($FILE, $NAMESPACE, $NAME_WS);
        self::$service->allowed_input['gui']=$gui; // set to false to disable the web gui.
        self::$service->soap12 = false; // if we want to use soap 1.2
        self::$service->verbose = 2; // for debug purpose

        //self::$service->allowed_format["GET"]=false;
        self::$service->serviceInstance = null;
        self::$service->description = 'Example server SoapKing';
        // ***** complex types 
        self::$service->addtype('Producto',
            [
                self::$service->param('idProduct', 'int',false,true, 'comentario'),
                self::$service->param('nombre', 'string'),
                self::$service->param('precio', 'int')
            ]);
        self::$service->addtype('InvoiceDetail', [
            self::$service->param('idInvoiceDetail', 'int',false,true),
            self::$service->param('idInvoice', 'int'),
            self::$service->param('detail', 'string')
        ]);
        self::$service->addtype('Invoice', [
            self::$service->param('idInvoice', 'int',false,true,'type invoice'),
            self::$service->paramList('details','InvoiceDetail'),
        ]);


        self::$service->addfunction('GetInvoice'
            , [self::$service->param('idInvoice','int',false,true,'the id of the invoice to get')]
            , [self::$service->param('Invoice', 'Invoice')]
            , 'Description :get invoices');
        
        self::$service->addfunction('ping',
            [
                self::$service->param('ping_param', 'string',true,false),
            ],
            [
                self::$service->param('return', 'string')
            ],
            'Description :Prueba de conexion'
        );
        self::$service->addfunction('pingshot',
            [
                self::$service->param('ping_param', 'string'),
            ],null,
            'Description :Prueba de conexion'
        );
        self::$service->addfunction('doubleping',
            [
                self::$service->param('ping_param1', 'string'),
                self::$service->param('ping_param2', 'string'),
            ],
            [
                self::$service->param('return', 'string')
            ],
            'Description :Prueba de conexion'
        );
        
        self::$service->addfunction('GetProducto',
            [
                self::$service->param('idProducto', 'int',false,true,'id del producto')
            ],
            [
                self::$service->param('return', 'Producto',false)
            ],
            'Description :obtiene los datos de una objeto'
        );
        self::$service->addfunction('InsertProducto'
            , array(self::$service->param('Producto', 'Producto'))
            , array(self::$service->param('return', 'int'))
            , 'Description :obtiene los datos de una objeto'
        );
        self::$service->addfunction('UpdateProducto'
            , array(self::$service->param('Producto', 'Producto'))
            , array(self::$service->param('return', 'boolean'))
            , 'Description :obtiene los datos de una objeto'
        );
        self::$service->addfunction('DeleteProducto',
            [
                self::$service->param('idProducto', 'int',false,true,'id del producto')
            ],
            [
                self::$service->param('return', 'boolean',false)
            ],
            'Description :delete an product'
        );
        self::$service->addfunction('GetProductos',
            [],
            [
                self::$service->paramList('return', 'Producto',false,false,'List of products')
            ],
            'Description :Obtiene una lista de productos'
        );


    }
    public static function run() {
        $r = self::$service->run();
        echo $r;
    }
}



