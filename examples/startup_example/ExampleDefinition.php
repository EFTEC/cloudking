<?php /** @noinspection PhpUnhandledExceptionInspection */

use eftec\cloudking\CloudKing;

@session_start();

include __DIR__ . '/../../vendor/autoload.php';

class ExampleDefinition {
    public static $service;

    /**
     * You must create this file manually.
     *
     * @param bool $gui if true then it shows the web gui
     */
    public static function init($gui = true) {
        $FILE = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $NAMESPACE
            = 'http://www.examplenamespace.cl/'; // the namespace of the web service. It could be anything and not specifically an existing url
        $NAME_WS = 'ExampleHello'; // the name of the service

        self::$service = new CloudKing($FILE, $NAMESPACE, $NAME_WS);
        self::$service->allowed_input['gui'] = $gui; // set to false to disable the web gui.
        self::$service->serviceInstance = null;
        self::$service->verbose = 2; // for debug purpose
        self::$service->description = 'Example server SoapKing';

        self::$service->addfunction('hello',
            [
                self::$service->param('param', 'string', true, false),
            ],
            [
                self::$service->param('return', 'string')
            ],
            'Example of function'
        );
    }

    public static function run() {
        $r = self::$service->run();
        echo $r;
    }
}