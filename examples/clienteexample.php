<? /** @noinspection ForgottenDebugOutputInspection */

use eftec\cloudking\CloudKingClient;
use www\examplenamespace\cl\Example2WSClient;

include __DIR__.'/../vendor/autoload.php';
include __DIR__.'/service/Example2WSClient.php';

$x=new Example2WSClient();
echo "<pre>";
var_dump($x->GetProductos());
var_dump($x->doubleping('a1','a2'));
echo "\nErrors:\n";
var_dump($x->lastError);
echo "</pre>";
/***************************************** Implementation *****************************************/
