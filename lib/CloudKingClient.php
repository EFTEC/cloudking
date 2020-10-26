<?php /** @noinspection UnknownInspectionInspection */
/** @noinspection NestedTernaryOperatorInspection */
/** @noinspection PhpUnused */
/** @noinspection DuplicatedCode */

/* 
CloudKing Client 
Version 2.6
Copyright Jorge Castro Castillo
License http://www.southprojects.com/product/cloudking/license1-1/
*/

namespace eftec\cloudking;

class CloudKingClient {
    public $user_agent = 'CloudKing Client (3.0)';
    public $charset = 'UTF-8';
    /** @var float=[1.1,1.2][$i] */
    public $soap = 1.2; // or 1.1
    public $tempuri = 'http://tempuri.org';
    public $prefixns = 'ts';
    public $cookie = '';
    public $proxyusername = '';
    public $proxypassword = '';
    // var $proxyhost = "";
    public $proxyport = '';
    public $lastError = '';
    public $lastResponse = '';

    /**
     * CloudKingClient constructor.
     *
     * @param float  $soap
     * @param string $tempuri
     */
    public function __construct($soap = 1.2, $tempuri = 'http://tempuri.org') {
        $this->soap = $soap;
        $this->tempuri = $tempuri;
    }

    /**
     * @param string $url
     * @param string $xmlparam
     * @param string $nameFunction
     * @param int    $timeout
     *
     * @return mixed|bool returns false if the operation fails
     */
    public function loadurl($url, $xmlparam, $nameFunction, $timeout = 30) {
        $header = [];
        $this->lastError = '';
        $this->lastResponse = '';

        $header[] = 'User-Agent: ' . $this->user_agent;
        if ($this->cookie) {
            $header[] = 'Set-Cookie: ' . $this->cookie;
        }
        $header[] = 'Accept-Encoding: gzip,deflate';
        if ($this->soap >= 1.2) {
            $header[] = 'Content-Type: application/soap+xml;charset=' . $this->charset . ';action="' . $this->tempuri .
                $nameFunction . '"';
            $content
                = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:' . $this->prefixns .
                '="' . $this->tempuri . '/"><soap:Header/><soap:Body>';
            $content3 = '</' . $this->prefixns . ':' . $nameFunction . '>';
            $content3 .= '</soap:Body></soap:Envelope>';
        } else {
            $header[] = 'Content-Type: text/xml;charset' . $this->charset . '';
            $header[] = "SOAPAction: \"{$this->tempuri}/{$nameFunction}\"";
            $content = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" ' .
                "xmlns:{$this->prefixns}=\"{$this->tempuri}/\">";
            $content .= '<soapenv:Header/><soapenv:Body>';
            $content3 = "</{$this->prefixns}:{$nameFunction}>";
            $content3 .= '</soapenv:Body></soapenv:Envelope>';
        }
        $content .= '<' . $this->prefixns . ':' . $nameFunction . '>';
        $content .= $xmlparam . $content3;
        $header[] = ($this->proxyusername) ? 'Proxy-Authorization: Basic ' .
            base64_encode($this->proxyusername . ':' . $this->proxypassword) . '' : '';

        $rawresponse = $this->httpPost($url, $content, $header, $timeout);
        $this->lastResponse = $rawresponse;

        if ($rawresponse === false) {
            return false;
        }

        $p0 = strpos($rawresponse, '<?xml');
        $p1 = strpos($rawresponse, '?>', $p0);
        if ($p0 === false || $p1 === false) {
            $this->lastError = $this->lastError === '' ? 'Error not xml tag' : $this->lastError;
            return false;
        }
        $resultadoxml = substr($rawresponse, $p1 + 2);

        $g = $this->xml2array($resultadoxml);
        $nfR = $nameFunction . 'Response';
        // ["Envelope"]["Body"]
        if (!isset($g['Envelope']['Body'][$nfR])) {
            $this->lastError = $this->lastError === '' ? 'Error no Envelope and Body tag' : $this->lastError;
            return false;
        }
        return $g['Envelope']['Body'][$nfR];
    }

    /**
     *
     * @param string $url
     * @param string $data
     * @param array  $header
     * @param int    $timeout Timeout in seconds.
     *
     * @return bool|string
     */
    private function httpPost($url, $data, $header = [], $timeout = 30) {
        $curl = curl_init($url);
        if ($curl === false) {
            return false;
        }
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //timeout in seconds
        $response = curl_exec($curl);

        $this->lastError = curl_error($curl);
        if ($this->lastError === '') {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode > 400) {
                $this->lastError = 'Error Code ' . $httpcode;
            }
        }

        curl_close($curl);
        return ($this->lastError !== '') ? false : $response;
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

    public function array2xml($array, $name = 'root', $contenttype = true, $start = true, $keyx = '') {
        if (!is_array($array)) {
            $array = array($name => $array);
        }
        $xmlstr = '';
        if ($start) {
            if ($contenttype) {
                @header('content-type:text/xml;charset=' . $this->charset);
            }
            $xmlstr .= '<?xml version="1.0" encoding="' . $this->charset . '"?>';
            $xmlstr .= '<' . $name . '>';
        }
        foreach ($array as $key => $child) {
            if (is_array($child)) {
                if (is_string($key)) {
                    $xmlstr .= '<' . $key . '>';
                } else {
                    $xmlstr .= '<' . $keyx . '>';
                }
                $xmlstr .= $this->array2xml($child, '', '', false, $key);
                if (is_string($key)) {
                    $xmlstr .= '</' . $key . '>';
                } else {
                    $xmlstr .= '</' . $keyx . '>';
                }
            } else {
                $type = $this->array2xmltype($child);
                $xmlstr .= '<' . (is_string($key) ? $key : $type) . '>' . $child . '</' .
                    (is_string($key) ? $key : $type) . '>';
            }
        }
        if ($start) {
            $xmlstr .= "</{$name}>";
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
        return 'string';
    }

    public function xml2array($xml) {
        $parentKey = [];
        $result = [];
        //echo "<pre>";
        //var_dump(htmlentities($xml));
        //echo "</pre>";
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'utf-8');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        $f = @xml_parse_into_struct($parser, trim($xml), $xmls);
        @xml_parser_free($parser);
        if ($f === 0) {
            return null;
        }

        /*
         We convert siblings in numeric indexes.
         <parent>
            <node> <-- open
                <child>
            </node> <-- close (ignored)
            <node>
                <child>
            </node>
            <node>
                <child>
            </node>
         </parent>
            ['node'=>'child']
         <parent>
            <0> <-- open 
                <child>
            </node>
            <1> 
                <child>
            </node>
            <2>
                <child>
            </node>
         </parent> <-- change of level.
         [0=>'child',1=>'child','2'=>'child']        
        */
        $c = count($xmls);
        /** @noinspection ForeachInvariantsInspection */
        for ($i0 = 0; $i0 < $c; $i0++) {
            $xitem =& $xmls[$i0];
            if (isset($xitem['type']) && $xitem['type'] === 'open') {
                $findme = $xitem['tag'];
                $findmelevel = $xitem['level'];
                $found = 0;
                for ($i1 = $i0 + 1; $i1 < $c; $i1++) {
                    $xitem1 =& $xmls[$i1];
                    $level = $xitem1['level'];
                    if ($level < $findmelevel) {
                        // end of the siblings 
                        break;
                    }
                    if ($level === $findmelevel && isset($xitem1['type']) && $xitem1['type'] === 'open'
                        && $xitem1['tag'] === $findme
                    ) {
                        // found a sibling
                        if ($found === 0) {
                            // we replace the first sibling.
                            $xitem['tag'] = 0;
                        }
                        // and we replace the current sibling
                        $found++;
                        $xitem1['tag'] = $found;
                    }
                }
            }
        }

        foreach ($xmls as $x) {
            //var_dump($x);
            //echo "<br>";
            $type = $x['type'];
            $level = $x['level'];
            $value = isset($x['value']) ? $x['value'] : null;
            $tag = $x['tag'];
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



    private function addElementArray(&$array, $keys, $level, $tag, $value) {
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

    public function fixtag($tag) {
        $arr = explode(':', $tag);
        return ($arr[count($arr) - 1]);
    }
}
