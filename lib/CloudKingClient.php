<? /** @noinspection PhpUnused */
/** @noinspection DuplicatedCode */

/* 
CloudKing Client 
Version 2.4
Copyright Jorge Castro Castillo
License http://www.southprojects.com/product/cloudking/license1-1/
*/

namespace eftec\cloudking;

class CloudKingClient
{
    var $user_agent = "CloudKing Client (2.5)";
    var $charset = "UTF-8";
    var $soap = 1.2; // or 1.1
    var $tempuri = "http://tempuri.org";
    var $prefixns = "ts";
    var $cookie = "";
    var $proxyusername = "";
    var $proxypassword = "";
    // var $proxyhost = "";
    var $proxyport = "";

    /**
     * @param string $url
     * @param string $xmlparam
     * @param string $nameFunction
     * @param int $timeout
     * @return mixed
     */
    function loadurl($url, $xmlparam, $nameFunction, $timeout = 30) {
        $_url = parse_url($url);
        //$host=($this->proxyhost!="")?$this->proxyhost:((@$_url["host"])?$_url["host"]:"127.0.0.1");
        $port = ($this->proxyport != "") ? $this->proxyport : ((@$_url["port"]) ? $_url["port"] : 80);
        $raw = "";
        $raw .= "POST $url HTTP/1.1\r\n";
        $raw .= "Host: " . $_url["host"] . "\r\n";
        $raw .= "User-Agent: " . $this->user_agent . "\r\n";
        if ($this->cookie) {
            $raw .= "Set-Cookie: " . $this->cookie . "\r\n";
        }
        $raw .= "Accept-Encoding: gzip,deflate\r\n";
        if ($this->soap >= 1.2) {
            $raw .= "Content-Type: application/soap+xml;charset=" . $this->charset . ";action=\"" . $this->tempuri .
                $nameFunction . "\"\r\n";
            $content =
                "<soap:Envelope xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\" xmlns:" . $this->prefixns .
                "=\"" . $this->tempuri . "/\"><soap:Header/><soap:Body>";
            $content3 = "</" . $this->prefixns . ":" . $nameFunction . ">";
            $content3 .= "</soap:Body></soap:Envelope>";
        } else {
            $raw .= "Content-Type: text/xml;charset" . $this->charset . "\r\n";
            $raw .= "SOAPAction: \"" . $this->tempuri . "/" . $nameFunction . "\"\r\n";
            $content = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:" .
                $this->prefixns . "=\"" . $this->tempuri . "/\">";
            $content .= "<soapenv:Header/><soapenv:Body>";
            $content3 = "</" . $this->prefixns . ":" . $nameFunction . ">";
            $content3 .= "</soapenv:Body></soapenv:Envelope>";
        }
        $content .= "<" . $this->prefixns . ":" . $nameFunction . ">";
        $content .= $xmlparam . $content3;
        $raw .= ($this->proxyusername != "") ? "Proxy-Authorization: Basic " .
            base64_encode($this->proxyusername . ':' . $this->proxypassword) . "\r\n" : "";
        $raw .= "Content-Length: " . strlen($content) . "\r\n";
        $raw .= "Connection: Close\r\n";
        $raw .= "\r\n"; // <-- important.
        $raw .= $content;
        //echo $raw;
        $fp = fsockopen("tcp://" . $_url["host"], $port, $errno, $errstr, $timeout);
        fwrite($fp, $raw);
        $rawresponse = "";
        $a = 0;
        while (!feof($fp)) {
            $a++;
            $rawresponse .= fgets($fp, 128);

        }
        fclose($fp);
        $p0 = strpos($rawresponse, "<?xml");
        $p1 = strpos($rawresponse, "?>", $p0);
        $resultadoxml = substr($rawresponse, $p1 + 2);

        // ["Envelope"]["Body"]
        
        $g = $this->xml2array(@$resultadoxml);
        return $g["Envelope"]["Body"][$nameFunction . "Response"];
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
        if (!is_array($array)) {
            $array = array($name => $array);
        }
        $xmlstr = "";
        if ($start) {
            if ($contenttype) {
                @header("content-type:text/xml;charset=" . $this->charset);
            }
            $xmlstr .= '<?xml version="1.0" encoding="' . $this->charset . '"?>';
            $xmlstr .= '<' . $name . '>';
        }
        foreach ($array as $key => $child) {
            if (is_array($child)) {
                if (is_string($key)) {
                    $xmlstr .= '<' . $key . '>';
                } else {
                    $xmlstr .= '<' . $keyx . ">";
                }
                $xmlstr .= $this->array2xml($child, "", "", false, $key);
                if (is_string($key)) {
                    $xmlstr .= '</' . $key . '>';
                } else {
                    $xmlstr .= '</' . $keyx . ">";
                }
            } else {
                $type = $this->array2xmltype($child);
                $xmlstr .= '<' . (is_string($key) ? $key : $type) . '>' . $child . '</' .
                    (is_string($key) ? $key : $type) . '>';
            }
        }
        if ($start) {
            $xmlstr .= '</' . $name . '>';
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
        return "string";
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


    public function fixtag($tag) {
        $arr = explode(":", $tag);
        return ($arr[count($arr) - 1]);
    }
}
