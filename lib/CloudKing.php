<?php /** @noinspection PhpUnusedPrivateMethodInspection */
/** @noinspection JSUnresolvedFunction */
/** @noinspection JSUndeclaredVariable */
/** @noinspection DuplicatedCode */

/** @noinspection PhpUnused */

namespace eftec\cloudking;

/**
 * Class CloudKing
 * @copyright Jorge Castro Castillo
 * @see https://github.com/eftec/cloudking
 * @package eftec\cloudking
 */
class CloudKing
{
    protected $version = "2.5";
    public $allowed_format;
    var $soap11 = true;
    var $soap12 = false;
    var $get = true;
    var $post = true;
    var $allowed_input = array("json" => true, "rest" => true, "php" => true, "xml" => true, "none" => true);
    /**
     * If the service is not set, then it will be unable to answer any call.
     * @var null|object
     */
    var $serviceInstance = null;    
    var $oem = false;
    var $encoding = "UTF-8";
    var $custom_wsdl = "";
    var $copyright = "";
    var $description = "CLOUDKING Server is running in this machine";
    var $verbose = 0;
    var $wsse_username = "";
    var $wsse_password = "";
    var $wsse_nonce = "";
    var $wsse_created = ""; // ISO-8859-1
    var $wsse_password_type = "None";
    var $variable_type = "array"; //Copyright (c)2009 - 2020, SouthProject <a href='http://www.southprojects.com'>www.southprojects.com</a>";
    var $allowed = array("*");
    var $disallowed = array("");
    protected $FILE;
    protected $NAMESPACE;
    protected $NAME_WS;
    protected $operation; //None, PasswordDigest, PasswordText 
    protected $complextype; // array or object. it define if the implementation will use array (or primitives) or objects
    
    protected $predef_types = [
        "string",
        "long",
        "int",
        "integer",
        "boolean",
        "decimal",
        "float",
        "double",
        "duration",
        "dateTime",
        "time",
        "date",
        "gYearMonth",
        "gYear",
        "gMonthDay",
        "gDay",
        "gMonth",
        "hexBinary",
        "base64Binary",
        "anyURI",
        "QName",
        "NOTATION"
    ];
    protected $predef_typesNS = [
        "s:string",
        "s:long",
        "s:int",
        "s:integer",
        "s:boolean",
        "s:decimal",
        "s:float",
        "s:double",
        "s:duration",
        "s:dateTime",
        "s:time",
        "s:date",
        "s:gYearMonth",
        "s:gYear",
        "s:gMonthDay",
        "s:gDay",
        "s:gMonth",
        "s:hexBinary",
        "s:base64Binary",
        "s:anyURI",
        "s:QName",
        "s:NOTATION"
    ];
    protected $predef_types_num = array(
        "long",
        "int",
        "integer",
        "boolean",
        "decimal",
        "float",
        "double",
        "duration"
    );

    public function __construct($FILE, $NAMESPACE = "http://test-uri/", $NAME_WS = "CKService1") {
        $this->_CKLIB($FILE, $NAMESPACE, $NAME_WS);
    }

    private function _CKLIB($FILE, $NAMESPACE, $NAME_WS) {

        if (@$_SERVER["HTTPS"]) {
            $FILE = str_replace("http://", "https://", $FILE);
        }
        $this->FILE = $FILE;


        $this->NAMESPACE = ($NAMESPACE != "") ? $NAMESPACE : $FILE . "/";
        $this->NAME_WS = ($NAME_WS != "") ? $NAME_WS : "CKService1";
        $this->operation = array();
        $this->complextype = array();
        $this->custom_wsdl = $this->FILE . "?wsdl";
    }

    public static function argList($name = 'return', $type = 'tns:*', $minOccurs = 0
        , $maxOccurs = 'unbounded', $byref = false) {
        return ["name" => $name, "type" => $type, "minOccurs" => $minOccurs
            , "maxOccurs" => $maxOccurs, "byref" => $byref];
    }

    /**
     * @param string $name
     * @param string $type=$this->predef_typesNS[$i]
     * @param bool $byref
     * @return array
     */
    public static function argPrim($name = 'return', $type = 's:*', $byref = false) {
        return ["name" => $name, "type" => $type, "byref" => $byref];
    }

    public static function argComplex($name = 'return', $type = 'tns:*', $byref = false) {
        return ["name" => $name, "type" => $type, "byref" => $byref];
    }

    public function set_copyright($copyright) {
        $this->copyright = $copyright;
    }

    public function save_wsdl($filename) {
        return $this->save_wsdl($filename);
    }

    public function password_correct($password, $type = "None") {
        if ($type != $this->wsse_password_type) {
            return false; // method not equal
        }
        if ($this->wsse_password_type == "PasswordDigest") {
            $wsse_nonce = $this->wsse_nonce;
            $wsse_created = $this->wsse_created;
            $nonce = base64_decode($wsse_nonce);
            $password_digest = base64_encode(sha1($nonce . $wsse_created . $password, true));
            return ($this->wsse_password == $password_digest);
        }
        if ($this->wsse_password_type == "PasswordText") {
            return ($this->wsse_password == $password);
        }
        return true;
    }

    public function run() {
        return $this->_run();
    }

    private function _run() {
        $result = '';
        if (!$this->security()) {
            return false;
        }
        global $_REQUEST;
        $param = @$_SERVER['QUERY_STRING'] . "&=";
        $p = strpos($param, "&");
        $p1 = strpos($param, "=");
        $paraminit = substr($param, 0, min($p, $p1)); // ?{value}&other=aaa


        //$HTTP_RAW_POST_DATA=@$GLOBALS['HTTP_RAW_POST_DATA'];
        $HTTP_RAW_POST_DATA = file_get_contents('php://input');


        $isget = false;
        $methodcalled = ($paraminit == "") ? "soap" : $paraminit;
        $methoddefined = false;
        if (strlen($methodcalled) >= 3) {
            if (substr($methodcalled, 0, 3) == "get" and $this->get) {
                $methodcalled = str_replace("get", "", $methodcalled);
                $methodcalled = ($methodcalled == "" ? "none" : $methodcalled);
                $isget = true;
                $methoddefined = true;
            }
        }
        if (strlen($methodcalled) >= 4) {
            if (substr($methodcalled, 0, 4) == "post" and $this->post) {
                $methodcalled = str_replace("post", "", $methodcalled);
                $methodcalled = ($methodcalled == "" ? "none" : $methodcalled);
                $isget = false;
                $methoddefined = true;
            }
        }
        $info = explode("/", @$_SERVER["PATH_INFO"]);
        $function_name = (count($info) >= 2) ? $info[1] : "unknown_unknown";
        $function_out = (count($info) >= 3) ? $info[2] : $methodcalled;

        if (count($info) >= 4 and $this->get) {
            // is passing more that the functionname and output type 0> is rest myphp?php/functionname/typeout/p1/p2....
            $isget = true;
            $methodcalled = "rest";
            $methoddefined = true;
        }
        if ($this->soap12) {
            if (!$methoddefined and $HTTP_RAW_POST_DATA == "" and $function_name != "unknown_unknown" and $this->get) {
                // mypage.php/functioname?param1=......
                $methodcalled = "none";
                $function_out = "xml";
                $isget = true;
                $methoddefined = true;
            }
            if (!$methoddefined and $function_name != "unknown_unknown" and $this->post) {
                // mypage.php/functioname (it must be soap http post).

                $methodcalled = "none";
                $function_out = "xml";
                $HTTP_RAW_POST_DATA = " "; // only for evaluation.
                $isget = false;
                $methoddefined = true;
            }
        }
        if (!@$this->allowed_input[$methodcalled] and $methoddefined) {

            trigger_error("method <b>$methodcalled</b> not allowed. Did you use SOAP 1.1 or 1.2?", E_USER_ERROR);
        }


        if ($HTTP_RAW_POST_DATA != "" or $isget) {
            // is trying to evaluate a function.

            // ejemplo :http://www.micodigo.com/webservice.php/functionname/xml?getjson&value1={json..}&value2={json}
            //info(0)=0
            //info(1)=functionname
            //info(2)=serialize return type (optional, by default is the same name as passed)


            $res = false;
            switch ($methodcalled) {
                case "soap":
                case "wsdl":
                    $res = $this->requestSOAP($HTTP_RAW_POST_DATA);
                    break;
                case "json":
                case "rest":
                case "php":
                case "xml":
                case "none":

                    $res = $this->requestNOSOAP($function_name, $function_out, $methodcalled, $isget, $info);
                    break;
            }
            if ($res) {
                $result .= $res;
                return $result;
            } else {
                return false;
            }
        } else {
            switch ($paraminit) {
                case "wsdl":
                    header("content-type:text/xml;charset=" . $this->encoding);
                    $result .= $this->genwsdl();
                    return $result;
                    break;
                case "source":
                    if ($this->verbose >= 2) {
                        switch (@$_GET["source"]) {
                            case "php":

                                if (method_exists($this, 'genphp')) {
                                    header("content-type:text/plain;charset=" . $this->encoding);
                                    $result .= $this->genphp();
                                } else {
                                    $result .= "not supported<br>";
                                }
                                break;
                            case "phpclient":

                                if (method_exists($this, 'genphpclient')) {
                                    header("content-type:text/plain;charset=" . $this->encoding);
                                    /** @see \CKLIB_SRC::genphpclient */
                                    $result .= $this->genphpclient();
                                } else {
                                    $result .= "Not supported<br>";
                                }
                                break;
                            case "unity":
                                if (method_exists($this, 'genunitycsharp')) {
                                    header("content-type:text/plain;charset=" . $this->encoding);
                                    $result .= $this->genunitycsharp();
                                } else {
                                    $result .= "not supported<br>";
                                }
                                break;
                            default:
                                if (method_exists($this, 'source')) {
                                    header("content-type:text/html");
                                    $result .= $this->source();
                                } else {
                                    $result .= "not supported<br>";
                                }
                                break;
                        }
                        return $result;
                    }
                    break;
                case "unitycsharp":
                    if ($this->verbose >= 2) {
                        header("content-type:text/plain;charset=" . $this->encoding);
                        $result .= $this->genunitycsharp();
                        return $result;
                    }
                    break;
            }

        }
        if (method_exists($this, 'gen_description')) {
            $result .= $this->gen_description();
        } else {
            $result .= $this->html_header();
            $result .= "Name Web Service :" . $this->NAME_WS . "<br>";
            $result .= $this->html_footer();
        }
        return $result;
    }

    private function security() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        foreach ($this->disallowed as $value) {
            if ($value == $hostname or $value == $ip) {
                echo("host $ip $hostname not allowed (blacklist)\n");
                return false;
            }
        }
        foreach ($this->allowed as $value) {
            if ($value == "*" or $value == $hostname or $value == $ip) {
                return true;
            }
        }
        echo("host $ip $hostname not allowed \n");
        return false;
    }

    private function requestSOAP($HTTP_RAW_POST_DATA) {
        global $param, $r;
        $soapenv = "";
        if (strpos($HTTP_RAW_POST_DATA, "http://schemas.xmlsoap.org/soap/envelope/")) {
            if ($this->soap11) {
                header("content-type:text/xml;charset=" . $this->encoding);
                $soapenv = "http://schemas.xmlsoap.org/soap/envelope/";
            }
        }
        if (strpos($HTTP_RAW_POST_DATA, "http://www.w3.org/2003/05/soap-envelope")) {
            if ($this->soap12) {
                header("content-type:application/soap+xml;charset=" . $this->encoding);
                $soapenv = "http://www.w3.org/2003/05/soap-envelope";
            }
        }
        if ($soapenv == "") {
            die("soap incorrect or not allowed");
        }
        $arr = $this->xml2array($HTTP_RAW_POST_DATA);
        $this->wsse_username = @$arr["Envelope"]["Header"]["Security"]["UsernameToken"]["Username"];
        $this->wsse_password = @$arr["Envelope"]["Header"]["Security"]["UsernameToken"]["Password"];
        $this->wsse_nonce = @$arr["Envelope"]["Header"]["Security"]["UsernameToken"]["Nonce"];
        $this->wsse_created = @$arr["Envelope"]["Header"]["Security"]["UsernameToken"]["Created"];
        $tmp = @$arr["Envelope"]["Header"]["Security"]["UsernameToken"]["Password_attr"]["Type"];

        if (strpos($tmp, "#PasswordText")) {
            $this->wsse_password_type = "PasswordText";
        } else {
            if (strpos($tmp, "#PasswordDigest")) {
                $this->wsse_password_type = "PasswordDigest";
            } else {
                $this->wsse_password_type = "None";
            }
        }


        $funcion = array_keys($arr["Envelope"]["Body"]);
        $function_name0 = $funcion[0];
        $function_name = $this->fixtag($function_name0); // "tem:getSix" (deberia ser solo una funcion?)
        // pasar los parametros
        $param = array();

        $paramt = " ";
        $i = 0;
        $indice_operation = -1;
        foreach ($this->operation as $key => $value) {
            if ($value["name"] == $function_name) {
                $indice_operation = $key;
            }
        }
        if ($indice_operation >= 0) {
            $my_operation = $this->operation[$indice_operation];
            foreach ($my_operation["in"] as $value) {
                $param[] = @$arr["Envelope"]["Body"][$function_name0][$value["name"]];
                if (empty($param[$i])) {
                    $param[$i] = "";
                }
                $paramt .= '@$param[' . $i . '],';
                $i++;
            }

            if ($this->variable_type == "object") {
                // convert all parameters in classes.
                foreach ($param as $key => $value) {
                    $classname = $my_operation["in"][$key]["type"];

                    if (strpos($classname, "tns:", 0) !== false) {

                        $param[$key] = $this->array2class($value, $this->fixtag($classname));
                    }

                }
            }
            //$param_count = count($param);
            $paramt = substr($paramt, 0, strlen($paramt) - 1);
            $r = "";

            if ($this->serviceInstance === null ) {
                $evalstr = "\$r=$function_name($paramt);";
            } else {
                // @eval("global \$" . $this->serviceInstance . ";");
                $evalstr = "\$r=\$this->serviceInstance->$function_name($paramt);";
            }


            $evalret = eval($evalstr);
            if ($this->variable_type == "object") {
                $classname = $my_operation["out"][0]["type"];
                if (strpos($classname, "tns:", 0) !== false) {
                    //$ttype = $this->fixtag($classname);
                    $r = $this->class2array($r, $this->fixtag($classname));


                }
                //var_dump($r);
                //$r=@$r[$ttype];


            }
        } else {
            $evalret = array("soap:Fault" => 'Caught exception: function not defined');
        }
        if (!isset($my_operation)) {
            $evalret = array("soap:Fault" => 'Caught exception: no operation found');
            return $evalret; // no operation
        }

        if (is_array($r)) {

            $objectName = @$my_operation["out"][0]["name"];
            if (!$objectName) {
                $classname = $my_operation["out"][0]["type"];
                $objectName = $this->separateNS($classname);
            }

            // the \n is for fixarray2xml
            $serial = "\n" . $this->array2xml($r, "array", false, false, $objectName) . "\n";
            //var_dump($serial);
            $l = strlen($serial);
            if ($l > 2) {
                if (substr($serial, $l - 1, 1) == "\n") {
                    $serial = substr($serial, 0, $l - 1);
                }
            }


            $serial = $this->fixarray2xml($serial);
            //var_dump($serial);


            if (@$r["soap:Fault"] != "") {
                $evalret = false;
            }

        } else {
            $serial = $r;
        }
        // agregamos si tiene valor byref.
        $extrabyref = "";
        $indice = 0;
        $value = $this->operation[$indice_operation];

        foreach ($value["in"] as $key2 => $value2) {
            if (@$value2["byref"]) {
                $paramtmp = @$param[$indice];
                if (is_array($paramtmp)) {
                    $tmp2 = $this->array2xml($paramtmp, "array", false, false);
                    $tmp2 = $this->fixarray2xml($tmp2);
                } else {
                    $tmp2 = $paramtmp;
                }
                $extrabyref .= "<" . $value2["name"] . ">" . $tmp2 . "</" . $value2["name"] . ">";
            }
            $indice++;
        }


        if ($evalret !== false) {
            $resultado = '<?xml version="1.0" encoding="' . $this->encoding . '"?>';
            $resultado .= '<soap:Envelope xmlns:soap="' . $soapenv .
                '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>' .
                "\n";
            $resultado .= '<' . $function_name . 'Response xmlns="' . $this->NAMESPACE . '">';
            $resultado .= '<' . $function_name . 'Result>' . $serial . '</' . $function_name . 'Result>';
            $resultado .= $extrabyref;
            $resultado .= '</' . $function_name . 'Response>';
            $resultado .= '</soap:Body>';
            $resultado .= '</soap:Envelope>';
        } else {
            $resultado = '<soap:Envelope xmlns:soap="' . $soapenv .
                '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">' .
                "\n";
            $resultado .= '<soap:Body><soap:Fault><soap:Code><soap:Value>soap:Sender</soap:Value></soap:Code><soap:Reason><soap:Text xml:lang="en">' .
                $function_name . ' failed to evaluate .' . @$r["soap:Fault"] . '</soap:Text></soap:Reason>';
            $resultado .= '<soap:Detail/></soap:Fault></soap:Body></soap:Envelope>';
        }


        return $resultado;
    }
    public function xml2array($xml) {
        $parentKey=[];
        $result=[];
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'utf-8');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        @xml_parse_into_struct($parser, trim($xml), $xmls);
        xml_parser_free($parser);


        foreach($xmls as $x) {
            $type=@$x['type'];
            $level=@$x['level'];
            $value=@$x['value'];
            $tag=@$x['tag'];
            switch ($type) {
                case 'open':
                    $ns=$this->separateNS($tag);
                    /*if(!isset($parentKey[$level])) {
                        $parentKey[$level]=$ns;
                    } else {
                        $parentKey[$level]++;
                    }
                    */
                    $parentKey[$level]=$ns;
                    $this->addElementArray($result,$parentKey,$level,$ns,[]);
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
    private function addElementArray(&$array,$keys,$level,$tag,$value) {
        switch ($level) {
            case 1:
                $array[$tag]=$value;
                break;
            case 2:
                $array[$keys[1]][$tag]=$value;
                break;
            case 3:
                $array[$keys[1]][$keys[2]][$tag]=$value;
                break;
            case 4:
                $array[$keys[1]][$keys[2]][$keys[3]][$tag]=$value;
                break;
            case 5:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$tag]=$value;
                break;
            case 6:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$tag]=$value;
                break;
            case 7:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$tag]=$value;
                break;
            case 8:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$tag]=$value;
                break;
            case 9:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$tag]=$value;
                break;
            case 10:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$keys[9]][$tag]=$value;
                break;
            case 11:
                $array[$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]][$keys[6]]
                [$keys[7]][$keys[8]][$keys[9]][$keys[10]][$tag]=$value;
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
        $idx = $this->findIdxComplexType($newclass);
        if ($idx == -1) {
            trigger_error('Complex Type ' . $newclass . ' not found', E_USER_ERROR);
        }
        foreach ($this->complextype[$idx]["elements"] as $value) {
            if (strpos($value["type"], "tns:", 0) !== false) {
                $result->$value["name"] = $this->array2class($result->$value["name"]
                    , $this->fixtag($value["type"]));
            }
        }

        return $result;
    }

    private function class2array($class, $classname) {
        if (is_object($class)) {
            $resultado = (array)$class;

            $idx = $this->findIdxComplexType($classname);
            foreach ($this->complextype[$idx]["elements"] as $value) {
                if (strpos($value["type"], "tns:", 0) !== false) {
                    $this->class2array($resultado[$value["name"]], $this->fixtag($value["type"]));
                    //$tmp=$this->class2array($resultado[$value["name"]], $this->fixtag($value["type"]));
                    //if ($tmp != "") {
                    //$resultado[$value["name"]]=$tmp;
                    //}
                }
            }
        } else {
            $resultado[$classname] = $class;
        }
        return $resultado;
    }

    private function findIdxComplexType($complexname) {

        foreach ($this->complextype as $key => $value) {
            if ($value["name"] == $complexname) {
                return $key;
            }
        }
        return -1;

    }

    protected function fixtag($tag) {
        $arr = explode(":", $tag);
        return ($arr[count($arr) - 1]);
    }

    /**
     * @param $tnsName
     * @return mixed|string
     */
    private function separateNS($tnsName) {
        if (strpos($tnsName, ':') === false) {
            return $tnsName;
        }
        $r = explode(':', $tnsName, 2);
        return $r[1];
    }

    function array2xml($array, $name = "root", $contenttype = true, $start = true, $keyx = "") {
        // \n is important, you should not remove it.
        if (!is_array($array)) {
            return $array;
        }

        $xmlstr = "";
        if ($start) {
            if ($contenttype) {
                @header("content-type:text/xml;charset=" . $this->encoding);
            }
            $xmlstr .= '<?xml version="1.0" encoding="' . $this->encoding . "\"?>\n";
            $xmlstr .= '<' . $name . ">\n";
        }

        foreach ($array as $key => $child) {
            if (is_array($child)) {
                $xmlstr .= (is_string($key)) ? "<" . $key . ">\n" : "<" . $keyx . ">\n";
                $child = $this->array2xml($child, "", "", false, $key);
                $xmlstr .= $child;
                $xmlstr .= (is_string($key)) ? "</" . $key . ">\n" : "</" . $keyx . ">\n";
            } else {
                $type = $this->array2xmltype($child);
                if ($this->variable_type == "object" and is_object($child)) {
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

    function array2xmltype($value) {
        if (is_float($value)) {
            return "float";
        }
        if (is_int($value)) {
            return "int";
        }
        if (is_object($value)) {
            return get_class($value);
        }
        return "string";
    }

    private function fixarray2xml($string) {
        //if (!is_array($string)) { return $string; }
        // we remove the first and last element of the xml file so.

        $arr = explode("\n", $string);

        $resultado = "";
        for ($i = 1; $i < count($arr) - 1; $i++) {
            $l = trim($arr[$i]);
            $lant = trim($arr[$i - 1]);
            if ($l != $lant) {
                $resultado .= $arr[$i] . "\n";
            }
        }

        return $resultado;
    }

    private function requestNOSOAP($function_name, $function_out, $methodcalled, $isget, $info = array()) {
        global $param, $r;


        $this->wsse_username = @$_POST["Username"];
        $this->wsse_password = @$_POST["Password"];
        $this->wsse_nonce = @$_POST["Nonce"];
        $this->wsse_created = @$_POST["Created"];
        $tmp = @$_POST["Type"];

        if (strpos($tmp, "#PasswordText")) {
            $this->wsse_password_type = "PasswordText";
        } else {
            if (strpos($tmp, "#PasswordDigest")) {
                $this->wsse_password_type = "PasswordDigest";
            } else {
                $this->wsse_password_type = "None";
            }
        }
        // pasar los parametros
        $param = array();

        $paramt = "";

        $indice_operation = -1;
        foreach ($this->operation as $key => $value) {
            if ($value["name"] == $function_name) {
                $indice_operation = $key;
            }
        }
        $operation = array("in" => array());
        if ($indice_operation >= 0) {
            $i = 0;
            $operation = $this->operation[$indice_operation];
            foreach ($operation["in"] as $key => $value) {
                $tmpvalue = ($isget) ? @$_GET[$value["name"]] : @$_POST[$value["name"]];
                if ($methodcalled == "rest") {
                    $param[] = @$info[$i + 3];
                } else {
                    $param[] = $this->decodeNOSOAP($methodcalled, $tmpvalue);
                }
                $paramt .= '@$param[' . $i . '],';
                $i++;
            }
            if ($this->variable_type == "object") {
                // convert all parameters in classes.				
                foreach ($param as $key => $value) {
                    $classname = $operation["in"][$key]["type"];
                    if (strpos($classname, "tns:", 0) !== false) {
                        $param[$key] = $this->array2class($value, $this->fixtag($classname));
                    }
                }
            }
            // $param_count = count($param);
            $paramt = substr($paramt, 0, strlen($paramt) - 1);
            $r = "";
            if ($this->serviceInstance == "") {
                $evalstr = "\$r=$function_name($paramt);";
            } else {
                //@eval("global \$" . $this->serviceInstance . ";");
                $evalstr = "\$r=\$this->serviceInstance->$function_name($paramt);";

            }

            $evalret = eval($evalstr);

            if ($this->variable_type == "object") {


                $r = $this->class2array($r, $this->fixtag($operation["out"][0]["type"]));

            }
        } else {
            $evalret = array("soap:Fault" => 'Caught exception: function not defined');
        }

        $max_result = array();


        $max_result[$function_name . 'Result'] = $r;

        // agregamos si tiene valor byref.
        $extrabyref = "";
        $indice = 0;

        foreach ($operation["in"] as $key2 => $value2) {
            if (@$value2["byref"]) {
                $paramtmp = @$param[$indice];
                $max_result[$value2["name"]] = $paramtmp;
                $extrabyref .= $value2["name"] . "=" . $paramtmp . "\n";

            }
            $indice++;
        }

        if (count($max_result) == 1) {

            $max_result = $r; // if not byref then we returned as a single value
        }

        if ($evalret !== false) {

            $resultado = $max_result;
        } else {
            $resultado = $r;
        }


        $resultado = $this->encodeNOSOAP($function_out, $resultado, $function_name . 'Result');
        return $resultado;
    }

    private function decodeNOSOAP($methodcalled, $tmpvalue) {
        // pass json/xml/php/raw --> return a array or value.
        $tmp = "";
        switch ($methodcalled) {
            case "json":
                $tmp = json_decode($tmpvalue);
                break;
            case "xml":
                $this->xml2array($tmpvalue);
                break;
            case "php":
                $tmp = @unserialize($tmpvalue);
                break;
            case "none":
                /*if (is_array($tmpvalue)) {
                    $tmpvalue=ex(",",$tmpvalue);
                }
                */ $tmp = $tmpvalue; // urlencode is done
                break;
        }
        return $tmp;
    }

    private function encodeNOSOAP($methodcalled, $tmpvalue, $tmpname) {
        $tmp = "";
        switch ($methodcalled) {
            case "json":
                $tmp = json_encode($tmpvalue);
                break;
            case "xml":
                if (!is_array($tmpvalue)) {
                    @header("content-type:text/xml;charset=" . $this->encoding);
                    $tmp = '<' . '?' . 'xml version="1.0" encoding="' . $this->encoding . '"' . '?' . '>' . "\n";
                    $tmp .= "<$tmpname>$tmpvalue</$tmpname>";
                } else {
                    $tmp = $this->array2xml($tmpvalue, "array", true, true);
                }
                $tmp = $this->fixarray2xml($tmp);

                break;
            case "php":
                $tmp = serialize($tmpvalue);
                break;
            case "none":
                if (is_array($tmpvalue)) {
                    $tmp = "";
                    foreach ($tmpvalue as $key => $value) {
                        $tmp .= $key . "=" . $value . "&";
                    }
                    $tmpvalue = $tmp;
                    $tmpvalue = substr($tmpvalue, 0, strlen($tmpvalue) - 1);
                }
                $tmp = $tmpvalue;
                break;
        }
        return $tmp;
    }

    protected function genphp() {
        
        $r=<<<cin
/**
 * @generated The structure of this class is generated by CloudKing
 * Class {$this->NAME_WS}Service
 */
class {$this->NAME_WS}Service {
cin;


        foreach ($this->operation as $key => $value) {
            $param = "";
            foreach ($value["in"] as $key2 => $value2) {
                $param .= ($value2["byref"]) ? "&" : "";
                $param .= "$" . $value2["name"] . ", ";

            }
            if ($param != "") {
                $param = $this->right($param, 2);
            }
            $r .= "\n";
            $r .= "\tfunction " . $value["name"] . "($param) {\n";
            $r .= "\t\ttry {\n";

            $r .= "\t\t\t// todo: missing implementation \n";
            $r .= "\t\t\t/*\n";
            $param = "";
            foreach ($value["in"] as $key2 => $value2) {
                $param .= "\t\t\t\$_" . $value2["name"] . "=" .
                    $this->Param2PHPValue($value2["type"], $value2["maxOccurs"]) . ";\n";
            }

            $r .= $param;
            $r .= "\t\t\t*/\n";
            $r .= "\t\t\t// End Input Values \n";
            foreach ($value["out"] as $key2 => $value2) {
                $r .= "\t\t\t\$_" . $value["name"] . "Result=" .
                    $this->Param2PHPValue($value2["type"], $value2["maxOccurs"]) . ";\n";
            }
            foreach ($value["out"] as $key2 => $value2) {

                $param .= $this->Param2PHPvalue($value2["type"], $value2["maxOccurs"]);

            }
            //$r.="\t\t \$result=\$_".$value["name"]."Result ".$param."; \n";
            $r .= "\t\t\t return \$_" . $value["name"] . "Result; \n";
            $r .= "\t\t} catch (Exception \$_exception) {\n";
            $r .= "\t\t\treturn(array(\"soap:Fault\"=>'Caught exception: '. \$_exception->getMessage()));\n";
            $r .= "\t\t}\n";
            $r .= "\t}\n";
        }
        foreach ($this->complextype as $key => $value) {
            
            
            $r.="\n\t function factory".$value["name"]."("
                .$this->Param2PHPArg("tns:" . $value["name"], @$value["maxOccurs"],',').") {";
            $r .= "\n";
            $r .= $this->Param2PHP("tns:" . $value["name"], @$value["maxOccurs"]) . "\n";
            $r.="\n\t }\n";
        }
        $r .= "} // end class \n ";

        $r .= "\n" . $this->genphpast("Complex Types (Classes)");
        foreach ($this->complextype as $key => $value) {
            $r .= "\nclass " . $value["name"] . " {\n";
            foreach ($value["elements"] as $key2 => $value2) {
                $r .= "\tvar $" . $value2["name"] . "; // " . $value2["type"] . "\n";
            }
            $r .= "}\n";
        }
        
        
        return $r;
    }

    protected function genphpast($text, $lenght = 100) {
        $L = ($lenght - 6 - strlen($text)) / 2;
        $L = ($L < 1) ? 1 : $L;
        $ast = str_repeat("*", $L);
        return "/*" . $ast . " " . $text . " " . $ast . "*/\n";
    }

    protected function right($string, $num_cut = 1) {
        if (strlen($string) - $num_cut >= 0) {
            return substr($string, 0, strlen($string) - $num_cut);
        }
        return $string;
    }

    protected function Param2PHPvalue($type, $max) {
        $x1 = explode(":", $type);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        $space = $x1[0];
        $name = $x1[1];
        $p = "";
        if ($space == "s") {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            if (!in_array($name, $this->predef_types_num)) {

                $p = "''";
            } else {
                $p = "0";
            }
        }
        if ($space == "tns") {
            foreach ($this->complextype as $key => $value) {
                if ($name == $value["name"]) {
                    $p = '$_' . $name;
                }
            }
            if ($p === "") {
                return "// complex type $type not found";
            }
        }
        if ($p !== "") {
            switch ($max) {
                case "unbounded":
                    return "array($p,$p,...)";
                    break;
                case "1":
                    return $p;
                    break;
                default:
                    $tmp = "array(";
                    for ($i = 0; $i < $max; $i++) {
                        $tmp .= $p . ",";
                    }
                    $tmp = $this->right($tmp, 1) . ")";
                    return ($tmp);
                    break;
            }
        }
        return "\\ complex type $type not defined";
    }

    protected function Param2PHP($type, $max, $separator=";\n",$pre="\t\t") {
        $x1 = explode(":", $type);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        $space = $x1[0];
        $name = $x1[1];
        if ($space == "s") {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            $p = $this->Param2PHPvalue($type, $max);
            if ($max == "unbounded") {
                return "array($p,$p,...)";
            }
            if ($max == 1) {
                return $p;
            }
            $tmp = "array(";
            for ($i = 0; $i < $max; $i++) {
                $tmp .= $p . ",";
            }
            $tmp = $this->right($tmp, 1) . ")";
            return ($tmp);
        }
        $resultado = "";
        if ($space == "tns") {
            foreach ($this->complextype as $key => $value) {
                if ($name == $value["name"]) {

                    foreach ($value["elements"] as $key2 => $value2) {
                        $resultado .= $pre."\$_" . $name . "['" . $value2["name"] . "']=$" .
                            $value2["name"] . $separator;
                        //$resultado.="'".$value2["name"]."'=>".$this->Param2PHP($value2["type"],$value2["maxOccurs"]).",";                    
                    }
                    $resultado = $this->right($resultado);
                    return ($resultado);
                }
            }
            return "\\ complex type $type not defined";
        }
        return "";
    }
    protected function Param2PHPArg($type, $max, $separator=";\n") {
        $x1 = explode(":", $type);
        if (count($x1) != 2) {
            return "// type $type not defined ";
        }
        $space = $x1[0];
        $name = $x1[1];
        if ($space == "s") {
            if (!in_array($name, $this->predef_types)) {
                return "// type $type not found";
            }
            $p = $this->Param2PHPvalue($type, $max);
            if ($max == "unbounded") {
                return "array($p,$p,...)";
            }
            if ($max == 1) {
                return $p;
            }
            $tmp = "array(";
            for ($i = 0; $i < $max; $i++) {
                $tmp .= $p . ",";
            }
            $tmp = $this->right($tmp, 1) . ")";
            return ($tmp);
        }
        $resultado = "";
        if ($space == "tns") {
            foreach ($this->complextype as $key => $value) {
                if ($name == $value["name"]) {

                    foreach ($value["elements"] as $key2 => $value2) {
                        $resultado .= "\$" . $value2["name"] . "=" .
                            $this->Param2PHPValue($value2["type"], $value2["maxOccurs"]) . $separator;
                        //$resultado.="'".$value2["name"]."'=>".$this->Param2PHP($value2["type"],$value2["maxOccurs"]).",";                    
                    }
                    $resultado = $this->right($resultado);
                    return ($resultado);
                }
            }
            return "\\ complex type $type not defined";
        }
        return "";
    }

    /**
     * @return string
     */
    protected function genphpclient() {

        $r = "<?\n";
        $r .= "include_once 'vendor\.php';\n";
        $r .= $this->genphpast("Implementation");
        $r .= "class " . $this->NAME_WS . "Client {\n";
        $r .= "\tvar \$url='" . $this->FILE . "';\n";
        $r .= "\tvar \$tempuri='" . $this->NAMESPACE . "';\n";
        foreach ($this->operation as $key => $value) {
            $functionname = $value["name"];
            $param = "";
            foreach ($value["in"] as $key2 => $value2) {
                $param .= ($value2["byref"]) ? "&" : "";
                $param .= "$" . $value2["name"] . ", ";

            }
            if ($param != "") {
                $param = $this->right($param, 2);
            }
            $r .= "\n\t// " . @$value["description"] . " \n";
            foreach ($value["in"] as $key2 => $value2) {
                $varname = $value2["name"];
                $r .= "\t// $varname = " . @$value2["description"] . " \n";
            }
            $r .= "\tfunction $functionname($param) {\n";
            $r .= "\t\t\$_obj=new CloudKingClient();\n";
            $r .= "\t\t\$_obj->tempuri=\$this->tempuri;\n";
            $r .= "\t\t\$_obj->soap='" . ($this->soap11 ? "1.1" : "1.2") . "'\n";
            $r .= "\t\t\$_param='';\n";
            foreach ($value["in"] as $key2 => $value2) {
                $varname = $value2["name"];
                $r .= "\t\t\$_param.=\$_obj->array2xml(\$$varname,'ts:$varname',false,false);\n";
            }
            $r .= "\t\t\$resultado=\$_obj->loadurl(\$this->url,\$_param,'$functionname');\n";
            foreach ($value["in"] as $key2 => $value2) {
                if ($value2["byref"]) {
                    $r .= "\t\t\$" . $value2["name"] . "=@\$resultado['" . $value2["name"] . "'];\n";
                }
            }
            $r .= "\t\treturn @\$resultado['" . $functionname . "Result'];\n";
            $r .= "\t}\n";

        }
        $r .= "} // end " . $this->NAME_WS . "\n";
        $r .= "?>\n";
        return $r;
    }

    protected function genunitycsharp() {
        $r = $this->genphpast("Implementation");

        $r .= 'using UnityEngine;
		
using System.IO;
using System.Xml.Serialization;
using System;
using System.Text;
using System.Collections;
using System.Collections.Generic;

public class ' . $this->NAME_WS . ' : MonoBehaviour
{
    // Use this for initialization
    private string charset = "UTF-8";
    private string url = "' . $this->FILE . '";
    private string tempuri = "' . $this->NAMESPACE . '";
    private string prefixns = "ts";
	public string cookie="";	
    ';

        foreach ($this->operation as $key => $value) {
            $tmpname = $value["name"];
            if (count($value["out"]) >= 1) {
                $outtype = $this->fixtag($value["out"][0]["type"]);
                $outtypereal = $this->type2csharp($outtype);
            } else {
                $outtypereal = "";
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
        $r .= ' 	private void Start()
	{
		return;
	}';
        foreach ($this->operation as $key => $value) {
            $tmpname = $value["name"];
            $param = "";
            foreach ($value["in"] as $key2 => $value2) {
                $param .= $this->fixtag($value2["type"]) . " " . $value2["name"] . ",";
            }
            $param = $this->right($param, 1);
            $r .= ' 
	private void ' . $tmpname . 'Async(' . $param . ')
        {
		string namefunction = "' . $tmpname . '";
		Single soapVersion=1.1f;
                string ss2 = SoapHeader(namefunction,soapVersion);';

            foreach ($value["in"] as $key2 => $value2) {
                $name = $value2["name"];
                $r .= '
		ss2 += "<" + prefixns + ":' . $name . '>" + Obj2XML(' . $name . ',true) + "</" + prefixns + ":' . $name . '>";
		';
            }

            if (count($value["out"]) >= 1) {
                $outtype = $this->fixtag($value["out"][0]["type"]);
                $outtypereal = $this->type2csharp($outtype);
                $outinit = $this->csharp_init($outtype);
            } else {
                $outtype = "";
                $outtypereal = "";
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
            if ($outtype != "") {
                $r .= $tmpname . "Result=" . $outinit . ";\n";
                $r .= '                ' . $tmpname . 'Result=(' . $outtypereal . ')XML2Obj(other,"' . $outtype . '",' .
                    $tmpname . 'Result.GetType());	
		';
            }
            $r .= 'webservice' . $tmpname . '.responseHeaders.TryGetValue("SET-COOKIE",out cookie);
		';
            $r .= $tmpname . 'AsyncDone();
	}
	public void ' . $tmpname . 'AsyncDone() {
		// we do something...';
            if ($outtype != "") {
                $r .= '
		// ' . $outtypereal . ' dnx=' . $tmpname . 'Result;';
            }
            $r .= '
	}
	';
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
            // cut xml and first node element from the xml
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

        $r .= "\n" . $this->genphpast("Complex Types (Classes)");
        foreach ($this->complextype as $key => $value) {
            $type = $this->fixtag($value["type"]);


            if (strlen($value["name"]) <= 7 || substr($value["name"], 0, 7) == "ArrayOf") {
                $r .= "\npublic class " . $value["name"] . " {\n";
                foreach ($value["elements"] as $key2 => $value2) {
                    $r .= "\tprivate " . $type . " _" . $value2["name"] . "; \n";
                }
                $r .= "\n";
                foreach ($value["elements"] as $key2 => $value2) {
                    $r .= "\tpublic " . $type . " " . $value2["name"] . "\n";
                    $r .= "\t{\n";
                    $r .= "\t\tget { return _" . $value2["name"] . "; }\n";
                    $r .= "\t\tset { _" . $value2["name"] . " = value; }\n";
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

        if ($l > 8 and substr($type, 0, 7) == "ArrayOf") {
            $type = "List<" . substr($type, 7, $l - 7) . ">";
            return $this->type2csharp($type);
        } else {
            return $type;
        }
    }

    protected function csharp_init($type) {
        // ArrayOfS
        // 12345678
        switch ($type) {
            case "string":
            case "String":
                return '""';
                break;
            case "int":
            case "long":
            case "Single":
                return '0';
                break;
            default:
                return "new " . ($this->type2csharp($type)) . "()";
        }
    }

    protected function source() {
        $result = $this->html_header();
        $result .= "<br><h3>List of Operations</h3><ul>";
        $result .= "<li><a href='" . $this->FILE . "?source=unity'>Unity (C#) Client Source</a></li>";
        $result .= "<li><a href='" . $this->FILE . "?source=php'>PHP Server Source</a></li>";
        $result .= "<li><a href='" . $this->FILE . "?source=phpclient'>PHP Source Client</a></li>";
        $result .= "</ul>";
        $result .= $this->html_footer();

        return $result;
    }

    protected function html_header() {
        $r = "<header>";
        $r .= "<title>" . $this->NAME_WS . "</title>\n";

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

        $r .= "font.error { color: darkred; font: bold; }</style>";
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
        $r .= "</header>";
        $r .= "<body>\n";
        $r .= '<div id="content"><p class="heading1">' . $this->NAME_WS . '</p>';
        $r .= "<h2>" . $this->description . "</h2><br>";
        return $r;
    }

    protected function html_footer($timer_init = 0) {
        $r = "";
        $t2 = ceil((microtime(true) - $timer_init) * 1000) / 1000;
        //echo base64_encode("<hr>Webserver powered by <a href='http://www.southprojects.com/cloudking/")."<br>";
        //echo base64_encode("'>CLOUDKING</a>")."<br>";


        $b1 =
            base64_decode("PGhyPldlYnNlcnZlciBwb3dlcmVkIGJ5IDxhIGhyZWY9J2h0dHA6Ly93d3cuc291dGhwcm9qZWN0cy5jb20vY2xvdWRraW5nLw==");
        $b1 .= "version.php?version=" . $this->version;
        $b1 .= base64_decode("Jz5DTE9VREtJTkc8L2E+");
        //echo $b1;
        if (!$this->oem) {
            $r .= "$b1&nbsp;";
            if ($this->verbose >= 1) {
                $r .= "Version " . $this->version . ".&nbsp;";
            }
        }
        if ($timer_init != 0) {
            $r .= "Generated in $t2 seconds<br>";
        }
        $r .= $this->copyright . "<br>";
        $r .= "</div></body>";
        return $r;
    }

    protected function gen_description() {
        $t1 = microtime(true);
        $result = $this->html_header();

        if ($this->verbose >= 2) {
            $result .= "Name Web Service :" . $this->NAME_WS . "<br>";
            $result .= "Namespace :<a href='" . $this->NAMESPACE . "'>" . $this->NAMESPACE . "</a<br>";
            $result .= "WSDL :<a href='" . $this->FILE . "?wsdl" . "'>WSDL description</a><br>";
            $result .= "Protocols Supported :" . (($this->soap11) ? "SOAP 1.1, " : "") .
                (($this->soap12) ? "SOAP 1.2 (2.0), " : "") . (($this->get) ? "HTTP GET, " : "") .
                (($this->post) ? "HTTP POST, " : "") . "None <br>";
            if (method_exists($this, 'source')) {
                $result .= "Source :<a href='" . $this->FILE . "?source" . "'>Source Generation</a><br>";
            }
        }
        if ($this->verbose >= 1) {
            $result .= "<br><h3 onclick='showAllDivOp();' style='cursor:pointer;'>List of Operations</h3><ul>";
            $jsall = "<script>function showAllDivOp() {";
            foreach ($this->operation as $key => $value) {
                $tmpname = $value["name"];
                $js = "showDiv(\"$tmpname\");";
                $jsall .= $js;
                $result .= "<li><a onclick='showDiv(\"$tmpname\");' name='$tmpname'  style='cursor:pointer;'><strong>$tmpname</strong></a>(<ul id='" .
                    $tmpname . "_ul' style='display:none;'>";
                foreach ($value["in"] as $key2 => $value2) {
                    $tmp = $this->gen_description_util(@$value2["minOccurs"], @$value2["maxOccurs"]);
                    $tmp .= (@$value2["byref"]) ? " ByRef " : "";
                    if (@$value2["description"]) {
                        $tmp .= '// ' . $value2["description"];
                    }

                    $result .= "<li><strong>" . $value2["name"] . "</strong> as " .
                        $this->var_is_defined($value2["type"]) . " $tmp </li>";
                }
                $result .= "</ul><span id='" . $tmpname .
                    "_hid' style='display:inline;'>parameters (click name to show)..</span><br>)";
                foreach ($value["out"] as $key2 => $value2) {
                    $tmp = $this->gen_description_util(@$value2["minOccurs"], @$value2["maxOccurs"]);
                    if (@$value2["description"]) {
                        $tmp .= '// ' . $value2["description"];
                    }
                    $result .= " as " . $this->var_is_defined($value2["type"]) . " $tmp ";
                }
                $result .= "&nbsp;&nbsp; //<i>" . $value["description"] . "</i><br>";
                $result .= "</li>";
            }
            $result .= "</ul>";
            $jsall .= "}</script>";
            $result .= $jsall;
            $result .= "<br><h3 onclick='showAllDivComplex();' style='cursor:pointer;'>List of Complex Types</h3><ul>";
            $jsall = "<script>function showAllDivComplex() {";
            foreach ($this->complextype as $key => $value) {
                $tmpname = $value["name"];
                $js = "showDiv(\"$tmpname\");";
                $jsall .= $js;
                $result .= "<li ><a onclick='$js' name='$tmpname'  style='cursor:pointer;'><strong>$tmpname</strong></a>{<ul id='" .
                    $tmpname . "_ul' style='display:none;'>";
                foreach ($value["elements"] as $key2 => $value2) {
                    $tmp = $this->gen_description_util(@$value2["minOccurs"], @$value2["maxOccurs"]);
                    if (@$value2["description"]) {
                        $tmp .= ' // ' . $value2["description"];
                    }
                    $result .= "<li><strong>" . $value2["name"] . "</strong> as " .
                        $this->var_is_defined($value2["type"]) . " $tmp </li>";
                }
                $result .= "</ul><span id='" . $tmpname .
                    "_hid' style='display:inline;'>parameters (click name to show)..</span>}";
                $result .= "</li>";
            }
            $result .= "</ul><br>";
            $jsall .= "}</script>";
            $result .= $jsall;
        }
        // copyright
        $result .= $this->html_footer($t1);
        return $result;
    }

    private function gen_description_util($min, $max) {
        $tmp = "";
        $tmp1 = "";
        if ($min != "0" && $min != "") {
            $tmp1 = "required";
        }
        if (@$max > 1) {
            $tmp = "Array(" . $min . " to " . $max . ")";
        }
        if (@$max == "unbounded") {
            $tmp = "Array(" . $min . " to unlimited)";
        }
        return ($tmp . " " . $tmp1);
    }

    protected function var_is_defined($fullname) {
        $x1 = explode(":", $fullname);
        if (count($x1) != 2) {
            return "<span style='color:red'>$fullname</span>";
        }
        $space = $x1[0];
        $name = $x1[1];
        if ($space == "s") {
            if (!in_array($name, $this->predef_types)) {
                return "<span style='color:red'>$fullname</span>";
            }
            return "<a href='http://www.w3.org/TR/xmlschema-2/#$name'>$fullname</a>";
        }
        if ($space == "tns") {
            foreach ($this->complextype as $key => $value) {
                if ($name == $value["name"]) {
                    return "<a href='#$name'>$fullname</a>";
                }
            }
            return "<span style='color:red'>$fullname</span>";
        }
        return "<span style='color:red'>??$fullname</span>";
    }

    public function addfunction($namefunction, $arr_in, $arr_out, $description = "") {
        return $this->_addfunction($namefunction, $arr_in, $arr_out, $description);
    }

    private function _addfunction($namefunction, $arr_in, $arr_out, $description = "") {
        if (count($arr_out) > 1) {
            trigger_error("output cannot exceed 1 value", E_USER_ERROR);
        }
        $description = ($description == "") ? "The function $namefunction" : $description;
        foreach ($arr_in as $key => $value) {
            if ($arr_in[$key]["name"] == "") {
                trigger_error("name must be defined", E_USER_ERROR);
            }
            $arr_in[$key]["name"] = (@$arr_in[$key]["name"] == "") ? "undefined" : $arr_in[$key]["name"];
            $arr_in[$key]["type"] = (@$arr_in[$key]["type"] == "") ? "s:string" : $arr_in[$key]["type"];
            $arr_in[$key]["minOccurs"] = (@$arr_in[$key]["minOccurs"] == "") ? 0 : $arr_in[$key]["minOccurs"];
            if (@$arr_in[$key]["maxOccurs"] > 1 or @$arr_in[$key]["maxOccurs"] == "unbounded") {
                trigger_error("maxOccurs cannot be >1", E_USER_ERROR);
            }
            $arr_in[$key]["maxOccurs"] = (@$arr_in[$key]["maxOccurs"] == "") ? 1 : $arr_in[$key]["maxOccurs"];
            $arr_in[$key]["extra"] = (@$arr_in[$key]["extra"] == "") ? "" : $arr_in[$key]["extra"];
            $arr_in[$key]["byref"] = (@$arr_in[$key]["byref"] == "") ? false : $arr_in[$key]["byref"];
        }
        // it must be only one value (or zero).
        foreach ($arr_out as $key => $value) {
            $arr_out[$key]["type"] = (@$arr_out[$key]["type"] == "") ? "s:string" : $arr_out[$key]["type"];
            $arr_out[$key]["minOccurs"] = (@$arr_out[$key]["minOccurs"] == "") ? 0 : $arr_out[$key]["minOccurs"];
            $arr_out[$key]["maxOccurs"] = (@$arr_out[$key]["maxOccurs"] == "") ? 1 : $arr_out[$key]["maxOccurs"];
            $arr_out[$key]["extra"] = (@$arr_out[$key]["extra"] == "") ? "" : $arr_out[$key]["extra"];
            $arr_out[$key]["byref"] = (@$arr_out[$key]["byref"] == "") ? false : $arr_out[$key]["byref"];
        }

        $this->operation[] = array(
            "name" => $namefunction,
            "in" => $arr_in,
            "out" => $arr_out,
            "description" => $description
        );

        return true;
    }

    public function addtype($nametype, $arr_param) {
        $this->_addtype($nametype, $arr_param);
    }

    private function _addtype($nametype, $arr_param) {
        foreach ($arr_param as $key => $value) {
            $arr_param[$key]["name"] = (@$arr_param[$key]["name"] == "") ? "undefined" : $arr_param[$key]["name"];
            $arr_param[$key]["type"] = (@$arr_param[$key]["type"] == "") ? "s:string" : $arr_param[$key]["type"];
            $arr_param[$key]["minOccurs"] = (@$arr_param[$key]["minOccurs"] == "") ? 0 : $arr_param[$key]["minOccurs"];
            $arr_param[$key]["maxOccurs"] = (@$arr_param[$key]["maxOccurs"] == "") ? 1 : $arr_param[$key]["maxOccurs"];
            $arr_param[$key]["extra"] = (@$arr_param[$key]["extra"] == "") ? "" : $arr_param[$key]["extra"];
            $arr_param[$key]["byref"] = (@$arr_param[$key]["byref"] == "") ? false : $arr_param[$key]["byref"];
            $arr_param[$key]["description"] =
                (@$arr_param[$key]["description"] == "") ? "a value" : $arr_param[$key]["description"];
        }
        $this->complextype[] = array(
            "name" => $nametype,
            "elements" => $arr_param
        );
    }

    private function _save_wsdl($filename) {
        if (!$fp = fopen($filename, 'a')) {
            return "file '$filename' can't be saved";
        }
        if (fwrite($fp, $this->genwsdl()) === false) {
            return "information can't be saved";
        }
        @fclose($fp);
        return "ok";
    }

    public function genwsdl() {
        if ($this->custom_wsdl != $this->FILE . "?wsdl") {
            // se usa un archivo customizado.
            $handle = @fopen($this->custom_wsdl, "r");
            if ($handle) {
                $contents = fread($handle, filesize($this->custom_wsdl));
            } else {
                $contents = "file or url :" . $this->custom_wsdl . " can't be open <br>\n";
            }
            fclose($handle);
            return $contents;
        }
        $cr = "\n";
        $tab1 = "\t";
        $wsdl = '<?xml version="1.0" encoding="' . $this->encoding . '" ?>' . $cr;
        $wsdl .= '<wsdl:definitions targetNamespace="' . $this->NAMESPACE .
            '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:tns="' .
            $this->NAMESPACE . '" ';
        $wsdl .= ' xmlns:s="http://www.w3.org/2001/XMLSchema" ';
        if ($this->soap12) {
            $wsdl .= ' xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" ';
        }
        $wsdl .= ' xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">' .
            $cr;
        if ($this->description) {
            $wsdl .= '<wsdl:documentation >' . $this->description . '</wsdl:documentation>';
        }
        // types ***
        $wsdl .= $tab1 . '<wsdl:types>' . $cr . '<s:schema elementFormDefault="qualified" targetNamespace="' .
            $this->NAMESPACE . '">' . $cr;
        // elementos in
        foreach ($this->operation as $key => $value) {
            $wsdl .= '<s:element name="' . $value["name"] . '">
                    <s:complexType>
                       <s:sequence>
                       ';
            foreach ($value["in"] as $key2 => $value2) {
                $minOccurs = $value2["minOccurs"];
                $maxOccurs = $value2["maxOccurs"];
                $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                    $value2["name"] . '" type="' . $value2["type"] . '" ' . @$value2["extra"] . '>';
                //if (@$value2["description"]) {
                //$wsdl.='<s:documentation >'.$value2["description"].'</s:documentation>';
                //}
                $wsdl .= '</s:element>';
            }
            $wsdl .= '</s:sequence>
                    </s:complexType>
                 </s:element>';
            // out
            $wsdl .= '<s:element name="' . $value["name"] . 'Response">
                    <s:complexType>
                       <s:sequence>
                       ';
            foreach ($value["out"] as $key2 => $value2) {
                $minOccurs = $value2["minOccurs"];
                $maxOccurs = $value2["maxOccurs"];
                $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                    $value["name"] . 'Result" type="' . $value2["type"] . '" ' . @$value2["extra"] . '>';
                //if (@$value2["description"]) {
                //$wsdl.='<s:documentation >'.$value2["description"].'</s:documentation>';
                //}
                $wsdl .= '</s:element>';
            }
            foreach ($value["in"] as $key2 => $value2) {
                if (@$value2["byref"]) {
                    $minOccurs = $value2["minOccurs"];
                    $maxOccurs = $value2["maxOccurs"];
                    $wsdl .= '<s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                        $value2["name"] . '" type="' . $value2["type"] . '" ' . @$value2["extra"] . '/>';
                }
            }

            $wsdl .= '</s:sequence>
                    </s:complexType>
                 </s:element>
                 ';
        }
        // complex types
        foreach ($this->complextype as $key => $value) {
            $wsdl .= '         <s:complexType name="' . $value["name"] . '">
                    <s:sequence>
                    ';
            foreach ($value["elements"] as $key2 => $value2) {
                $minOccurs = $value2["minOccurs"];
                $maxOccurs = $value2["maxOccurs"];
                $type = str_replace($this->NAMESPACE, "tns", $value2["type"]);
                $wsdl .= ' <s:element minOccurs="' . $minOccurs . '" maxOccurs="' . $maxOccurs . '" name="' .
                    $value2["name"] . '" type="' . $type . '" ' . @$value2["extra"] . '>';
                //if (@$value2["description"]) {
                //$wsdl.='<s:documentation >'.$value2["description"].'</s:documentation>';
                //}
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
        foreach ($this->operation as $key => $value) {
            $name = $value["name"];
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
        $wsdl .= '<wsdl:portType name="' . $this->NAME_WS . 'Soap">';
        foreach ($this->operation as $key => $value) {
            $name = $value["name"];
            $wsdl .= '<wsdl:operation name="' . $name . '">';
            if (@$value["description"]) {
                $wsdl .= '<wsdl:documentation>' . @$value["description"] . '</wsdl:documentation>';
            }
            $wsdl .= '<wsdl:input message="tns:' . $name . 'SoapIn"/><wsdl:output message="tns:' . $name .
                'SoapOut"/></wsdl:operation>';
        }
        $wsdl .= '</wsdl:portType>';
        if ($this->get) {
            $wsdl .= '<wsdl:portType name="' . $this->NAME_WS . 'HttpGet">';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                if (@$value["description"]) {
                    $wsdl .= '<wsdl:documentation>' . @$value["description"] . '</wsdl:documentation>';
                }
                $wsdl .= '<wsdl:input message="tns:' . $name . 'HttpGetIn"/><wsdl:output message="tns:' . $name .
                    'HttpGetOut"/></wsdl:operation>';
            }
            $wsdl .= '</wsdl:portType>';
        }
        if ($this->post) {
            $wsdl .= '<wsdl:portType name="' . $this->NAME_WS . 'HttpPost">';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                if (@$value["description"]) {
                    $wsdl .= '<wsdl:documentation >' . @$value["description"] . '</wsdl:documentation>';
                }
                $wsdl .= '<wsdl:input message="tns:' . $name . 'HttpPostIn"/><wsdl:output message="tns:' . $name .
                    'HttpPostOut"/></wsdl:operation>';
            }
            $wsdl .= '</wsdl:portType>';
        }

        // binding
        if ($this->soap11) {
            $wsdl .= '<wsdl:binding name="' . $this->NAME_WS . 'Soap" type="tns:' . $this->NAME_WS . 'Soap">
			<soap:binding transport="http://schemas.xmlsoap.org/soap/http"/>     
		   ';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">
				 <soap:operation soapAction="' . $this->NAMESPACE . $name . '" style="document"/>
				 <wsdl:input><soap:body use="literal"/></wsdl:input>
				 <wsdl:output><soap:body use="literal"/></wsdl:output>
			  </wsdl:operation>
			  ';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // binding12
        if ($this->soap12) {
            $wsdl .= '<wsdl:binding name="' . $this->NAME_WS . 'Soap12" type="tns:' . $this->NAME_WS . 'Soap">
				<soap12:binding transport="http://schemas.xmlsoap.org/soap/http"/>';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">
				 <soap12:operation soapAction="' . $this->NAMESPACE . $name . '" style="document"/>
				 <wsdl:input><soap12:body use="literal"/></wsdl:input>
				 <wsdl:output><soap12:body use="literal"/></wsdl:output>
			  </wsdl:operation>
			  ';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // binding12 (get)
        if ($this->soap12 and $this->get) {
            $wsdl .= '<wsdl:binding name="' . $this->NAME_WS . 'HttpGet" type="tns:' . $this->NAME_WS . 'HttpGet">
				<http:binding verb="GET" />';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                $wsdl .= '<http:operation location="/' . $name . '" />';
                $wsdl .= '<wsdl:input><http:urlEncoded /></wsdl:input><wsdl:output>';
                $wsdl .= '<mime:mimeXml part="HttpBody" /></wsdl:output></wsdl:operation>';
            }
            $wsdl .= '</wsdl:binding>';
        }


        // binding12 (post)
        if ($this->soap12 and $this->post) {
            $wsdl .= '<wsdl:binding name="' . $this->NAME_WS . 'HttpPost" type="tns:' . $this->NAME_WS . 'HttpPost">
				<http:binding verb="POST" />';
            foreach ($this->operation as $key => $value) {
                $name = $value["name"];
                $wsdl .= '<wsdl:operation name="' . $name . '">';
                $wsdl .= '<http:operation location="/' . $name . '" />';
                $wsdl .= '<wsdl:input><mime:content type="application/x-www-form-urlencoded" /></wsdl:input>';
                $wsdl .= '<wsdl:output><mime:mimeXml part="HttpBody" /></wsdl:output></wsdl:operation>';
            }
            $wsdl .= '</wsdl:binding>';
        }
        // service

        $wsdl .= '<wsdl:service name="' . $this->NAME_WS . '">';

        if ($this->soap11) {
            $wsdl .= '<wsdl:port name="' . $this->NAME_WS . 'Soap" binding="tns:' . $this->NAME_WS . 'Soap">
             <soap:address location="' . $this->FILE . '"/></wsdl:port>';
        }
        if ($this->soap12) {
            $wsdl .= '<wsdl:port name="' . $this->NAME_WS . 'Soap12" binding="tns:' . $this->NAME_WS . 'Soap12">
             <soap12:address location="' . $this->FILE . '"/></wsdl:port>';
            if ($this->get) {
                $wsdl .= '<wsdl:port name="' . $this->NAME_WS . 'HttpGet" binding="tns:' . $this->NAME_WS . 'HttpGet">
				 <http:address location="' . $this->FILE . '"/></wsdl:port>';
            }
            if ($this->post) {
                $wsdl .= '<wsdl:port name="' . $this->NAME_WS . 'HttpPost" binding="tns:' . $this->NAME_WS . 'HttpPost">
				 <http:address location="' . $this->FILE . '"/></wsdl:port>';
            }

        }
        $wsdl .= '</wsdl:service></wsdl:definitions>';
        return $wsdl;
    }


}