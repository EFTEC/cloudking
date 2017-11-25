<?
/* 
CloudKing Client 
Version 2.3.4
Copyright Jorge Castro Castillo
License http://www.southprojects.com/product/cloudking/license1-1/
*/
class CKClient {
	var $version="2.3.4";
	var $user_agent="CloudKing Client (2.3.4)";
	var $charset="UTF-8";
	var $soap=1.2; // or 1.1
	var $tempuri="http://tempuri.org";
	var $prefixns = "ts";
	var $cookie="";
	var $proxyusername="";
	var $proxypassword="";
	var $proxyhost="";
	var $proxyport="";
		function loadurl($url,$xmlparam,$nameFunction,$timeout=30) {
		$_url=parse_url($url);
		$host=($this->proxyhost!="")?$this->proxyhost:((@$_url["host"])?$_url["host"]:"127.0.0.1");
		$port=($this->proxyport!="")?$this->proxyport:((@$_url["port"])?$_url["port"]:80);
		$raw="";
		$raw.="POST $url HTTP/1.1\r\n";
		$raw.="Host: ".$_url["host"]."\r\n";
		$raw.="User-Agent: ".$this->user_agent."\r\n";
		if ($this->cookie) {
			$raw.="Set-Cookie: ".$this->cookie."\r\n";
		}
		$raw.= "Accept-Encoding: gzip,deflate\r\n";
		if ($this->soap>=1.2) {
			$raw.="Content-Type: application/soap+xml;charset=".$this->charset.";action=\"".$this->tempuri.$nameFunction."\"\r\n";			
			$content="<soap:Envelope xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\" xmlns:".$this->prefixns."=\"".$this->tempuri."/\"><soap:Header/><soap:Body>";		
			$content3 = "</".$this->prefixns.":".$nameFunction.">";
			$content3 .= "</soap:Body></soap:Envelope>";			
		} else {
			$raw.="Content-Type: text/xml;charset".$this->charset."\r\n";
			$raw.="SOAPAction: \"".$this->tempuri."/".$nameFunction."\"\r\n";		
			$content="<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:".$this->prefixns."=\"".$this->tempuri."/\">";
			$content.= "<soapenv:Header/><soapenv:Body>";
			$content3 = "</".$this->prefixns.":".$nameFunction.">";
			$content3.= "</soapenv:Body></soapenv:Envelope>";
		}
		$content.= "<".$this->prefixns.":".$nameFunction.">";
		$content.=$xmlparam.$content3;
		$raw.=($this->proxyusername!="")?"Proxy-Authorization: Basic ".base64_encode($this->proxyusername.':'.$this->proxypassword)."\r\n":"";		
		$raw.="Content-Length: ".strlen($content)."\r\n";
		$raw.="Connection: Close\r\n";
		$raw.="\r\n"; // <-- important.
		$raw.=$content;
		echo $raw;
		$fp = fsockopen("tcp://".$_url["host"], $port, $errno, $errstr,$timeout);
		fwrite($fp, $raw);
		$rawresponse="";
		$a=0;
		while (!feof($fp)) {
			$a++;
			$rawresponse.= fgets($fp, 128);
			
		}		
		fclose($fp);	
		$p0=strpos($rawresponse,"<?xml");
		$p1=strpos($rawresponse,"?>",$p0);
		$resultadoxml=substr($rawresponse,$p1+2);

		// ["Envelope"]["Body"]
		$g=$this->xml2array(@$resultadoxml);
		
		return $g["Envelope"]["Body"][$nameFunction."Response"];
	}
    function fixarray2xml($string) {
		if (!is_array($string)) { return $string; }
        $arr=explode("\n", $string);
        $resultado="";
        for ($i=1; $i < count($arr); $i++) {
            $l=trim($arr[$i]);
            $lant=trim($arr[$i - 1]);
            if ($l != $lant) { $resultado.=$arr[$i - 1] . "\n"; }
        }
        return $resultado;
    }
	function array2xml($array, $name="root", $contenttype=TRUE, $start=TRUE, $keyx="") {
        if (!is_array($array)) {  $array=array($name=>$array); }        
        $xmlstr="";
        if ($start) {
            if ($contenttype) {
                @header("content-type:text/xml;charset=".$this->charset);
			}
            $xmlstr.='<?xml version="1.0" encoding="'.$this->charset.'"?>' ;
            $xmlstr.='<'.$name.'>' ;            
        }
        foreach ($array as $key => $child) {
            if (is_array($child)) {
                if (is_string($key)) { $xmlstr.= '<'.$key.'>';}
                else { $xmlstr.='<'.$keyx.">"; }
                $xmlstr.=$this->array2xml($child, "", "", FALSE, $key);
                if (is_string($key)) { $xmlstr.= '</'.$key.'>';}
                else { $xmlstr.='</'.$keyx.">"; }
            } else { 
				$type=$this->array2xmltype($child);
				$xmlstr.= '<'.(is_string($key)?$key:$type).'>'.$child.'</'.(is_string($key)?$key:$type).'>';
			}
        }
        if ($start)
            $xmlstr.='</'.$name.'>';
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
    public function xml2array($contents, $get_attributes=0, $priority='tag') {
        if (!$contents)
            return array();
        if (!function_exists('xml_parser_create')) {
        return array(); }
        $parser=xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING,
            $this->charset); 
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return; 
        $xml_array=array();
        $parents=array();
        $opened_tags=array();
        $arr=array();
        $current=&$xml_array; 
        $repeated_tag_index=array();    
        foreach ($xml_values as $data) {
            unset($attributes, $value); 
            extract($data); 
            $result=array();
            $attributes_data=array();
            if (isset($value)) {
                if ($priority == 'tag')
                    $result=$value;
                else
                    $result['value']=$value; 
            }
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr]=$val;
                    else
                        $result['attr'][$attr]=$val; 
                }
            }
            $tag=$this->fixtag($tag);
            if ($type == "open") { 
                $parent[$level - 1]=&$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag]=$result;
                    if ($attributes_data)
                        $current[$tag . '_attr']=$attributes_data;
                    $repeated_tag_index[$tag . '_' . $level]=1;
                    $current=&$current[$tag];
                } else {
                    if (isset($current[$tag][0])) { 
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]]=$result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else
                        {          
                        $current[$tag]=array
                            (
                            $current[$tag],
                            $result
                            );
                        $repeated_tag_index[$tag . '_' . $level]=2;
                        if (isset($current[$tag . '_attr']))
                            { 
                            $current[$tag]['0_attr']=$current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index=$repeated_tag_index[$tag . '_' . $level] - 1;
                    $current=&$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { 
                if (!isset($current[$tag])) {
                    $current[$tag]=$result;
                    $repeated_tag_index[$tag . '_' . $level]=1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr']=$attributes_data;
                } else { 
                    if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]]=$result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data)
                            { $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr']=$attributes_data; }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $tmp=$current[$tag];
                        @$current[$tag]=array
                            (
                            $tmp,
                            $result
                            ); 
                        $repeated_tag_index[$tag . '_' . $level]=1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr']))
                                { 
                                $current[$tag]['0_attr']=$current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data)
                                { $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr']=$attributes_data; }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($type == 'close') { 
            $current=&$parent[$level - 1]; }
        }
        return ($xml_array);
    }
    public function fixtag($tag) {
        $arr=explode(":", $tag);
        return ($arr[count($arr) - 1]);
    }
	}
?>