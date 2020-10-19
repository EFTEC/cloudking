<? /** @noinspection ForgottenDebugOutputInspection */

use eftec\cloudking\CloudKingClient;
use examplenamespace\Example2WSClient;

include __DIR__.'/../../vendor/autoload.php';
include 'Example2WSClient.php';

$x=new Example2WSClient();
echo "<pre>";
var_dump($x->GetProductos());
echo "\nErrors:\n";
var_dump($x->lastError);
echo "</pre>";
/***************************************** Implementation *****************************************/
