<?php /** @noinspection ForgottenDebugOutputInspection */
/** @noinspection PhpUnused */
/** @noinspection UnknownInspectionInspection */
/** @noinspection TypeUnsafeArraySearchInspection */
/** @noinspection TypeUnsafeComparisonInspection */
/** @noinspection HtmlDeprecatedAttribute */
/** @noinspection JSUnresolvedFunction */
/** @noinspection JSUndeclaredVariable */
/** @noinspection DuplicatedCode */

namespace eftec\cloudking;

use RuntimeException;

/**
 * Class CloudKing
 *
 * @copyright Jorge Castro Castillo Copyright (c)2009 - 2020, SouthProject <
 * @see       https://github.com/eftec/cloudking
 * @see       https://www.southprojects.com
 *
 * @package   eftec\cloudking
 */
class CloudKing {
    protected $version = '3.0';

    public $soap11 = true;
    public $soap12 = false;
    public $get = true;
    public $post = true;
    public $allowed_input
        = [
            'json' => true,
            'rest' => true,
            'php' => true,
            'xml' => true,
            'none' => true,
            'gui' => true,
            'phpclient' => true,
            'unity' => true
        ];
    /**
     * If the service is not set, then it will be unable to answer any call.
     *
     * @var null|object
     */
    public $serviceInstance;
    public $oem = false;
    public $encoding = 'UTF-8';
    public $custom_wsdl = '';
    public $copyright = '';
    public $description = 'CLOUDKING Server is running in this machine';
    public $verbose = 0;
    public $wsse_username = '';
    public $wsse_password = '';
    public $wsse_nonce = '';
    public $wsse_created = ''; // ISO-8859-1
    public $wsse_password_type = 'None';
    public $variable_type = 'array';
    public $allowed = array('*');
    public $disallowed = array('');
    public $folderServer = '';
    public $lastError = '';
    /** @var string port URL */
    protected $portUrl;
    /** @var string the namespace of the web service (with trailing dash) and the classes.<br>
     * Example: https://www.southprojects.com/
     */
    protected $nameSpace;
    /** @var string The name of the web service, such as "myservice" */
    protected $nameWebService;
    /** @var array ['','None','PasswordDigest','PasswordText'][$i] */
    protected $operation; //None, PasswordDigest, PasswordText
    ///** @var string=['array','object'][$i] it define if the implementation will use array (or primitives) or objects */ 
    protected $complextype;

    protected $predef_types
        = [
            'string',
            'long',
            'int',
            //'integer',
            'boolean',
            'decimal',
            'float',
            'double',
            'duration',
            'dateTime',
            'time',
            'date',
            'gYearMonth',
            'gYear',
            'gMonthDay',
            'gDay',
            'gMonth',
            'hexBinary',
            'base64Binary',
            'anyURI',
            'QName',
            'NOTATION'
        ];
    protected $predef_typesNS
        = [
            'string',
            'long',
            'int',
            //'integer',
            'boolean',
            'decimal',
            'float',
            'double',
            'duration',
            'dateTime',
            'time',
            'date',
            'gYearMonth',
            'gYear',
            'gMonthDay',
            'gDay',
            'gMonth',
            'hexBinary',
            'base64Binary',
            'anyURI',
            'QName',
            'NOTATION'
        ];
    protected $predef_types_num
        = array(
            'long',
            'int',
            'integer',
            'boolean',
            'decimal',
            'float',
            'double',
            'duration'
        );

    /**
     * CloudKing constructor.
     *
     * @param string $portUrl   The port url where we will do the connection.
     * @param string $nameSpace with trailing slash
     * @param string $nameWebService
     */
    public function __construct($portUrl='', $nameSpace = 'http://test-uri/', $nameWebService = 'CKService1') {
        if($portUrl==='') {
            if(!isset($_SERVER['SERVER_NAME'])) {
                $portUrl = $_SERVER['SCRIPT_NAME'];
            } else {
                $portUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
            }
            
        }
        
        if (@$_SERVER['HTTPS']) {
            $portUrl = str_replace('http://', 'https://', $portUrl);
        }
        $this->portUrl = $portUrl;

        $this->nameSpace = ($nameSpace != '') ? $nameSpace : $portUrl . '/';
        $this->nameWebService = ($nameWebService != '') ? $nameWebService : 'CKService1';
        $this->operation = array();
        $this->complextype = array();
        $this->custom_wsdl = $this->portUrl . '?wsdl';
    }

    /**
     * We add an argument that returns a list of complex.
     *
     * @param string $name        Name of the argument
     * @param string $type        Type of the complex argument tns:NameComplex or NameComplex
     * @param int    $minOccurs   Minimum ocurrence,if 0 it means that the value is not required.
     * @param string $maxOccurs   Maximum ocurrences. If "unbounded" means unlimited.
     * @param false  $byref       If the value will be returned.
     * @param string $description (optional) the description of the web service.
     *
     * @return array
     */
    public static function argList(
        $name = 'return',
        $type = 'tns:*',
        $minOccurs = 0
        ,
        $maxOccurs = 'unbounded',
        $byref = false,
        $description = ''
    ) {
        $type = strpos($type, ':') === false ? 'tns:' . $type : $type;
        return [
            'name' => $name,
            'type' => $type,
            'minOccurs' => $minOccurs
            ,
            'maxOccurs' => $maxOccurs,
            'byref' => $byref,
            'description' => $description
        ];
    }

    /**
     * @param string $nameParam the name of the parameter. If it is return, then uses 'return'
     * @param string $type      =$this->predef_typesNS[$i]
     * @param false  $byref
     * @param false  $required
     * @param string $description
     *
     * @return array
     */
    public function paramList(
        $nameParam = 'return',
        $type = '',
        $byref = false,
        $required = false
        ,
        $description = ''
    ) {

        if (in_array($type, $this->predef_typesNS)) {
            $primitive = true;
            $prefix = 's:';
        } else {
            $found = $this->findComplexByName($type);
            if ($found === false) {
                throw new RuntimeException("paramList: Type [$type] not defined");
            }
            $primitive = false;
            $prefix = 'tns:';
        }
        if (!$primitive) {
            $newType = 'ArrayOf' . $type;
            $found = $this->findComplexByName($newType);
            if (!$found) {
                // we add a new complex type
                $this->addtype($newType, array(
                    self::argList($type, $prefix . $type, '0', 'unbounded')
                ));
            }
        } else {
            $newType = 'ArrayOf' . $type;
        }
        return self::argList($nameParam, $newType, ($required ? 1 : 0), 1, $byref, $description);
    }

    /**
     * @param string $nameParam
     * @param string $type =$this->predef_typesNS[$i]
     * @param false  $byref
     * @param false  $required
     * @param string $description
     *
     * @return array
     */
    public function param($nameParam, $type, $byref = false, $required = false, $description = '') {
        if (in_array($type, $this->predef_typesNS)) {
            $prefix = 's:';
        } else {
            $found = $this->findComplexByName($type);
            if ($found === false) {
                throw new RuntimeException("paramList: Type [$type] not defined");
            }
            $prefix = 'tns:';
        }
        return self::argPrim($nameParam, $prefix . $type, $byref, $required, $description);
    }

    /**
     * It finds a complex type. If not found then it returns false.
     *
     * @param string $nameType without namespace
     *
     * @return false|mixed
     */
    private function findComplexByName($nameType) {
        if (isset($this->complextype[$nameType])) {
            return $this->complextype[$nameType];
        }
        return false;
    }

    private function findOperationByName($nameOp) {
        if (isset($this->operation[$nameOp])) {
            return $this->operation[$nameOp];
        }
        return false;
    }

    /**
     * We add a primary argument
     *
     * @param string $name        Name of the argument
     * @param string $type        =$this->predef_typesNS[$i] It could be "s:int" or "int"
     * @param bool   $byref       if true then it returns the value
     * @param bool   $required    if true then the value is required
     * @param string $description (optional) the description
     *
     * @return array
     * @see \eftec\cloudking\CloudKing::$predef_typesNS
     */
    public static function argPrim(
        $name = 'return',
        $type = 's:*',
        $byref = false,
        $required = false,
        $description = ''
    ) {
        $type = strpos($type, ':') === false ? 's:' . $type : $type;
        return [
            'name' => $name,
            'type' => $type,
            'byref' => $byref
            ,
            'minOccurs' => ($required) ? '1' : '0',
            'description' => $description
        ];
    }

    /**
     * We add a complex argument (Comple arguments are added with the method addType)
     *
     * @param string $name  Name of the argument
     * @param string $type  Type of the complex argument (SOAP specification)
     * @param false  $byref if true then it returns the value
     * @param bool   $required
     * @param string $description
     *
     * @return array
     */
    public static function argComplex(
        $name = 'return',
        $type = 'tns:*',
        $byref = false
        ,
        $required = false,
        $description = ''
    ) {
        $type = strpos($type, ':') === false ? 'tns:' . $type : $type;
        return [
            'name' => $name,
            'type' => $type,
            'byref' => $byref
            ,
            'minOccurs' => ($required) ? '1' : '0',
            'description' => $description
        ];
    }

    public function set_copyright($copyright) {
        $this->copyright = $copyright;
    }

    public function save_wsdl($filename) {
        return $this->_save_wsdl($filename);
    }

    public function password_correct($password, $type = 'None') {
        if ($type != $this->wsse_password_type) {
            return false; // method not equal
        }
        if ($this->wsse_password_type === 'PasswordDigest') {
            $wsse_nonce = $this->wsse_nonce;
            $wsse_created = $this->wsse_created;
            $nonce = base64_decode($wsse_nonce);
            $password_digest = base64_encode(sha1($nonce . $wsse_created . $password, true));
            return ($this->wsse_password == $password_digest);
        }
        if ($this->wsse_password_type === 'PasswordText') {
            return ($this->wsse_password == $password);
        }
        return true;
    }

    public function run() {
        $result = '';
        if (!$this->security()) {
            return false;
        }
        global $_REQUEST;
        $param = @$_SERVER['QUERY_STRING'] . '&=';

        $p = strpos($param, '&');
        $p1 = strpos($param, '=');
        $paraminit = substr($param, 0, min($p, $p1)); // ?{value}&other=aaa

        //$HTTP_RAW_POST_DATA=@$GLOBALS['HTTP_RAW_POST_DATA'];
        $HTTP_RAW_POST_DATA = file_get_contents('php://input');

        $isget = isset($_SERVER['REQUEST_METHOD']) 
            ? $_SERVER['REQUEST_METHOD'] === 'GET' 
            : true;
        

        //$info = explode('/', @$_SERVER['PATH_INFO']);
        //$functionName = (count($info) >= 2) ? $info[1] : '';
        //$functionTypeOut = (count($info) >= 3) ? $info[2] : $methodcalled;

        $functionName = isset($_GET['_op']) ? $_GET['_op'] : '';
        $functionTypeOut = isset($_GET['_out']) ? $_GET['_out'] : $paraminit;

        $methoddefined = false;
        if ($this->soap12 || $this->soap11) {
            /*if (!$paraminit && $functionName !== '' && $isget) {
                // mypage.php/functioname?param1=......
                $paraminit = 'soap';
                //$functionTypeOut = 'xml';
                $isget = true;
                $methoddefined = true;
            }*/
            // url.php? (POST)
            if ($paraminit === '' && $functionName === '' && !$isget) {
                $paraminit = 'soap';
            }
            /*if (!$paraminit && $functionName !== '' && $this->post) {
                // mypage.php/functioname (it must be soap http post).
                $paraminit = 'soap';
                $functionTypeOut = 'xml';
                $HTTP_RAW_POST_DATA = ' '; // only for evaluation.
                $isget = false;
                $methoddefined = true;
            }*/
        }
        if (!@$this->allowed_input[$paraminit] && $methoddefined) {

            trigger_error("method <b>$paraminit</b> not allowed. Did you use SOAP 1.1 or 1.2?", E_USER_ERROR);
        }

        if ($this->folderServer) {
            @$save = @$_GET['save'];
        } else {
            $save = null;
        }
        switch ($paraminit) {
            case 'soap':
                $res = $this->requestSOAP($HTTP_RAW_POST_DATA);
                $result .= $res;
                return $result;
            case 'json':
            case 'rest':
            case 'php':
            case 'xml':
            case 'none':

                $res = $this->requestNoSOAP($functionName, $functionTypeOut, $paraminit, $isget, $HTTP_RAW_POST_DATA);
                $result .= $res;
                return $result;

            case 'wsdl':
                header('content-type:text/xml;charset=' . $this->encoding);
                $result .= $this->genwsdl();
                return $result;
            case 'source':
                if ($this->verbose >= 2) {
                    $source = @$_GET['source'];
                    if (!$source) {
                        header('content-type:text/html');
                        $result .= $this->source();
                        break;
                    }
                    if (!isset($this->allowed_input[$source])) {
                        $result .= 'Method not allowed';
                        return $result;
                    }
                    if ($this->allowed_input[$source] === false) {
                        $result .= 'Method not allowed';
                        return $result;
                    }
                    switch ($source) {
                        case 'php':
                            header('content-type:text/plain;charset=' . $this->encoding);
                            $result .= $this->generateServiceClass(true);
                            if ($save) {
                                echo "┌───────────────────────┐\n";
                                echo "│        Saving         │\n";
                                echo "└───────────────────────┘\n";
                                $folder = $this->folderServer . '\service';
                                if (@!mkdir($folder) && !is_dir($folder)) {
                                    $this->lastError = "Directory $folder was not created";
                                    return '';
                                }
                                $file = "$folder\\I{$this->nameWebService}Service.php";
                                $r = file_put_contents($file, "<?php\n" . $result);
                                if ($r) {
                                    echo "Info: File $file saved\n";
                                } else {
                                    echo "Error: unable to save file $file\n";
                                }
                                $file = "$folder\\{$this->nameWebService}Service.php";
                                if (!file_exists($file)) {
                                    $r = file_put_contents($file, "<?php\n" . $this->generateServiceClass());
                                    if ($r) {
                                        echo "Info: File $file saved\n";
                                    } else {
                                        echo "Error: unable to save file $file\n";
                                    }
                                } else {
                                    echo "Note: File $file already exist, skipped\n";
                                }
                                $file = "$folder\\{$this->nameWebService}Client.php";
                                $result = $this->genphpclient();
                                $r = file_put_contents($file, $result);
                                if ($r) {
                                    echo "Info: File $file saved\n";
                                } else {
                                    echo "Error: unable to save file $file\n";
                                }

                                echo "┌───────────────────────┐\n";
                                echo "│         Done          │\n";
                                echo "└───────────────────────┘\n";
                            }
                            break;
                        case 'phpclient':
                            header('content-type:text/plain;charset=' . $this->encoding);
                            $result .= $this->genphpclient();
                            break;
                        case 'unity':
                            header('content-type:text/plain;charset=' . $this->encoding);
                            $result .= $this->genunitycsharp();
                            break;

                    }
                    return $result;
                }
                break;
            case 'unitycsharp':
                if ($this->verbose >= 2) {
                    header('content-type:text/plain;charset=' . $this->encoding);
                    $result .= $this->genunitycsharp();
                    return $result;
                }
                break;
        }
        if ($this->allowed_input['gui'] === true) {
            $result .= $this->gen_description();
        } else {
            $result .= $this->html_header();
            $result .= 'Name Web Service :' . $this->nameWebService . '<br>';
            $result .= 'Method not allowed<br>';
            $result .= $this->html_footer();
        }
        return $result;
    }

    private function security() {
        $ip =(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:'0.0.0.0';
        $hostname = gethostbyaddr($ip);
        foreach ($this->disallowed as $value) {
            if ($value == $hostname || $value == $ip) {
                echo("host $ip $hostname not allowed (blacklist)\n");
                return false;
            }
        }
        foreach ($this->allowed as $value) {
            if ($value === '*' || $value == $hostname || $value == $ip) {
                return true;
            }
        }
        echo("host $ip $hostname not allowed \n");
        return false;
    }

    /**
     * It is called when we request a SOAP (a client called our web service as SOAP)
     *
     * @param $HTTP_RAW_POST_DATA
     *
     * @return string|string[]
     */
    private function requestSOAP($HTTP_RAW_POST_DATA) {
        global $param, $resultService;
        $soapenv = '';
        if ($this->soap11 && strpos($HTTP_RAW_POST_DATA, 'http://schemas.xmlsoap.org/soap/envelope/')) {
            header('content-type:text/xml;charset=' . $this->encoding);
            $soapenv = 'http://schemas.xmlsoap.org/soap/envelope/';
        }
        if ($this->soap12 && strpos($HTTP_RAW_POST_DATA, 'http://www.w3.org/2003/05/soap-envelope')) {
            header('content-type:application/soap+xml;charset=' . $this->encoding);
            $soapenv = 'http://www.w3.org/2003/05/soap-envelope';
        }
        if (!$soapenv) {
            die('soap incorrect or not allowed');
        }
        $arr = $this->xml2array($HTTP_RAW_POST_DATA);

        $this->wsse_username = @$arr['Envelope']['Header']['Security']['UsernameToken']['Username'];
        $this->wsse_password = @$arr['Envelope']['Header']['Security']['UsernameToken']['Password'];
        $this->wsse_nonce = @$arr['Envelope']['Header']['Security']['UsernameToken']['Nonce'];
        $this->wsse_created = @$arr['Envelope']['Header']['Security']['UsernameToken']['Created'];
        $tmp = @$arr['Envelope']['Header']['Security']['UsernameToken']['Password_attr']['type'];

        if (strpos($tmp, '#PasswordText')) {
            $this->wsse_password_type = 'PasswordText';
        } elseif (strpos($tmp, '#PasswordDigest')) {
            $this->wsse_password_type = 'PasswordDigest';
        } else {
            $this->wsse_password_type = 'None';
        }
        $body = $arr['Envelope']['Body'];

        $funcion = array_keys($body);
        $functionNameXML = $funcion[0]; // as expressed in the xml
        $functionName = $this->fixtag($functionNameXML); // "tem:getSix" (deberia ser solo una funcion?)
        // pasar los parametros
        $param = array();

        $i = 0;
        $myOperation = $this->findOperationByName($functionName);
        if ($myOperation !== false) {

            foreach ($myOperation['in'] as $key => $value) {
                $param[] = @$body[$functionNameXML][$key];

                if (empty($param[$i])) {
                    $param[$i] = '';
                }

                $i++;
            }

            if ($this->variable_type === 'object') {
                // convert all parameters in classes.
                foreach ($param as $nameOperation => $value) {
                    $classname = $myOperation['in'][$nameOperation]['type'];

                    if (strpos($classname, 'tns:', 0) !== false) {

                        $param[$nameOperation] = $this->array2class($value, $this->fixtag($classname));
                    }

                }
            }
            //$param_count = count($param);

            $resultService = '';

            $errorSOAP = false;

            if ($this->serviceInstance === null) {
                $errorSOAP = $this->returnErrorSOAP('Caught exception: no service instance'
                    , 'Caught exception: no service instance', 'Server');
            } elseif (method_exists($this->serviceInstance, $functionName)) {
                try {

                    $resultService = $this->serviceInstance->$functionName(...$param);
                } catch (RuntimeException $ex) {
                    $errorSOAP = $this->returnErrorSOAP('Caught exception: ' . $ex->getMessage(),
                        'Caught exception: ' . $ex->getMessage(), 'Server');
                }
            } else {
                $errorSOAP = $this->returnErrorSOAP('Caught exception: method not defined ',
                    'Caught exception: method not defined in service', 'Server');
            }
        } else {
            $errorSOAP = $this->returnErrorSOAP('Caught exception: method not defined ',
                'Caught exception: method not defined in wsdl', 'Server');
        }

        if (is_array($resultService)) {

            //var_dump($myOperation);

            $objectName = @$myOperation['out']['name'];
            $classname = $objectName ? $myOperation['out']['type'] : '';

            //var_dump($objectName);
            //var_dump($r);
            // the \n is for fixarray2xml

            if (strpos($classname, 'tns:ArrayOf') === 0) {
                $serial = "\n" . $this->array2xmlList($resultService, $classname)
                    . "\n"; // the last \n is important to cut the last value
            } else {
                $serial = "\n" . $this->array2xml($resultService, 'array', false, false, $classname)
                    . "\n"; // the last \n is important to cut the last value
            }

            //var_dump($serial);
            $l = strlen($serial);
            if (($l > 2) && $serial[$l - 1] === "\n") {
                $serial = substr($serial, 0, $l - 1);
            }
            $serial = $this->fixarray2xml($serial);

        } else {
            $serial = $resultService;
        }
        // agregamos si tiene valor byref.
        $extrabyref = '';
        $indice = 0;

        foreach ($myOperation['in'] as $key2 => $value2) {
            if (@$value2['byref']) {
                $paramtmp = @$param[$indice];
                if (is_array($paramtmp)) {
                    $tmp2 = $this->array2xml($paramtmp, 'array', false, false);
                    $tmp2 = $this->fixarray2xml($tmp2);
                } else {
                    $tmp2 = $paramtmp;
                }
                $extrabyref .= '<' . $key2 . '>' . $tmp2 . '</' . $key2 . '>';
            }
            $indice++;
        }

        if ($errorSOAP === false) {
            // no error
            $resultado = '<?xml version="1.0" encoding="' . $this->encoding . '"?>';
            $resultado .= "<soap:Envelope xmlns:soap=\"{$soapenv}\" " .
                'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
                'xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>';
            $resultado .= "\n<{$functionName}Response xmlns=\"{$this->nameSpace}\">";
            $resultado .= "<{$functionName}Result>{$serial}</{$functionName}Result>";
            $resultado .= $extrabyref;
            $resultado .= "</{$functionName}Response>";
            $resultado .= '</soap:Body>';
            $resultado .= '</soap:Envelope>';
        } else {
            // error
            $resultado = '<?xml version="1.0" encoding="' . $this->encoding . '"?>';
            $resultado .= $errorSOAP;
        }

        return $resultado;
    }

    /**
     * @param string $reason
     * @param string $message
     * @param string $code =['VersionMismatch','MustUnderstand','Client','Server'][$i]
     *
     * @return string
     */
    public function returnErrorSOAP($reason, $message, $code) {

        if ($this->soap12) {
            $xml = <<<TAG
<env:Envelope xmlns:env=http://www.w3.org/2003/05/soap-envelope>
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>env:$code</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text>$reason<env:Text>
         </env:Reason>
         <env:Detail>
            <e:myFaultDetails 
               xmlns:e={$this->nameSpace}>
               <e:message>$message</e:message>
               <e:errorcode>$code</e:errorcode>
            </e:myFaultDetails>
         </env:Detail>
      </env:Fault>
   </env:Body>
</env:Envelope>
TAG;
        } else {
            $xml = <<<TAG
<soap:Envelope 
    xmlns:soap='http://schemas.xmlsoap.org/soap/envelope'>
   <soap:Body>
      <soap:Fault>
         <faultcode>soap:Server</faultcode>
         <faultstring>
            $message
         </faultstring>
         <faultactor>
            {$this->nameSpace}
         </faultactor>
      </soap:Fault>
   </soap:Body>
</soap:Envelope>
TAG;

        }
        return $xml;
    }

    public function xml2array($xml) {
        $parentKey = [];
        $result = [];
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'utf-8');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        @xml_parse_into_struct($parser, trim($xml), $xmls);
        xml_parser_free($parser);

        foreach ($xmls as $x) {
            $type = @$x['type'];
            $level = @$x['level'];
            $value = @$x['value'];
            $tag = @$x['tag'];
            switch ($type) {
                case 'open':
                    $ns = $this->separateNS($tag);
                    /*if(!isset($parentKey[$level])) {
                        $parentKey[$level]=$ns;
                    } else {
                        $parentKey[$level]++;
                    }
                    */
                    $parentKey[$level] = $ns;
                    $this->addElementArray($result, $parentKey, $level, $ns, []);
                    break;
                case 'close':
                    //$parentKey[$level]++;
                    break;
                case 'complete':
                    $this->addElementArray($result, $parentKey, $level, $tag, $value);
                    break;
            }
        }
        return $result;
    }

    /**
     * We add a new element to our result array<br>
     * <b>Example</b>:<br>
     * <pre>
     * $this->addElementArray($arr,['level1','level2'],3,'newlevel','hello']);
     * // $arr=['level1']['level2']['newlevel']=>'hello'
     * </pre>
     *
     * @param array  $array The result array.
     * @param array  $keys  the parent node
     * @param int    $level the level where we will add a new node
     * @param string $tag   the tag of the new node
     * @param mixed  $value the value of the new node
     */
    private function addElementArray(&$array, $keys, $level, $tag, $value) {
        // we removed any namespace <exam:GetProducto> -> <GetProducto>
        foreach ($keys as $k => $v) {
            if (strpos($v, ':') !== false) {
                $keys[$k] = explode(':', $v, 2)[1];
            }
        }
        if (strpos($tag, ':') !== false) {
            $tag = explode(':', $tag, 2)[1];
        }
        // we store the value form the xml in our array.
        switch ($level) {
            case 1:
                $array[$tag] = $value;
                break;
            case 2:
                $array[$keys[1]][$tag] = $value;
                break;
            case 3:
                $array[$keys[1]][$keys[2]][$tag] = $value;
                break;
            case 4:
                $array[$keys[1]][$keys[2]][$keys[3]][$tag] = $value;
                break;
            case 5:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$tag] = $value;
                break;
            case 6:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$tag] = $value;
                break;
            case 7:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$tag]
                    = $value;
                break;
            case 8:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$tag]
                    = $value;
                break;
            case 9:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$tag]
                    = $value;
                break;
            case 10:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$keys[9]][$tag]
                    = $value;
                break;
            case 11:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$keys[9]][$keys[10]][$tag]
                    = $value;
                break;
            case 12:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$keys[9]][$keys[10]][$keys[11]][$tag]
                    = $value;
                break;
        }
    }

    private function array2class($arr, $newclass) {
        if ($arr == null) {
            return null;
        }
        $object = (object)$arr;
        if (!class_exists($newclass)) {
            // We'll save unserialize the work of triggering an error if the class does not exist
            trigger_error('Class ' . $newclass . ' not found', E_USER_ERROR);
        }
        $serialized_parts = explode(':', serialize($object));
        $serialized_parts[1] = strlen($newclass);
        $serialized_parts[2] = '"' . $newclass . '"';
        $result = unserialize(implode(':', $serialized_parts));
        // aqui recorremos los miembros
        $complex = $this->findComplexByName($newclass);
        if ($complex === false) {
            trigger_error('Complex Type ' . $newclass . ' not found', E_USER_ERROR);
        }
        foreach ($complex as $nameComplex => $value) {
            if (strpos($value['type'], 'tns:', 0) !== false) {
                $result->$nameComplex = $this->array2class($result->$nameComplex
                    , $this->fixtag($value['type']));
            }
        }

        return $result;
    }

    private function class2array($class, $classname) {
        if (is_object($class)) {
            $resultado = (array)$class;

            $complex = $this->findComplexByName($classname);
            foreach ($complex as $nameComplex => $value) {
                if (strpos($value['type'], 'tns:', 0) !== false) {
                    $this->class2array($resultado[$nameComplex], $this->fixtag($value['type']));
                    //$tmp=$this->class2array($resultado[$value['name']], $this->fixtag($value['type']));
                    //if ($tmp != "") {
                    //$resultado[$value['name']]=$tmp;
                    //}
                }
            }
        } else {
            $resultado[$classname] = $class;
        }
        return $resultado;
    }

    protected function fixtag($tag) {
        $arr = explode(':', $tag);
        return ($arr[count($arr) - 1]);
    }

    /**
     * @param $tnsName
     *
     * @return mixed|string
     */
    private function separateNS($tnsName) {
        if (strpos($tnsName, ':') === false) {
            return $tnsName;
        }
        $r = explode(':', $tnsName, 2);
        return $r[1];
    }

    /**
     * It generates a xml form an array.
     *
     * @param string|array $array       the input array
     * @param string       $name        The name of the first node
     * @param bool         $contenttype if true then it shows the initial content type
     * @param bool         $start       if true then it is the first and initial node.
     * @param string       $expected    The expected node type with the namespace
     *
     * @return string
     */
    public function array2xml($array, $name = 'root', $contenttype = true, $start = true, $expected = '') {
        // \n is important, you should not remove it.
        if (!is_array($array)) {
            return $array;
        }
        //var_dump($expected);
        //var_dump($name);
        //var_dump($start);
        if (strpos($expected, 'tns:') !== false) {
            //echo "expected:";
            //var_dump($expected);
            $complex = $this->findComplexByName(str_replace('tns:', '', $expected));
            //echo "complex:";
            //var_dump($complex);
        } else {
            //echo "nexpected:";
            //var_dump($expected);
            $complex = null;
        }

        $xmlstr = '';
        if ($start) {
            if ($contenttype) {
                @header("content-type:text/xml;charset={$this->encoding}");
            }
            $xmlstr .= "<?xml version=\"1.0\" encoding=\"{$this->encoding}\"?>\n";
            $xmlstr .= "<{$name}>\n";
        }

        foreach ($array as $key => $child) {
            if (is_array($child)) {
                $xmlstr .= "<{$key}>\n";
                if (strpos($complex[$key]['type'], 'tns:ArrayOf') === 0) {
                    $child = $this->array2xmlList($child, $complex[$key]['type']);
                } else {
                    $child = $this->array2xml($child, '', '', false, $complex[$key]['type']);
                }

                $xmlstr .= $child;
                $xmlstr .= "</{$key}>\n";
            } else {
                $type = $this->array2xmltype($child);
                if ($this->variable_type === 'object' && is_object($child)) {
                    $xmlstr .= "<$type>" .
                        $this->array2xml($this->class2array($child, $type), $type, false, false, $key) . "</$type>\n";
                } else {
                    $xmlstr .= '<' . (is_string($key) ? $key : $type) . '>' . $child . '</' .
                        (is_string($key) ? $key : $type) . ">\n";
                }
            }
        }
        if ($start) {
            $xmlstr .= '</' . $name . ">\n";
        }
        return $xmlstr;
    }

    /** @noinspection LoopWhichDoesNotLoopInspection */
    private function array_key_first($arr) {
        foreach ($arr as $key => $unused) {
            return $key;
        }
        return null;
    }

    public function array2xmlList($array, $expected = '') {
        // \n is important, you should not remove it.
        if (!is_array($array)) {
            return $array;
        }
        $xmlstr = '';
        $keyName = str_replace('tns:', '', $expected);
        $complex = $this->findComplexByName($keyName);
        $firstKey = $this->array_key_first($complex);
        //var_dump('list');
        //var_dump($complex);
        foreach ($array as $idx => $value) {
            $xmlstr .= "<$firstKey>" . $this->array2xml($value, '', '', false, $complex[$firstKey]['type'])
                . "</$firstKey>\n";
        }
        return $xmlstr;
    }

    public function array2xmltype($value) {
        if (is_float($value)) {
            return 'float';
        }
        if (is_int($value)) {
            return 'int';
        }
        if (is_object($value)) {
            return get_class($value);
        }
        return 'string';
    }

    private function fixarray2xml($string) {
        //if (!is_array($string)) { return $string; }
        // we remove the first && last element of the xml file so.

        $arr = explode("\n", $string);

        $resultado = '';
        for ($i = 1; $i < count($arr) - 1; $i++) {
            $l = trim($arr[$i]);
            $lant = trim($arr[$i - 1]);
            if ($l != $lant) {
                $resultado .= $arr[$i] . "\n";
            }
        }

        return $resultado;
    }

    /**
     * Process a non-soap request into an array.
     *
     * @param string $functionName    the name of the function
     * @param string $functionTypeOut =['json','xml','php','none'][$i]
     * @param string $methodcalled    =['soap','wsdl','json','rest','php','xml','none'][$i]
     * @param string $isget
     * @param array  $body
     *
     * @return false|string
     */
    private function requestNoSOAP($functionName, $functionTypeOut, $methodcalled, $isget, $body = array()) {
        $this->wsse_username = @$_POST['Username'];
        $this->wsse_password = @$_POST['Password'];
        $this->wsse_nonce = @$_POST['Nonce'];
        $this->wsse_created = @$_POST['Created'];
        $tmp = @$_POST['type'];

        if (strpos($tmp, '#PasswordText')) {
            $this->wsse_password_type = 'PasswordText';
        } elseif (strpos($tmp, '#PasswordDigest')) {
            $this->wsse_password_type = 'PasswordDigest';
        } else {
            $this->wsse_password_type = 'None';
        }

        $operationN = $this->findOperationByName($functionName);
        if ($operationN != false) {
            $operation = $operationN;
            if ($isget) {
                $param = $_GET;
            } else {
                $param = $this->decodeNOSOAP($methodcalled, $body);
            }
            // order and filter the arguments.
            $arguments = [];
            $argumentCounter = 0;
            $paramxArgument = [];
            foreach ($operation['in'] as $nameArg => $value) {
                if (isset($param[$nameArg])) {
                    $arguments[$argumentCounter] = $param[$nameArg];

                    $paramxArgument[$nameArg] = $argumentCounter;
                    $argumentCounter++;
                } else {
                    $arguments[$argumentCounter] = null;
                    $paramxArgument[$nameArg] = $argumentCounter;
                    $argumentCounter++;
                }
            }
            try {
                $er = $this->serviceInstance->$functionName(...$arguments);
            } catch (RuntimeException $ex) {
                return $this->returnErrorNoSOAP($functionTypeOut, 'Caught exception: ' . $ex->getMessage(),
                    'Caught exception: ' . $ex->getMessage(), 'Server');
            }
            $evalret = null;
            if (count($operation['out'])) {
                $returnArgName = $operation['out']['name'];
                $evalret[$returnArgName] = $er;
            }
            foreach ($operation['in'] as $nameArg => $value) {
                if (isset($paramxArgument[$nameArg]) && $value['byref']) {
                    $evalret[$nameArg] = $arguments[$paramxArgument[$nameArg]];
                } else {
                    $evalret[$nameArg] = null;
                }
            }
        } else {
            return $this->returnErrorNoSOAP($functionTypeOut, 'Caught exception: function not defined'
                , 'Caught exception: function not defined', 'Server');
        }
        return $this->encodeNOSOAP($functionTypeOut, $evalret, $functionName . 'Result');
    }

    private function returnErrorNoSOAP($methodcalled, $reason, $message, $code) {
        return $this->decodeNOSOAP($methodcalled,
            ['origin' => 'Cloudking Error', 'reason' => $reason, 'message' => $message, 'code' => $code]);
    }

    private function decodeNOSOAP($methodcalled, $tmpvalue) {
        // pass json/xml/php/raw --> return a array or value.
        $tmp = '';
        switch ($methodcalled) {
            case 'json':
                $tmp = json_decode($tmpvalue, true);
                break;
            case 'xml':
                $this->xml2array($tmpvalue);
                break;
            case 'php':
                $tmp = @unserialize($tmpvalue);
                break;
            case 'none':
                /*if (is_array($tmpvalue)) {
                    $tmpvalue=ex(",",$tmpvalue);
                }
                */ $tmp = $tmpvalue; // urlencode is done
                break;
        }
        return $tmp;
    }

    private function encodeNOSOAP($functionTypeOut, $tmpvalue, $tmpname) {
        $tmp = '';
        switch ($functionTypeOut) {
            case 'json':
                @header('Content-type: application/json');
                $tmp = json_encode($tmpvalue);
                break;
            case 'xml':
                if (!is_array($tmpvalue)) {
                    @header('content-type:text/xml;charset=' . $this->encoding);
                    $tmp = "<?xml version=\"1.0\" encoding=\"{$this->encoding}\"?>\n";
                    $tmp .= "<$tmpname>$tmpvalue</$tmpname>";
                } else {
                    $tmp = $this->array2xml($tmpvalue, 'array', true, true);
                }
                $tmp = $this->fixarray2xml($tmp);

                break;
            case 'php':
                $tmp = serialize($tmpvalue);
                break;
            case 'none':
                if (is_array($tmpvalue)) {
                    $tmp = '';
                    foreach ($tmpvalue as $key => $value) {
                        $tmp .= $key . '=' . $value . '&';
                    }
                    $tmpvalue = $tmp;
                    $tmpvalue = substr($tmpvalue, 0, -1);
                }
                $tmp = $tmpvalue;
                break;
        }
        return $tmp;
    }

    protected function generateServiceClass($interface = false) {
        $namespacephp = str_replace(['http://', 'https://', '.', '/'], ['', '', '\\', '\\'], $this->nameSpace);
        $namespacephp = rtrim($namespacephp, '\\');
        if ($interface) {
            $r = <<<cin
namespace $namespacephp\service;
/**
 * @generated The structure of this interface is generated by CloudKing
 * Interface I{$this->nameWebService}Service
 */
interface I{$this->nameWebService}Service {
cin;
        } else {
            $r = <<<cin
namespace $namespacephp\service;
// file:{$this->nameWebService}Service.php
/**
 * @generated The structure of this class is generated by CloudKing
 * Class {$this->nameWebService}Service
 */
class {$this->nameWebService}Service implements I{$this->nameWebService}Service {
cin;
        }

        foreach ($this->operation as $complexName => $args) {
            $param = '';
            foreach ($args['in'] as $key2 => $value2) {
                $param .= ($value2['byref']) ? '&' : '';
                $param .= '$' . $key2 . ', ';
            }
            if ($param != '') {
                $param = $this->right($param, 2);
            }
            $phpdoc = "\t/**\n\t * {$args['description']}\n\t *\n";
            foreach ($args['in'] as $key2 => $value2) {
                $phpdoc .= "\t * @param mixed \${$key2}\n";
            }
            if (is_array($args['out']) && count($args['out']) > 0) {
                $phpdoc .= "\t *\n\t * @return mixed\n";
            }
            $phpdoc .= "\t */";
            if ($interface) {
                $r .= "\n$phpdoc\n";
                $r .= "\tpublic function {$complexName}($param);\n";
            } else {
                $r .= "\n";
                $r .= "\n\t/**\n\t * @inheritDoc\n\t */\n";
                $r .= "\tpublic function {$complexName}($param) {\n";
                $r .= "\t\t// todo:custom implementation goes here\n";
                $r .= "\t}\n";
            }

        }
        foreach ($this->complextype as $complexName => $args) {
            $params = '';
            foreach ($args as $key2 => $value2) {
                $params .= '$' . $key2 . '=null, ';
            }
            if ($params != '') {
                $params = $this->right($params, 2);
            }
            if ($interface) {
                $r .= "\n\tpublic static function factory" . $complexName . '(' . $params . ");\n";
            } else {
                $r .= "\n\tpublic static function factory" . $complexName . '(' . $params . ') {';
                $r .= "\n";
                $r .= $this->Param2PHP('tns:' . $complexName, @$args['maxOccurs']) . "\n";
                $r .= "\t\treturn \$_$complexName;\n";
                $r .= "\t}\n";
            }
        }
        $r .= "} // end class \n ";

        /*$r .= "\n" . $this->genphpast('Complex Types (Classes)');
        foreach ($this->complextype as $complexName => $args) {
            $r .= "\nclass " . $complexName . " {\n";
            foreach ($args as $key2 => $value2) {
                $r .= "\tvar $" . $key2 . '; // ' . $value2['type'] . "\n";
            }
            $r .= "}\n";
        }
        */
        return $r;
    }

    private function createArgument($argArray) {
        $param = '';
        foreach ($argArray as $key2 => $value2) {
            $param .= ($value2['byref']) ? '&' : '';
            $param .= '$' . $key2 . ', ';
        }
        if ($param != '') {
            $param = $this->right($param, 2);
        }
        return $param;
    }

    protected function generateServiceInterface() {

        $r = <<<cin
/**
 * @generated The structure of this interface is generated by CloudKing
 * Interface I{$this->nameWebService}Service
 */
interface I{$this->nameWebService}Service {
cin;

        foreach ($this->operation as $complexName => $args) {
            $param = $this->createArgument($args['in']);
            $r .= "\n";
            $r .= "\tpublic function " . $complexName . "($param);\n";
        }
        foreach ($this->complextype as $complexName => $args) {
            $params = count($args) > 0 ? '$' . implode(',$', array_keys($args)) : '';

            $r .= "\n\tpublic static function factory" . $complexName . '(' . $params . ') {';
            $r .= "\n";
            $r .= $this->Param2PHP('tns:' . $complexName, @$args['maxOccurs']) . "\n";
            $r .= "\t\treturn \$_$complexName;\n";
            $r .= "\t}\n";
        }
        $r .= "} // end class \n ";

        $r .= "\n" . $this->genphpast('Complex Types (Classes)');
        foreach ($this->complextype as $complexName => $args) {
            $r .= "\nclass " . $complexName . " {\n";
            foreach ($args as $key2 => $value2) {
                $r .= "\tvar $" . $key2 . '; // ' . $value2['type'] . "\n";
            }
            $r .= "}\n";
        }

        return $r;
    }

    protected function genphpast($text, $lenght = 100) {
        $L = ($lenght - 6 - strlen($text)) / 2;
        $L = ($L < 1) ? 1 : $L;
        $ast = str_repeat('*', $L);
        return '/*' . $ast . ' ' . $text . ' ' . $ast . "*/\n";
    }

    protected function right($string, $num_cut = 1) {
        if (strlen($string) - $num_cut >= 0) {
            return substr($string, 0, strlen($string) - $num_cut);
        }
        return $string;
    }

    protected function Param2PHPvalue($type, $max) {
        $x1 = explode(':', $type, 2);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        list($space, $name) = $x1;
        $p = '';
        if ($space === 's') {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            if (!in_array($name, $this->predef_types_num)) {

                $p = "''";
            } else {
                $p = '0';
            }
        }
        if ($space === 'tns') {
            foreach ($this->complextype as $complexName => $args) {
                if ($name === $complexName) {
                    $p = '$_' . $name;
                }
            }
            if ($p === '') {
                return "// complex type $type not found";
            }
        }
        if ($p !== '') {
            switch ($max) {
                case 'unbounded':
                    return "array($p,$p,...)";
                case '1':
                    return $p;
                default:
                    $tmp = 'array(';
                    for ($i = 0; $i < $max; $i++) {
                        $tmp .= $p . ',';
                    }
                    $tmp = $this->right($tmp, 1) . ')';
                    return ($tmp);
            }
        }
        return "\\ complex type $type not defined";
    }

    protected function Param2PHP($type, $max, $separator = ";\n", $pre = "\t\t") {
        $x1 = explode(':', $type, 2);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        list($space, $name) = $x1;
        if ($space === 's') {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            $p = $this->Param2PHPvalue($type, $max);
            if ($max === 'unbounded') {
                return "array($p,$p,...)";
            }
            if ($max == 1) {
                return $p;
            }
            $tmp = 'array(';
            for ($i = 0; $i < $max; $i++) {
                $tmp .= $p . ',';
            }
            $tmp = $this->right($tmp, 1) . ')';
            return ($tmp);
        }
        $resultado = '';
        if ($space === 'tns') {
            foreach ($this->complextype as $complexName => $args) {
                if ($name == $complexName) {

                    foreach ($args as $key2 => $value2) {
                        $resultado .= $pre . '$_' . $name . "['" . $key2 . "']=$" .
                            $key2 . $separator;
                        //$resultado.="'".$value2['name']."'=>".$this->Param2PHP($value2['type'],$value2['maxOccurs']).",";                    
                    }
                    $resultado = $this->right($resultado);
                    return ($resultado);
                }
            }
            return "\\ complex type $type not defined";
        }
        return '';
    }

    protected function Param2PHPArg($type, $max) {
        $x1 = explode(':', $type, 2);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        list($space, $name) = $x1;
        if ($space === 's') {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            $p = $this->Param2PHPvalue($type, $max);
            if ($max === 'unbounded') {
                return "array($p,$p,...)";
            }
            if ($max == 1) {
                return $p;
            }
            $tmp = 'array(';
            for ($i = 0; $i < $max; $i++) {
                $tmp .= $p . ',';
            }
            $tmp = $this->right($tmp, 1) . ')';
            return ($tmp);
        }
        $resultado = '';
        if ($space === 'tns') {
            foreach ($this->complextype as $complexName => $args) {
                if ($name === $complexName) {

                    foreach ($args as $key2 => $value2) {
                        $resultado .= '$' . $key2; // . '=' .
                        //    $this->Param2PHPValue($value2['type'], $value2['maxOccurs']) . $separator;
                        //$resultado.="'".$value2['name']."'=>".$this->Param2PHP($value2['type'],$value2['maxOccurs']).",";                    
                    }
                    $resultado = $this->right($resultado);
                    return ($resultado);
                }
            }
            return "\\ complex type $type not defined";
        }
        return '';
    }

    /**
     *
     * @return string
     */
    protected function genphpclient() {
        $namespacephp = str_replace(['http://', 'https://', '.','/'], ['', '', '\\','\\'], $this->nameSpace);
        $namespacephp=trim($namespacephp,'\\');
        $r = "<?php\n";
        $r .= "/** @noinspection DuplicatedCode\n";
        $r .= " * @noinspection UnknownInspectionInspection\n";
        $r .= " */\n";
        $r .= "namespace $namespacephp;\n";
        $r .= "use eftec\\cloudking\\CloudKingClient;\n\n";
        $r .= "/**\n";
        $r .= " * Class {$this->nameWebService}Client<br>\n";
        if ($this->description) {
            $r .= " * {$this->description}<br>\n";
        }
        $r .= " * This code was generated automatically using CloudKing v{$this->version}, Date:" . date('r')
            . " <br>\n";
        $r .= ' * Using the web:' . $this->portUrl . "?source=phpclient\n";
        $r .= " */\n";
        $r .= "class {$this->nameWebService}Client {\n";
        $r .= "\t/** @var string The full url where is the web service */\n";
        $r .= "\tprotected \$url='" . $this->portUrl . "';\n";
        $r .= "\t/** @var string The namespace of the web service */\n";
        $r .= "\tprotected \$tempuri='" . $this->nameSpace . "';\n";
        $r .= "\t/** @var string The last error. It is cleaned per call */\n";
        $r .= "\tpublic \$lastError='';\n";
        $r .= "\t/** @var float=[1.1,1.2][\$i] The SOAP used by default */\n";
        $r .= "\tprotected \$soap=1.1;\n";
        $r .= "\t/** @var CloudKingClient */\n";
        $r .= "\tpublic \$service;\n";

        $r .= "\t/**\n";
        $r .= "\t * Example2WSClient constructor.\n";
        $r .= "\t *\n";
        $r .= "\t * @param string|null \$url The full url (port) of the web service\n";
        $r .= "\t * @param string|null \$tempuri The namespace of the web service\n";
        $r .= "\t * @param float|null \$soap=[1.1,1.2][\$i] The SOAP used by default\n";
        $r .= "\t */\n";
        $r .= "\tpublic function __construct(\$url=null, \$tempuri=null, \$soap=null) {\n";
        $r .= "\t\t\$url!==null and \$this->url = \$url;\n";
        $r .= "\t\t\$tempuri!==null and \$this->tempuri = \$tempuri;\n";
        $r .= "\t\t\$soap!==null and \$this->soap = \$soap;\n";
        $r .= "\t\t\$this->service=new CloudKingClient(\$this->soap,\$this->tempuri);\n";
        $r .= "\t}\n\n";

        foreach ($this->operation as $key => $value) {
            $functionname = $key;
            if (isset($value['out']) && count($value['out']) > 0) {
                $outType = $value['out']['type'];
            } else {
                $outType = 'void';
            }
            $param = '';
            foreach ($value['in'] as $key2 => $value2) {
                $param .= ($value2['byref']) ? '&' : '';
                $param .= '$' . $key2 . ', ';

            }
            if ($param != '') {
                $param = $this->right($param, 2);
            }
            $r .= "\n\t/**\n";
            $r .= "\t * " . @$value['description'] . "\n";
            $r .= "\t *\n";
            foreach ($value['in'] as $key2 => $value2) {
                $varname = $key2;
                $r .= "\t * @param mixed \$$varname " . @$value2['description'] . ' (' . @$value2['type'] . ") \n";
            }
            $r .= "\t * @return mixed ($outType)\n";
            $r .= "\t * @noinspection PhpUnused */\n";
            $r .= "\tpublic function $functionname($param) {\n";
            $r .= "\t\t\$_param='';\n";
            foreach ($value['in'] as $key2 => $value2) {
                $varname = $key2;
                $r .= "\t\t\$_param.=\$this->service->array2xml(\$$varname,'ts:$varname',false,false);\n";
            }
            $r .= "\t\t\$resultado=\$this->service->loadurl(\$this->url,\$_param,'$functionname');\n";
            $r .= "\t\t\$this->lastError=\$this->service->lastError;\n";
            $r .= "\t\tif(!is_array(\$resultado)) {\n";
            $r .= "\t\t\treturn false; // error\n";
            $r .= "\t\t}\n";
            foreach ($value['in'] as $key2 => $value2) {
                if ($value2['byref']) {
                    $r .= "\t\t\$" . $key2 . "=@\$resultado['" . $key2 . "'];\n";
                }
            }
            $r .= "\t\treturn @\$resultado['" . $functionname . "Result'];\n";
            $r .= "\t}\n";

        }
        $r .= "} // end {$this->nameWebService}Client\n";
        return $r;
    }

    protected function genunitycsharp() {
        $r = $this->genphpast('Implementation');

        $r .= 'using UnityEngine;
		
using System.IO;
using System.Xml.Serialization;
using System;
using System.Text;
using System.Collections;
using System.Collections.Generic;

public class ' . $this->nameWebService . ' : MonoBehaviour
{
    // Use this for initialization
    private string charset = "UTF-8";
    private string url = "' . $this->portUrl . '";
    private string tempuri = "' . $this->nameSpace . '";
    private string prefixns = "ts";
	public string cookie="";	
    ';

        foreach ($this->operation as $complexName => $args) {
            foreach ($args as $argname => $v) {
                $tmpname = $argname;
                if (isset($value['out']) && count($v['out']) >= 1) {
                    $outtype = $this->fixtag($v['out']['type']);
                    $outtypereal = $this->type2csharp($outtype);
                } else {
                    $outtypereal = '';
                }
                $r .= '	
    // ' . $tmpname . '
    public Boolean is' . $tmpname . 'Running = false;
    private WWW webservice' . $tmpname . ';
    public string ' . $tmpname . 'Error="";	
	
    public ' . $outtypereal . ' ' . $tmpname . 'Result;
    // End ' . $tmpname . '
';
            }
        }
        $r .= ' 	private void Start()
	{
		return;
	}';
        foreach ($this->operation as $complexName => $args) {
            foreach ($args as $argname => $v) {
                $tmpname = $argname;
                $param = '';
                foreach ($v['in'] as $key2 => $value2) {
                    $param .= $this->fixtag($value2['type']) . ' ' . $key2 . ',';
                }
                $param = $this->right($param, 1);
                $r .= ' 
	private void ' . $tmpname . 'Async(' . $param . ')
        {
		string namefunction = "' . $tmpname . '";
		Single soapVersion=1.1f;
                string ss2 = SoapHeader(namefunction,soapVersion);';

                foreach ($v['in'] as $key2 => $value2) {
                    $name = $key2;
                    $r .= '
		ss2 += "<" + prefixns + ":' . $name . '>" + Obj2XML(' . $name . ',true) + "</" + prefixns + ":' . $name . '>";
		';
                }

                if (isset($value['out']) && count($v['out']) >= 1) {
                    $outtype = $this->fixtag($v['out']['type']);
                    $outtypereal = $this->type2csharp($outtype);
                    $outinit = $this->csharp_init($outtype);
                } else {
                    $outtype = '';
                    $outtypereal = '';
                    $outinit = '';
                }

                $r .= 'ss2 += SoapFooter(namefunction,soapVersion);
                is' . $tmpname . 'Running = true;
		StartCoroutine(' . $tmpname . 'Async2(ss2));
       }
       private IEnumerator ' . $tmpname . 'Async2(string ss2) {
		string namefunction = "' . $tmpname . '";
		Single soapVersion=1.1f;
		byte[] bb = System.Text.Encoding.UTF8.GetBytes(ss2);
		var headers = header(namefunction,soapVersion);
		if (cookie!="") {
			headers.Add("Set-Cookie",cookie);
		}
		webservice' . $tmpname . ' = new WWW(url, bb, headers);
		while( !webservice' . $tmpname . '.isDone ) {
			yield return new WaitForSeconds(0.5f);
		}
                is' . $tmpname . 'Running = false;
                string other = cleanSOAPAnswer(webservice' . $tmpname . '.text, "' . $tmpname . '",ref ' . $tmpname . 'Error);
		';
                if ($outtype != '') {
                    $r .= $tmpname . 'Result=' . $outinit . ";\n";
                    $r .= '                ' . $tmpname . 'Result=(' . $outtypereal . ')XML2Obj(other,"' . $outtype
                        . '",' .
                        $tmpname . 'Result.GetType());	
		';
                }
                $r .= 'webservice' . $tmpname . '.responseHeaders.TryGetValue("SET-COOKIE",out cookie);
		';
                $r .= $tmpname . 'AsyncDone();
	}
	public void ' . $tmpname . 'AsyncDone() {
		// we do something...';
                if ($outtype != '') {
                    $r .= '
		// ' . $outtypereal . ' dnx=' . $tmpname . 'Result;';
                }
                $r .= '
	}
	';
            }
        }

        $r .= '
	#region util_function
	private Hashtable header(string nameFunction,Single soapVersion) {
		var tmpheader=new Hashtable();
		if (soapVersion>=1.2) {
			tmpheader["Content-Type"] = "application/soap+xml;charset" + charset + ";action=\"" + url + "/" + nameFunction +"\"";
		} else {
			tmpheader["Content-Type"] = "text/xml;charset" + charset;
			tmpheader["SOAPAction"]= "\""+ tempuri + "/" + nameFunction +"\"";			
		}
		return tmpheader;
	}

	
    private string cleanSOAPAnswer(string text, string functionname,ref string last_error )
    {
        int p0, p1, pbody;
        string tmp = functionname + "Result";
        pbody = text.IndexOf("<soap:Body>");
		if (pbody<0) {
			last_error="No soap found";
			return "";	
		}
        p0 = text.IndexOf("<" + tmp, pbody);
        if (p0 <= 0)
        {
            return "";
        }
        p0 = text.IndexOf(">", p0) + 1;
        p1 = text.IndexOf("</" + tmp, p0);
        if (p1 < p0)
        {
            tmp = "";
        }
        else
        {
            tmp = text.Substring(p0, p1 - p0);
        }
        return tmp;
    }

    private string SoapHeader(string nameFunction,Single soapVersion)
    {
		string ss2;
		if (soapVersion>=1.2) {
        	ss2 = "<soap:Envelope xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\" xmlns:" + prefixns + "=\"" +
                     tempuri + "/\">";
        	ss2 += "<soap:Header/><soap:Body>";
		} else {
        	ss2 = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:" + prefixns + "=\"" +
                     tempuri + "/\">";
        	ss2 += "<soapenv:Header/><soapenv:Body>";			
		}
        ss2 += "<" + prefixns + ":" + nameFunction + ">";

        return ss2;
    }

    private string SoapFooter(string nameFunction,Single soapVersion)
    {
		string ss2;
		ss2 = "</" + prefixns + ":" + nameFunction + ">";
		if (soapVersion>=1.2f) {
       		ss2 += "</soap:Body></soap:Envelope>";
		} else {
			ss2 += "</soapenv:Body></soapenv:Envelope>";
		}
		
        return ss2;
    }
	
    private string Obj2XML(object obj)
    {
        return Obj2XML(obj, false, false);
    }

    private string Obj2XML(object obj, Boolean withns)
    {
        return Obj2XML(obj, withns, false);
    }

    private string Obj2XML(object obj, Boolean withns, Boolean full)
    {
        string myStr, types;

        if (obj.GetType().Namespace == "System")
        {
            types = obj.GetType().Name.ToLower();
            types = (withns) ? prefixns + ":" + types : types;
            if (full)
            {
                myStr = "<" + types + ">" + obj.ToString() + "</" + types + ">";
            }
            else
            {
                myStr = obj.ToString();
            }
        }
        else
        {
            var mstream = new MemoryStream();
            XmlSerializer SerializerObj = new XmlSerializer(obj.GetType(), tempuri);
            if (withns)
            {
                var namespaces = new XmlSerializerNamespaces();
                namespaces.Add(prefixns, tempuri);
                SerializerObj.Serialize(mstream, obj, namespaces);
            }
            else
            {
                SerializerObj.Serialize(mstream, obj);
            }

            mstream.Position = 0;
            var sreader = new StreamReader(mstream);
            myStr = sreader.ReadToEnd();
            // cut xml && first node element from the xml
            if (!full)
            {
                var arr = myStr.Split(new char[] {' . "'" . '\n' . "'" . '}, StringSplitOptions.None);
                //var arr2 = "";
                int lim = arr.Length - 3;
                var arr2 = String.Join("", arr, 2, lim);
                myStr = arr2;
            }
        }

        return myStr;
    }
	private object XML2Obj(string xmlstr, string typedesc, Type type) {
		return 	XML2Obj( xmlstr,  typedesc,type,false);
	}
     private object XML2Obj(string xmlstr, string typedesc, Type type,Boolean full)
    {
        if (!full)
        {
            xmlstr = "<?xml version=\"1.0\" ?>" + "<" + typedesc + ">" + xmlstr + "</" + typedesc + ">";
        }
        var objdummy = new object();
        var SerializerObj = new XmlSerializer(type);

        byte[] byteArray = Encoding.ASCII.GetBytes(xmlstr);
        objdummy = SerializerObj.Deserialize(new MemoryStream(byteArray));

        return objdummy;
    }
	#endregion
} // end class.
	';

        $r .= "\n" . $this->genphpast('Complex Types (Classes)');
        foreach ($this->complextype as $complexName => $args) {
            $type = $this->fixtag(@$args['type']);

            if (strlen($complexName) <= 7 || strpos($complexName, 'ArrayOf') === 0) {
                $r .= "\npublic class " . $complexName . " {\n";
                foreach ($args as $key2 => $value2) {
                    $r .= "\tprivate " . $type . ' _' . $key2 . "; \n";
                }
                $r .= "\n";
                foreach ($args as $key2 => $value2) {
                    $r .= "\tpublic {$type} {$key2}\n";
                    $r .= "\t{\n";
                    $r .= "\t\tget { return _{$key2}; }\n";
                    $r .= "\t\tset { _{$key2} = value; }\n";
                    $r .= "\t}\n";
                }
                $r .= "}\n";
            }
        }

        return ($r);
    }

    protected function type2csharp($type) {
        // ArrayOfS
        // 12345678
        $l = strlen($type);

        if ($l > 8 && strpos($type, 'ArrayOf') === 0) {
            $type = 'List<' . substr($type, 7, $l - 7) . '>';
            return $this->type2csharp($type);
        }

        return $type;
    }

    protected function csharp_init($type) {
        // ArrayOfS
        // 12345678
        switch ($type) {
            case 'string':
            case 'String':
                return '""';
            case 'int':
            case 'long':
            case 'Single':
                return '0';
            default:
                return 'new ' . ($this->type2csharp($type)) . '()';
        }
    }

    protected function source() {
        $result = $this->html_header();
        $result .= '<br><h3>List of Operations</h3><ul>';
        $result .= "<li><a href='" . $this->portUrl . "?source=unity'>Unity (C#) Client Source</a></li>";
        $result .= "<li><a href='" . $this->portUrl . "?source=php'>View PHP Server Source</a></li>";
        if ($this->folderServer) {
            $result .= "<li><a href='" . $this->portUrl
                . "?source=php&save=true'>PHP Server Source (save in folder {$this->folderServer})</a></li>";
        }
        $result .= "<li><a href='" . $this->portUrl . "?source=phpclient'>PHP Source Client</a></li>";
        $result .= '</ul>';
        $result .= $this->html_footer();

        return $result;
    }

    protected function html_header() {
        $r = '<header>';
        $r .= '<title>' . $this->nameWebService . "</title>\n";

        $r .= "<style type=\"text/css\">\n";

        $r .= ".heading1 { color: #ffffff; font-family: Tahoma; font-size: 26px; font-weight: normal; background-color: #F88017; margin-top: 0px; margin-bottom: 0px; margin-left: -30px; padding-top: 10px; padding-bottom: 3px; padding-left: 15px; width: 105%; }\n";
        $r .= "BODY { color: #000000; background-color: white; font-family: Verdana; margin-left: 0px; margin-top: 0px; }\n";
        $r .= "#content { margin-left: 30px; font-size: .70em; padding-bottom: 2em; }\n";
        $r .= "A:link { color: #336699; font-weight: bold; text-decoration: underline; }\n";
        $r .= "A:visited { color: #6699cc; font-weight: bold; text-decoration: underline; }\n";
        $r .= "A:active { color: #336699; font-weight: bold; text-decoration: underline; }\n";
        $r .= "A:hover { color: cc3300; font-weight: bold; text-decoration: underline; }\n";
        $r .= "P { color: #000000; margin-top: 0px; margin-bottom: 12px; font-family: Verdana; }\n";
        $r .= "pre { background-color: #e5e5cc; padding: 5px; font-family: Courier New; font-size: x-small; margin-top: -5px; border: 1px #f0f0e0 solid; }\n";
        $r .= "h2 { font-size: 1.5em; font-weight: bold; margin-top: 25px; margin-bottom: 10px; border-top: 1px solid #003366; margin-left: -15px; color: #003366; }\n";
        $r .= "h3 { font-size: 1.1em; color: #000000; margin-left: -15px; margin-top: 10px; margin-bottom: 10px; }\n";
        $r .= "ul { margin-top: 10px; margin-left: 20px; }\n";
        $r .= "li { margin-top: 10px; color: #000000; }\n";

        $r .= 'font.error { color: darkred; font: bold; }</style>';
        $r .= '<script type="text/javascript">
			function showDiv(vThis)
			{            
			vSibling = document.getElementById(vThis+"_ul");
			vSibling2 = document.getElementById(vThis+"_hid");
			if(vSibling.style.display === "none")
			{
			vSibling.style.display = "block";
			vSibling2.style.display = "none";
			} else {
			vSibling.style.display = "none";
			vSibling2.style.display = "inline";
			}
			return false;
			}
			</script>';
        $r .= '</header>';
        $r .= "<body>\n";
        $r .= '<div id="content"><p class="heading1">' . $this->nameWebService . '</p>';
        $r .= '<h2>' . $this->description . '</h2><br>';
        return $r;
    }

    protected function html_footer($timer_init = 0) {
        $r = '';
        $t2 = ceil((microtime(true) - $timer_init) * 1000) / 1000;
        //echo base64_encode("<hr>Webserver powered by <a href='http://www.southprojects.com/")."<br>";
        //echo base64_encode("'>CLOUDKING</a>")."<br>";

        $b1
            = base64_decode('PGhyPldlYnNlcnZlciBwb3dlcmVkIGJ5IDxhIGhyZWY9J2h0dHA6Ly93d3cuc291dGhwcm9qZWN0cy5jb20vY2xvdWRraW5nLw==');
        $b1 .= 'version.php?version=' . $this->version;
        $b1 .= base64_decode('Jz5DTE9VREtJTkc8L2E+');
        //echo $b1;
        if (!$this->oem) {
            $r .= "$b1&nbsp;";
            if ($this->verbose >= 1) {
                $r .= 'Version ' . $this->version . '.&nbsp;';
            }
        }
        if ($timer_init != 0) {
            $r .= "Generated in $t2 seconds<br>";
        }
        $r .= $this->copyright . '<br>';
        $r .= '</div></body>';
        return $r;
    }

    protected function gen_description() {
        $t1 = microtime(true);
        $result = $this->html_header();

        if ($this->verbose >= 2) {
            $result .= "Name Web Service :{$this->nameWebService}<br>";
            $result .= "Namespace :<a href='{$this->nameSpace}'>{$this->nameSpace}</a><br>";
            $result .= "WSDL :<a href='{$this->portUrl}?wsdl'>WSDL description</a><br>";
            $result .= 'Protocols Supported :' . ($this->soap11 ? 'SOAP 1.1, ' : '') .
                ($this->soap12 ? 'SOAP 1.2 (2.0), ' : '') . (($this->get) ? 'HTTP GET, ' : '') .

                ($this->post ? 'HTTP POST, ' : '') . 'None <br>';
            if (method_exists($this, 'source')) {
                $result .= "Source :<a href='{$this->portUrl}?source'>Source Generation</a><br>";
            }
        }
        if ($this->verbose >= 1) {
            $k = '{';
            $result .= "<br><h3 onclick='showAllDivOp();' style='cursor:pointer;'>List of Operations</h3><ul>";
            $jsall = "<script>function showAllDivOp(){$k}";

            foreach ($this->operation as $complexName => $args) {
                $tmpname = $complexName;
                $js = "showDiv(\"$tmpname\");";
                $jsall .= $js;
                $result .= "<li><a onclick='showDiv(\"$tmpname\");' name='$tmpname'  style='cursor:pointer;'>" .
                    "<strong>$tmpname</strong></a>(<ul id='{$tmpname}_ul' style='display:none;'>";
                foreach ($args['in'] as $key2 => $value2) {
                    $tmp = $this->gen_description_util(@$value2['minOccurs'], @$value2['maxOccurs']);
                    $tmp .= (@$value2['byref']) ? ' ByRef ' : '';
                    if (@$value2['description']) {
                        $tmp .= '// ' . $value2['description'];
                    }

                    $result .= '<li><strong>' . $key2 . '</strong> as ' .
                        $this->var_is_defined($value2['type']) . " $tmp </li>";
                }
                $result .= "</ul><span id='" . $tmpname .
                    "_hid' style='display:inline;'>parameters (click name to show)..</span><br>)";
                if (isset($args['out']) && count($args['out']) > 1) {

                    $value2 = $args['out'];
                    $tmp = $this->gen_description_util(@$value2['minOccurs'], @$value2['maxOccurs']);
                    if (@$value2['description']) {
                        $tmp .= '// out: ' . $value2['description'];
                    }
                    $result .= ' as ' . $this->var_is_defined($value2['type']) . " $tmp ";

                }
                $result .= '&nbsp;&nbsp; //<i>' . $args['description'] . '</i><br>';
                $result .= '</li>';
            }
            $result .= '</ul>';
            $jsall .= '}</script>';
            $result .= $jsall;
            $result .= "<br><h3 onclick='showAllDivComplex();' style='cursor:pointer;'>List of Complex Types</h3><ul>";
            $k = '{';
            $jsall = '<script>function showAllDivComplex()' . $k;
            foreach ($this->complextype as $complexName => $args) {
                $tmpname = $complexName;
                $js = "showDiv(\"$tmpname\");";
                $jsall .= $js;
                $result .= "<li ><a onclick='$js' name='$tmpname'  style='cursor:pointer;'>" .
                    "<strong>tns::$tmpname</strong></a>{<ul id='{$tmpname}_ul' style='display:none;'>";
                foreach ($args as $key2 => $value2) {
                    $tmp = $this->gen_description_util(@$value2['minOccurs'], @$value2['maxOccurs']);
                    if (@$value2['description']) {
                        $tmp .= " // {$value2['description']}";
                    }
                    $result .= "<li><strong>{$key2}</strong> as {$this->var_is_defined($value2['type'])} $tmp </li>";
                }
                $result .= "</ul><span id='{$tmpname}_hid' style='display:inline;'>" .
                    'parameters (click name to show)..</span>}';
                $result .= '</li>';
            }
            $result .= '</ul><br>';
            $jsall .= '}</script>';
            $result .= $jsall;
        }
        // copyright
        $result .= $this->html_footer($t1);
        return $result;
    }

    private function gen_description_util($min, $max) {
        $tmp = '';
        $tmp1 = '';
        if ($min != '0' && $min != '') {
            $tmp1 = 'required';
        }
        if (@$max > 1) {
            $tmp = "Array({$min} to {$max})";
        }
        if (@$max === 'unbounded') {
            $tmp = "Array({$min} to unlimited)";
        }
        return ($tmp . ' ' . $tmp1);
    }

    protected function var_is_defined($fullname) {
        $x1 = explode(':', $fullname, 2);
        if (count($x1) != 2) {
            return "<span style='color:red'>$fullname</span>";
        }
        list($space, $name) = $x1;
        if ($space === 's') {
            if (!in_array($name, $this->predef_types)) {
                return "<span style='color:red'>$fullname</span>";
            }
            return "<a href='http://www.w3.org/TR/xmlschema-2/#$name'>$fullname</a>";
        }
        if ($space === 'tns') {
            foreach ($this->complextype as $complexName => $args) {
                if ($name === $complexName) {
                    return "<a href='#$name'>$fullname</a>";
                }
            }
            return "<span style='color:red'>$fullname</span>";
        }
        return "<span style='color:red'>??$fullname</span>";
    }

    /**
     * @param string     $namefunction
     * @param array      $arrIn  The input parameters
     * @param array|null $arrOut The output parameter (if any)
     * @param string     $description
     *
     * @return bool
     */
    public function addfunction($namefunction, $arrIn, $arrOut, $description = '') {

        $description = (!$description) ? "The function $namefunction" : $description;
        $resultIn = [];
        foreach ($arrIn as $k => $value) {
            if (!$arrIn[$k]['name']) {
                throw new RuntimeException('name must be defined', E_USER_ERROR);
            }
            $key = $arrIn[$k]['name'];

            $resultIn[$key]['type'] = (!@$arrIn[$k]['type']) ? 's:string' : $arrIn[$k]['type'];
            $resultIn[$key]['minOccurs'] = (@$arrIn[$k]['minOccurs'] == '') ? 0 : $arrIn[$k]['minOccurs'];
            if (@$resultIn[$key]['maxOccurs'] > 1 || @$arrIn[$k]['maxOccurs'] === 'unbounded') {
                trigger_error('maxOccurs cannot be >1', E_USER_ERROR);
            }
            $resultIn[$key]['maxOccurs'] = (@$arrIn[$k]['maxOccurs'] == '') ? 1 : $arrIn[$k]['maxOccurs'];
            $resultIn[$key]['extra'] = (@$arrIn[$k]['extra'] == '') ? '' : $arrIn[$k]['extra'];
            $resultIn[$key]['byref'] = (@$arrIn[$k]['byref'] == '') ? false : $arrIn[$k]['byref'];
            $resultIn[$key]['description'] = (@$arrIn[$k]['description'] == '') ? '' : $arrIn[$k]['description'];
        }
        // it must be only one value (or zero).

        //$key=$arrIn[$k]['name'];
        if (!isset($arrOut[0])) {
            $resultOut = [];
        } else {
            if (count($arrOut) > 1) {
                throw new RuntimeException('output cannot exceed 1 value', E_USER_ERROR);
            }
            $resultOut['name'] = $arrOut[0]['name'];
            $resultOut['type'] = (!@$arrOut[0]['type']) ? 's:string' : $arrOut[0]['type'];
            $resultOut['minOccurs'] = (!@$arrOut[0]['minOccurs']) ? 0 : $arrOut[0]['minOccurs'];
            $resultOut['maxOccurs'] = (!@$arrOut[0]['maxOccurs']) ? 1 : $arrOut[0]['maxOccurs'];
            $resultOut['extra'] = (!@$arrOut[0]['extra']) ? '' : $arrOut[0]['extra'];
            $resultOut['byref'] = (!@$arrOut[0]['byref']) ? false : $arrOut[0]['byref'];
            $resultOut['description'] = (!@$arrOut[0]['description']) ? '' : $arrOut[0]['description'];

        }

        $this->operation[$namefunction] = array(
            'in' => $resultIn,
            'out' => $resultOut,
            'description' => $description
        );

        return true;
    }

    public function addtype($nametype, $arr_param) {
        $result = [];
        foreach ($arr_param as $k => $value) {
            $key = $arr_param[$k]['name'];
            //$arr_param[$key]['name'] = (@$arr_param[$key]['name'] == '') ? 'undefined' : $arr_param[$key]['name'];
            $result[$key]['type'] = (@$arr_param[$k]['type'] == '') ? 's:string' : $arr_param[$k]['type'];
            $result[$key]['minOccurs'] = (@$arr_param[$k]['minOccurs'] == '') ? 0 : $arr_param[$k]['minOccurs'];
            $result[$key]['maxOccurs'] = (@$arr_param[$k]['maxOccurs'] == '') ? 1 : $arr_param[$k]['maxOccurs'];
            $result[$key]['extra'] = (@$arr_param[$k]['extra'] == '') ? '' : $arr_param[$k]['extra'];
            $result[$key]['byref'] = (@$arr_param[$k]['byref'] == '') ? false : $arr_param[$k]['byref'];
            $result[$key]['description'] = isset($arr_param) ? $arr_param[$k]['description'] : '';
        }

        $this->complextype[$nametype] = $result;
    }

    private function _save_wsdl($filename) {
        if (!$fp = fopen($filename, 'ab')) {
            return "file '$filename' can't be saved";
        }
        if (fwrite($fp, $this->genwsdl()) === false) {
            return "information can't be saved";
        }
        @fclose($fp);
        return 'ok';
    }

    public function genwsdl() {
        if ($this->custom_wsdl !== $this->portUrl . '?wsdl') {
            // se usa un archivo customizado.
            $handle = @fopen($this->custom_wsdl, 'rb');
            if ($handle) {
                $contents = fread($handle, filesize($this->custom_wsdl));
            } else {
                $contents = "file or url :{$this->custom_wsdl} can't be open <br>\n";
            }
            fclose($handle);
            return $contents;
        }
        $cr = "\n";
        $tab1 = "\t";
        $wsdl = "<?xml version=\"1.0\" encoding=\"{$this->encoding}\" ?>{$cr}";
        $wsdl .= "<wsdl:definitions targetNamespace=\"{$this->nameSpace}\" " .
            'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" ' .
            'xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" ' .
            "xmlns:mime=\"http://schemas.xmlsoap.org/wsdl/mime/\" xmlns:tns=\"{$this->nameSpace}\" ";
        $wsdl .= ' xmlns:s="http://www.w3.org/2001/XMLSchema" ';
        if ($this->soap12) {
            $wsdl .= ' xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" ';
        }
        $wsdl .= ' xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" ' .
            'xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">'
            . $cr;
        if ($this->description) {
            $wsdl .= "<wsdl:documentation >{$this->description}</wsdl:documentation>";
        }
        // types ***
        $wsdl .= "{$tab1}<wsdl:types>{$cr}<s:schema elementFormDefault=\"qualified\" " .
            "targetNamespace=\"{$this->nameSpace}\">{$cr}";
        // elementos in
        foreach ($this->operation as $complexName => $args) {
            $wsdl .= "<s:element name=\"{$complexName}\">
                    <s:complexType>
                       <s:sequence>
                       ";
            foreach ($args['in'] as $key2 => $value2) {
                //var_dump($args['in']);
                //die(1);
                $minOccurs = $value2['minOccurs'];
                $maxOccurs = $value2['maxOccurs'];
                $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                    $key2 . '" type="' . $value2['type'] . '" ' . @$value2['extra'] . '>';
                if (@$value2['description']) {
                    $wsdl .= "<s:annotation><s:documentation >{$value2['description']}</s:documentation></s:annotation>";
                }
                $wsdl .= '</s:element>';
            }
            $wsdl .= '</s:sequence>
                    </s:complexType>
                 </s:element>';
            // out
            $out = false;
            if (isset($args['out']) && count($args['out']) > 1) {
                $out = true;
            }
            foreach ($args['in'] as $key2 => $value2) {
                if (@$value2['byref']) {
                    $out = true;
                    break;
                }
            }
            $wsdl .= "<s:element name=\"{$complexName}Response\">";
            if ($out) {
                $wsdl .= '<s:complexType><s:sequence>';
                if (isset($args['out']) && count($args['out']) > 1) {
                    $value2 = $args['out'];
                    $minOccurs = $value2['minOccurs'];
                    $maxOccurs = $value2['maxOccurs'];
                    $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                        $complexName . 'Result" type="' . $value2['type'] . '" ' . @$value2['extra'] . '>';
                    if (@$value2['description']) {
                        $wsdl .= '<s:annotation><s:documentation >' . $value2['description']
                            . '</s:documentation></s:annotation>';
                    }
                    $wsdl .= '</s:element>';
                }
                foreach ($args['in'] as $key2 => $value2) {
                    if (@$value2['byref']) {
                        $minOccurs = $value2['minOccurs'];
                        $maxOccurs = $value2['maxOccurs'];
                        $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                            $key2 . '" type="' . $value2['type'] . '" ' . @$value2['extra'] . '>';
                        if (@$value2['description']) {
                            $wsdl .= "<s:annotation><s:documentation >{$value2['description']}</s:documentation></s:annotation>";
                        }
                        $wsdl .= '</s:element>';
                    }
                }

                $wsdl .= '</s:sequence></s:complexType>';
            } else {
                $wsdl .= '<s:complexType/>';
            }
            $wsdl .= '</s:element>
                 ';
        }
        // complex types
        foreach ($this->complextype as $complexName => $args) {
            $wsdl .= '         <s:complexType name="' . $complexName . '">
                    <s:sequence>
                    ';
            foreach ($args as $key2 => $value2) {
                $minOccurs = $value2['minOccurs'];
                $maxOccurs = $value2['maxOccurs'];
                $type = str_replace($this->nameSpace, 'tns', $value2['type']);
                $wsdl .= ' <s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                    $key2 . '" type="' . $type . '" ' . @$value2['extra'] . '>';
                if (@$value2['description']) {
                    $wsdl .= "<s:annotation><s:documentation >{$value2['description']}</s:documentation></s:annotation>";
                }
                $wsdl .= '</s:element>';
            }
            $wsdl .= '            </s:sequence>
                 </s:complexType>
                 ';
        }
        // end types
        $wsdl .= ' </s:schema>
             </wsdl:types>
           ';
        // messages
        foreach ($this->operation as $complexName => $args) {
            $name = $complexName;
            $wsdl .= '   <wsdl:message name="' . $name . 'SoapIn">
              <wsdl:part name="parameters" element="tns:' . $name . '"/>
           </wsdl:message>
           ';
            $wsdl .= '   <wsdl:message name="' . $name . 'SoapOut">
              <wsdl:part name="parameters" element="tns:' . $name . 'Response"/>
           </wsdl:message>
           ';
            if ($this->get) {
                $wsdl .= '   <wsdl:message name="' . $name . 'HttpGetIn">
				  <wsdl:part name="parameters" element="tns:' . $name . '"/>
			   </wsdl:message>
			   ';
                $wsdl .= '   <wsdl:message name="' . $name . 'HttpGetOut">
				  <wsdl:part name="HttpBody" element="tns:' . $name . 'Response"/>
			   </wsdl:message>
			   ';
            }

            if ($this->post) {
                $wsdl .= '   <wsdl:message name="' . $name . 'HttpPostIn">
				  <wsdl:part name="parameters" element="tns:' . $name . '"/>
			   </wsdl:message>
			   ';
                $wsdl .= '   <wsdl:message name="' . $name . 'HttpPostOut">
				  <wsdl:part name="HttpBody" element="tns:' . $name . 'Response"/>
			   </wsdl:message>
			   ';
            }
        }
        // porttype
        $wsdl .= '<wsdl:portType name="' . $this->nameWebService . 'Soap">';
        foreach ($this->operation as $complexName => $args) {
            $name = $complexName;
            $wsdl .= '<wsdl:operation name="' . $name . '">';
            if (@$args['description']) {
                $wsdl .= '<wsdl:documentation>' . @$args['description'] . '</wsdl:documentation>';
            }
            $wsdl .= '<wsdl:input message="tns:' . $name . 'SoapIn"/><wsdl:output message="tns:' . $name .
                'SoapOut"/></wsdl:operation>';
        }
        $wsdl .= '</wsdl:portType>';
        if ($this->get) {
            $wsdl .= '<wsdl:portType name="' . $this->nameWebService . 'HttpGet">';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                if (@$args['description']) {
                    $wsdl .= '<wsdl:documentation>' . @$args['description'] . '</wsdl:documentation>';
                }
                $wsdl .= '<wsdl:input message="tns:' . $name . 'HttpGetIn"/><wsdl:output message="tns:' . $name .
                    'HttpGetOut"/></wsdl:operation>';
            }
            $wsdl .= '</wsdl:portType>';
        }
        if ($this->post) {
            $wsdl .= '<wsdl:portType name="' . $this->nameWebService . 'HttpPost">';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                if (@$args['description']) {
                    $wsdl .= '<wsdl:documentation >' . @$args['description'] . '</wsdl:documentation>';
                }
                $wsdl .= '<wsdl:input message="tns:' . $name . 'HttpPostIn"/><wsdl:output message="tns:' . $name .
                    'HttpPostOut"/></wsdl:operation>';
            }
            $wsdl .= '</wsdl:portType>';
        }

        // binding
        if ($this->soap11) {
            $wsdl .= '<wsdl:binding name="' . $this->nameWebService . 'Soap" type="tns:' . $this->nameWebService . 'Soap">
			<soap:binding transport="http://schemas.xmlsoap.org/soap/http"/>     
		   ';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= '<wsdl:operation name="' . $name . '">
				 <soap:operation soapAction="' . $this->nameSpace . $name . '" style="document"/>
				 <wsdl:input><soap:body use="literal"/></wsdl:input>
				 <wsdl:output><soap:body use="literal"/></wsdl:output>
			  </wsdl:operation>
			  ';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // binding12
        if ($this->soap12) {
            $wsdl .= '<wsdl:binding name="' . $this->nameWebService . 'Soap12" type="tns:' . $this->nameWebService . 'Soap">
				<soap12:binding transport="http://schemas.xmlsoap.org/soap/http"/>';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= '<wsdl:operation name="' . $name . '">
				 <soap12:operation soapAction="' . $this->nameSpace . $name . '" style="document"/>
				 <wsdl:input><soap12:body use="literal"/></wsdl:input>
				 <wsdl:output><soap12:body use="literal"/></wsdl:output>
			  </wsdl:operation>
			  ';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // binding12 (get)
        if ($this->soap12 && $this->get) {
            $wsdl .= '<wsdl:binding name="' . $this->nameWebService . 'HttpGet" type="tns:' . $this->nameWebService . 'HttpGet">
				<http:binding verb="GET" />';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= "<wsdl:operation name=\"{$name}\">";
                $wsdl .= "<http:operation location=\"/{$name}\" />";
                $wsdl .= '<wsdl:input><http:urlEncoded /></wsdl:input><wsdl:output>';
                $wsdl .= '<mime:mimeXml part="HttpBody" /></wsdl:output></wsdl:operation>';
            }
            $wsdl .= '</wsdl:binding>';
        }

        // binding12 (post)
        if ($this->soap12 && $this->post) {
            $wsdl .= '<wsdl:binding name="' . $this->nameWebService . 'HttpPost" type="tns:' . $this->nameWebService . 'HttpPost">
				<http:binding verb="POST" />';
            foreach ($this->operation as $complexName => $args) {
                $name = $complexName;
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                $wsdl .= '<http:operation location="/' . $name . '" />';
                $wsdl .= '<wsdl:input><mime:content type="application/x-www-form-urlencoded" /></wsdl:input>';
                $wsdl .= '<wsdl:output><mime:mimeXml part="HttpBody" /></wsdl:output></wsdl:operation>';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // service

        $wsdl .= '<wsdl:service name="' . $this->nameWebService . '">';

        if ($this->soap11) {
            $wsdl .= "<wsdl:port name=\"{$this->nameWebService}Soap\" binding=\"tns:{$this->nameWebService}Soap\">
             <soap:address location=\"{$this->portUrl}\"/></wsdl:port>";
        }
        if ($this->soap12) {
            $wsdl .= "<wsdl:port name=\"{$this->nameWebService}Soap12\" binding=\"tns:{$this->nameWebService}Soap12\">
             <soap12:address location=\"{$this->portUrl}\"/></wsdl:port>";
            if ($this->get) {
                $wsdl .= "<wsdl:port name=\"{$this->nameWebService}HttpGet\" binding=\"tns:{$this->nameWebService}HttpGet\">
				 <http:address location=\"{$this->portUrl}\"/></wsdl:port>";
            }
            if ($this->post) {
                $wsdl .= "<wsdl:port name=\"{$this->nameWebService}HttpPost\" binding=\"tns:{$this->nameWebService}HttpPost\">
				 <http:address location=\"{$this->portUrl}\"/></wsdl:port>";
            }

        }
        $wsdl .= '</wsdl:service></wsdl:definitions>';
        return $wsdl;
    }

}