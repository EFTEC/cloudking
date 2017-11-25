<?
include_once "./wslib/ckclient.2.3.php";





/***************************************** Implementation *****************************************/
class Sin_WSClient {
	var $url='http://localhost/currentproject/cloudking/webservice_example.php';
	var $tempuri='http://localhost/currentproject/cloudking/webservice_example.php/';

	// Descripcion :Prueba de conexion 
	// ping_param =  
	function ping($ping_param) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($ping_param,'ts:ping_param',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'ping');
		return @$resultado['pingResult'];
	}

	// Descripcion :GetSin obtiene los datos de una SIN 
	// id_sin =  
	// i_usuario =  
	// error =  
	function GetSin($id_sin, $i_usuario, &$error) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_sin,'ts:id_sin',false,false);
		$_param.=$_obj->array2xml($i_usuario,'ts:i_usuario',false,false);
		$_param.=$_obj->array2xml($error,'ts:error',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetSin');
		$error=@$resultado['error'];
		return @$resultado['GetSinResult'];
	}

	// Descripcion :GetSin obtiene los datos de una SIN 
	// id_sin =  
	// i_usuario =  
	// error =  
	function GetEstado($id_sin, $i_usuario, &$error) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_sin,'ts:id_sin',false,false);
		$_param.=$_obj->array2xml($i_usuario,'ts:i_usuario',false,false);
		$_param.=$_obj->array2xml($error,'ts:error',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetEstado');
		$error=@$resultado['error'];
		return @$resultado['GetEstadoResult'];
	}

	// Descripcion :Ingresa un SIN nuevo 
	// i_datos =  
	// i_usuario =  
	// error =  
	function IngresarSin($i_datos, $i_usuario, &$error) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($i_datos,'ts:i_datos',false,false);
		$_param.=$_obj->array2xml($i_usuario,'ts:i_usuario',false,false);
		$_param.=$_obj->array2xml($error,'ts:error',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'IngresarSin');
		$error=@$resultado['error'];
		return @$resultado['IngresarSinResult'];
	}

	// Descripcion :Actualizar una SIN 
	// i_datos =  
	// i_usuario =  
	// error =  
	function UpdateSin($i_datos, $i_usuario, &$error) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($i_datos,'ts:i_datos',false,false);
		$_param.=$_obj->array2xml($i_usuario,'ts:i_usuario',false,false);
		$_param.=$_obj->array2xml($error,'ts:error',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'UpdateSin');
		$error=@$resultado['error'];
		return @$resultado['UpdateSinResult'];
	}

	// Descripcion :Actualizar una SIN 
	// id_sin =  
	// t_estado =  
	// comentario =  
	// i_usuario =  
	// error =  
	function UpdateEstadoSin($id_sin, $t_estado, $comentario, $i_usuario, &$error) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_sin,'ts:id_sin',false,false);
		$_param.=$_obj->array2xml($t_estado,'ts:t_estado',false,false);
		$_param.=$_obj->array2xml($comentario,'ts:comentario',false,false);
		$_param.=$_obj->array2xml($i_usuario,'ts:i_usuario',false,false);
		$_param.=$_obj->array2xml($error,'ts:error',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'UpdateEstadoSin');
		$error=@$resultado['error'];
		return @$resultado['UpdateEstadoSinResult'];
	}

	// Descripcion :Get_sin obtiene los datos de una SIN 
	// filtro =  
	function GetSins($filtro) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($filtro,'ts:filtro',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetSins');
		return @$resultado['GetSinsResult'];
	}

	// The function GetCombo_fen_servicios 
	// id_servicios =  
	function GetCombo_fen_servicios($id_servicios) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_servicios,'ts:id_servicios',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_fen_servicios');
		return @$resultado['GetCombo_fen_serviciosResult'];
	}

	// The function GetCombo_fen_tipo_rep 
	// id_tipo_rep =  
	function GetCombo_fen_tipo_rep($id_tipo_rep) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_tipo_rep,'ts:id_tipo_rep',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_fen_tipo_rep');
		return @$resultado['GetCombo_fen_tipo_repResult'];
	}

	// The function GetCombo_fen_impacto 
	// id_impacto =  
	function GetCombo_fen_impacto($id_impacto) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_impacto,'ts:id_impacto',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_fen_impacto');
		return @$resultado['GetCombo_fen_impactoResult'];
	}

	// The function GetCombo_fen_subsistema 
	// t_sisafec =  
	// t_servicio =  
	function GetCombo_fen_subsistema($t_sisafec, $t_servicio) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($t_sisafec,'ts:t_sisafec',false,false);
		$_param.=$_obj->array2xml($t_servicio,'ts:t_servicio',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_fen_subsistema');
		return @$resultado['GetCombo_fen_subsistemaResult'];
	}

	// The function GetCombo_sin_resultado 
	// id_estado =  
	function GetCombo_sin_resultado($id_estado) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_estado,'ts:id_estado',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_sin_resultado');
		return @$resultado['GetCombo_sin_resultadoResult'];
	}

	// The function GetCombo_v_six_tecnicos 
	// t_zona =  
	function GetCombo_v_six_tecnicos($t_zona) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($t_zona,'ts:t_zona',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_v_six_tecnicos');
		return @$resultado['GetCombo_v_six_tecnicosResult'];
	}

	// The function GetCombo_r5estados 
	// id_estado =  
	function GetCombo_r5estados($id_estado) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_estado,'ts:id_estado',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_r5estados');
		return @$resultado['GetCombo_r5estadosResult'];
	}

	// The function GetCombo_org_localidad 
	// t_localidad =  
	function GetCombo_org_localidad($t_localidad) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($t_localidad,'ts:t_localidad',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_org_localidad');
		return @$resultado['GetCombo_org_localidadResult'];
	}

	// The function GetCombo_v_usuario 
	// id_usuario =  
	function GetCombo_v_usuario($id_usuario) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_usuario,'ts:id_usuario',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_v_usuario');
		return @$resultado['GetCombo_v_usuarioResult'];
	}

	// The function GetCombo_v_aprobador 
	// id_usuario =  
	function GetCombo_v_aprobador($id_usuario) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_usuario,'ts:id_usuario',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_v_aprobador');
		return @$resultado['GetCombo_v_aprobadorResult'];
	}

	// The function GetCombo_org_direccion 
	// id_direccion =  
	function GetCombo_org_direccion($id_direccion) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($id_direccion,'ts:id_direccion',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_org_direccion');
		return @$resultado['GetCombo_org_direccionResult'];
	}

	// The function GetCombo_sin_actividad 
	// pat_localidad =  
	function GetCombo_sin_actividad($pat_localidad) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($pat_localidad,'ts:pat_localidad',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_sin_actividad');
		return @$resultado['GetCombo_sin_actividadResult'];
	}

	// The function GetCombo_pat_localidad 
	// codi_localidad =  
	function GetCombo_pat_localidad($codi_localidad) {
		$_obj=new CKClient();
		$_obj->tempuri=$this->tempuri;
		$_param='';
		$_param.=$_obj->array2xml($codi_localidad,'ts:codi_localidad',false,false);
		$resultado=$_obj->loadurl($this->url,$_param,'GetCombo_pat_localidad');
		return @$resultado['GetCombo_pat_localidadResult'];
	}
} // end Sin_WS

$obj=new Sin_WSClient();


echo "llamando al webservice, funcion ping con parametro string <br>";
echo "resultado :<br>";
$resultado=$obj->GetCombo_fen_impacto("");
var_dump($resultado["ArrayOfCCombobox"]);



?>
