<?
@session_start();

include "./wslib/cklib.2.4.php";
include "csin.class.php";

		

$objws=new Sin_Proxy();

//$objws->GetSin


$FILE = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]; 
$NAMESPACE="";
$NAME_WS='Sin_WS';

$ns=new CKLIB_SRC($FILE,$NAMESPACE,$NAME_WS);
$ns->encoding="UTF-8";
//$ns->encoding="UTF-8";
$ns->verbose=2;
$ns->allowed_format["POST"]=false;
$ns->allowed_format["GET"]=false;

$ns->variable_type="object";
$ns->object_function="objws";
$ns->description="NNOC SIN WS Server";
$ns->addfunction("ping",
	array(
		array("name"=>"ping_param","type"=>"s:string"),
	),
	array(
		array("type"=>"s:string")
	),
	"Descripcion :Prueba de conexion"
	);
$ns->addfunction("GetSin",
	array(
		array("name"=>"id_sin","type"=>"s:integer"),
		array("name"=>"i_usuario","type"=>"tns:CUsuario"),
		array("name"=>"error","type"=>"tns:SINCError","byref"=>true)
	),
	array(
		array("type"=>"tns:CSin")
	),
	"Descripcion :GetSin obtiene los datos de una SIN"
	);
$ns->addfunction("GetEstado",
	array(
		array("name"=>"id_sin","type"=>"s:integer"),
		array("name"=>"i_usuario","type"=>"tns:CUsuario"),
		array("name"=>"error","type"=>"tns:SINCError","byref"=>true)
	),
	array(
		array("type"=>"s:string")
	),
	"Descripcion :GetSin obtiene los datos de una SIN"
	);	
$ns->addfunction("IngresarSin",
	array(
		array("name"=>"i_datos","type"=>"tns:CSin"),
		array("name"=>"i_usuario","type"=>"tns:CUsuario"),
		array("name"=>"error","type"=>"tns:SINCError","byref"=>true)
	),
	array(
		array("type"=>"s:string")
	),
	"Descripcion :Ingresa un SIN nuevo"
	);	
$ns->addfunction("UpdateSin",
	array(
		array("name"=>"i_datos","type"=>"tns:CSin"),
		array("name"=>"i_usuario","type"=>"tns:CUsuario"),
		array("name"=>"error","type"=>"tns:SINCError","byref"=>true)
	),
	array(
		array("type"=>"s:integer")
	),
	"Descripcion :Actualizar una SIN"
	);	
$ns->addfunction("UpdateEstadoSin",
	array(
		array("name"=>"id_sin","type"=>"s:integer"),
		array("name"=>"t_estado","type"=>"s:string"),
		array("name"=>"comentario","type"=>"tns:CSin_Comentario"),		 
		array("name"=>"i_usuario","type"=>"tns:CUsuario"),		
		array("name"=>"error","type"=>"tns:SINCError","byref"=>true)
	),
	array(
		array("type"=>"s:integer")
	),
	"Descripcion :Actualizar una SIN"
	);	
	
$ns->addfunction("GetSins",
	array(
		array("name"=>"filtro","type"=>"tns:SINCFiltro")
	),
	array(
		array("type"=>"tns:SINGrillaArray")
	),
	"Descripcion :Get_sin obtiene los datos de una SIN"
	);

$ns->addfunction("GetCombo_fen_servicios",
	array(
		array("name"=>"id_servicios","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);

$ns->addfunction("GetCombo_fen_tipo_rep",
	array(
		array("name"=>"id_tipo_rep","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);
$ns->addfunction("GetCombo_fen_impacto",
	array(
		array("name"=>"id_impacto","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_fen_subsistema",
	array(
		array("name"=>"t_sisafec","type"=>"s:string"),
		array("name"=>"t_servicio","type"=>"s:string"),
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_sin_resultado",
	array(
		array("name"=>"id_estado","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_v_six_tecnicos",
	array(
		array("name"=>"t_zona","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_r5estados",
	array(
		array("name"=>"id_estado","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
	
	
$ns->addfunction("GetCombo_org_localidad",
	array(
		array("name"=>"t_localidad","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_v_usuario",
	array(
		array("name"=>"id_usuario","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);		
$ns->addfunction("GetCombo_v_aprobador",
	array(
		array("name"=>"id_usuario","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);			

$ns->addfunction("GetCombo_org_direccion",
	array(
		array("name"=>"id_direccion","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);	
$ns->addfunction("GetCombo_sin_actividad",
	array(
		array("name"=>"pat_localidad","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);		
$ns->addfunction("GetCombo_pat_localidad",
	array(
		array("name"=>"codi_localidad","type"=>"s:string")
	),
	array(
		array("type"=>"tns:ArrayOfCCombobox")
	)
	);		
	
	
	
	
// ******** type
$ns->addtype("SINCError",
	array(
		array("name"=>"codigo","type"=>"s:int"),
		array("name"=>"descripcion","type"=>"s:string"),
	));
$ns->addtype("CCombobox",
	array(
		array("name"=>"id","type"=>"s:string"),
		array("name"=>"descripcion","type"=>"s:string"),
	));
$ns->addtype("ArrayOfCCombobox",
	array(
		array("name"=>"CCombobox","type"=>"tns:CCombobox","minOccurs"=>"0","maxOccurs"=>"unbounded")
	));
	

$ns->addtype("SINCFiltro",
	array(
		array("name"=>"status","type"=>"s:string"),
		array("name"=>"fechaIni","type"=>"s:string"),
		array("name"=>"fechaFIn","type"=>"s:string")
	));
		
	
$ns->addtype("SINGrilla",
	array(
		array("name"=>"Ciudad","type"=>"s:string"),
		array("name"=>"Localidad","type"=>"s:string"),
		array("name"=>"Nodo","type"=>"s:string"),
		array("name"=>"Servicio","type"=>"s:string"),
		array("name"=>"Sist_Afectado","type"=>"s:string"),
		array("name"=>"Subsistema","type"=>"s:string"),
		array("name"=>"Solicitante","type"=>"s:string"),
		array("name"=>"Ejec_o_Resp","type"=>"s:string"),
		array("name"=>"Preap_Tec","type"=>"s:string"),
		array("name"=>"Estado","type"=>"s:string"),
		array("name"=>"Titulo","type"=>"s:string"),
		array("name"=>"Descripcion","type"=>"s:string"),
		array("name"=>"Resultado_de_SI","type"=>"s:string"),
		array("name"=>"Fec_Hr_PreCierre","type"=>"s:string")
	));
$ns->addtype("SINGrillaArray",
	array(
		array("name"=>"SINGrilla","type"=>"tns:SINGrilla","minOccurs"=>"0","maxOccurs"=>"unbounded")
	));	
$ns->addtype("CUsuario",
	array(
		array("name"=>"user","type"=>"s:string"),
		array("name"=>"nombre","type"=>"s:string"),
		array("name"=>"clave","type"=>"s:string")	
	));
$ns->addtype("CSin",
	array(
		array("name"=>"id_ticket","type"=>"s:int"),
		array("name"=>"t_atecnico","type"=>"s:string"),
		array("name"=>"t_nnoc01","type"=>"s:string"),
		array("name"=>"t_estado","type"=>"s:string"),
		array("name"=>"t_estado_ini","type"=>"s:string"),
		array("name"=>"t_servicio","type"=>"s:string"),
		array("name"=>"t_sisafec","type"=>"s:string"),
		array("name"=>"titulo_si","type"=>"s:string"),
		array("name"=>"t_ultmod","type"=>"s:string"),
		array("name"=>"t_impacto","type"=>"s:string"),
		array("name"=>"t_subsistema","type"=>"s:string"),
		array("name"=>"t_ejeores","type"=>"s:string"),
		array("name"=>"t_oejec","type"=>"s:string"),
		array("name"=>"t_preap","type"=>"s:string"),
		array("name"=>"t_rsi","type"=>"s:string"),
		array("name"=>"t_d7","type"=>"s:string"),
		array("name"=>"t_nodo","type"=>"s:string"),
		array("name"=>"t_cuadrante","type"=>"s:string"),
		array("name"=>"t_fini","type"=>"s:string"),
		array("name"=>"t_festter","type"=>"s:string"),
		array("name"=>"t_id_solicitante","type"=>"s:string"),
		array("name"=>"t_desc","type"=>"s:string"),
		array("name"=>"t_direccion","type"=>"s:string"),
		array("name"=>"t_horter","type"=>"s:string"),
		array("name"=>"t_minter","type"=>"s:string"),
		array("name"=>"t_horini","type"=>"s:string"),
		array("name"=>"t_minini","type"=>"s:string"),
		array("name"=>"t_dirvtr","type"=>"s:string"),
		array("name"=>"t_fec_ingreso","type"=>"s:string"),
		array("name"=>"jefatura","type"=>"s:string"),
		array("name"=>"t_sinactividad","type"=>"s:integer"),
		array("name"=>"t_mailgrupos","type"=>"s:string"),
		array("name"=>"t_explode_reclamos","type"=>"s:string","minOccurs"=>"0","maxOccurs"=>"unbounded"),
		array("name"=>"rec_tv","type"=>"s:string"),
		array("name"=>"rec_tel","type"=>"s:string"),
		array("name"=>"rec_int","type"=>"s:string"),
		array("name"=>"reclamo_t","type"=>"s:string"),
		array("name"=>"t_upc","type"=>"s:string"),
		array("name"=>"t_abi","type"=>"s:string"),
		array("name"=>"precierre_hora","type"=>"s:string"),
		array("name"=>"t_eservicio","type"=>"s:string"),
		array("name"=>"id_fen","type"=>"s:string"),
		array("name"=>"check_acc_equipo","type"=>"s:string"),
		array("name"=>"equipo_acceso","type"=>"s:string"),
		array("name"=>"check_acc_establecido","type"=>"s:string"),
		array("name"=>"fmesa","type"=>"s:string"),
		array("name"=>"check_mccnnoc","type"=>"s:string"),
		array("name"=>"check_mccti","type"=>"s:string"),
		array("name"=>"intervenidos","type"=>"s:integer"),
		array("name"=>"mail_boss","type"=>"s:string"),
		array("name"=>"zona_local","type"=>"s:string","minOccurs"=>"0","maxOccurs"=>"unbounded"),
		array("name"=>"zona_city","type"=>"s:string","minOccurs"=>"0","maxOccurs"=>"unbounded"),
		array("name"=>"zona_zonas","type"=>"s:string","minOccurs"=>"0","maxOccurs"=>"unbounded"),
		array("name"=>"pre_cierre","type"=>"tns:CSin_PreCierre"),
		array("name"=>"comentarios","type"=>"tns:CSin_Comentario","minOccurs"=>"0","maxOccurs"=>"unbounded")	
		
	));

$ns->addtype("CSin_Gauss",
	array(
		array("name"=>"ElementoGauss","type"=>"s:string"),
		array("name"=>"pot_afec_inte_g","type"=>"s:integer"),
		array("name"=>"pot_afec_prem_g","type"=>"s:integer"),
		array("name"=>"pot_afec_anag_g","type"=>"s:integer"),
		array("name"=>"pot_afec_csv_g","type"=>"s:integer"),
		array("name"=>"pot_afec_toip_g","type"=>"s:integer"),
		array("name"=>"id_sin_gauss","type"=>"s:integer")	
	));

$ns->addtype("CSin_PreCierre",
	array(
		array("name"=>"t_dest","type"=>"s:string"),
		array("name"=>"tupc","type"=>"s:string"),
		array("name"=>"precierre","type"=>"s:string"),
		array("name"=>"tcomobs","type"=>"s:string"),
		array("name"=>"teservicio","type"=>"s:string"),
		array("name"=>"rec_tv","type"=>"s:integer"),
		array("name"=>"rec_tel","type"=>"s:integer"),
		array("name"=>"rec_int","type"=>"s:integer"),
		array("name"=>"tactuabases","type"=>"s:string")		
	));

$ns->addtype("CSin_Comentario",
	array(
		array("name"=>"autor_comentario","type"=>"s:string"),
		array("name"=>"descripcion_comentario","type"=>"s:string"),
		array("name"=>"fecha_comentario","type"=>"s:string"),
		array("name"=>"id_comentario","type"=>"s:integer"),
		array("name"=>"si_express_comentario","type"=>"s:string"),
		array("name"=>"tipo_comentario","type"=>"s:integer")
	));
	
$ns->addtype("CSinCreacionRespuesta",
	array(
		array("name"=>"id_ticket","type"=>"s:long"),
		array("name"=>"id_sin","type"=>"s:long"),
		array("name"=>"tiempo","type"=>"s:long"),
		array("name"=>"dias","type"=>"s:long"),
		array("name"=>"sistema","type"=>"s:string"),
		array("name"=>"afectacion","type"=>"s:string"),
		array("name"=>"subsistema","type"=>"s:string"),
		array("name"=>"ejecutorvtr","type"=>"s:string"),
		array("name"=>"preaprobador","type"=>"s:string"),
		array("name"=>"finicio","type"=>"s:string"),
		array("name"=>"ftermino","type"=>"s:string"),
		array("name"=>"dir_vtr","type"=>"s:string")
	));	
	
		 
	
	$ns->run();
exit;

die("");
// ************** implementacion

class Sin_Proxy
{
	private function util_combo($sql) {
		global $conn;
		$rs_util=ociparse($conn,$sql);
		//trigger_error("sql".$sql, E_USER_ERROR);
		if (!ociexecute($rs_util,OCI_DEFAULT)) {
			
		}
		$objlista=new ArrayOfCCombobox();
		$objlista->CCombobox=array();
		$r=array();
		while($campo=oci_fetch_array($rs_util)){
			$obj=new CCombobox();
			$obj->id=$campo[0];
			$obj->descripcion=$campo[0];
			
			$objlista->CCombobox[]=$obj;
			$r[]=$obj;
			
		}
		
		return $r;			
	}
	function GetCombo_fen_servicios($id_servicios="") {
		
		if ($id_servicios=="") {
			$sql="select id_servicios,descripcion from nnoc.fen_servicios order by descripcion";
		} else {
			$sql="select id_servicios,descripcion from nnoc.fen_servicios where id_servicios='$id_servicios'";
		}
		$objlista=new ArrayOfCCombobox();
		$objlista->CCombobox=array();
		$r=array();
		
		$obj=new CCombobox();
		$obj->id=1;
		$obj->descripcion="aaa".$id_servicios;
		
		$objlista->CCombobox[]=$obj;
		$r[]=$obj;
		$obj=new CCombobox();
		$obj->id=2;
		$obj->descripcion="bbb";
		
		$objlista->CCombobox[]=$obj;
		$r[]=$obj;		
		
		
		return $r;
	}
	function GetCombo_fen_tipo_rep($id_tipo_rep="") {
		global $conn;
		if ($id_tipo_rep=="") {
			$sql="select id_tipo_rep,descripcion from nnoc.fen_tipo_rep where servicios_id_servicios='' order by descripcion";
		} else {
			$sql="select id_tipo_rep,descripcion from nnoc.fen_tipo_rep where servicios_id_servicios='' where id_tipo_rep='$id_tipo_rep'";
		}
		return $this->util_combo($sql);	
	}
	function GetCombo_fen_impacto($id_impacto="") {
		global $conn;
		if ($id_impacto=="") {
			$sql="select id_impacto,descripcion from nnoc.fen_impacto order by descripcion";
		} else {
			$sql="select id_impacto,descripcion from nnoc.fen_impacto where id_impacto='$id_impacto'";
		}
		
		
		return $this->util_combo($sql);
	}	
	function GetCombo_fen_subsistema($t_sisafec="",$t_servicio="") {
		global $conn;
		if ($t_sisafec=="") {
			$sql="select id_sistema,descripcion from nnoc.fen_subsistema order by descripcion";
		} else {
			$sql="select id_sistema,descripcion from nnoc.fen_subsistema where id_sistema='$t_sisafec' and id_servicio='$t_servicio' order by descripcion";
		}
		return $this->util_combo($sql);
	}		
	function GetCombo_sin_resultado($id_estado="") {
		global $conn;
		if ($id_estado=="") {
			$sql="select id_estado,descripcion_estado from sin_resultado order by descripcion_estado";
		} else {
			$sql="select id_estado,descripcion_estado from sin_resultado where id_estado='$id_estado'";
		}
		return $this->util_combo($sql);
	}		
	function GetCombo_v_six_tecnicos($t_zona="") {
		global $conn;
		if ($t_zona=="") {
			$sql="select codigo_tecnico,initcap(nombre),celular_six_tecnico 
				from nnoc.v_six_tecnicos 
				 order by nombre";
		} else {
			$sql="select codigo_tecnico,initcap(nombre),celular_six_tecnico 
				from nnoc.v_six_tecnicos 
				where codigo_zona like '%'||substr(upper('$t_zona'),1,2)||'%' order by nombre";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_r5estados($id_estado="") {
		global $conn;
		if ($id_estado=="") {
			$sql="select id_estado,desc_estado from nnoc.r5estados";
		} else {
			$sql="select id_estado,desc_estado from nnoc.r5estados where id_estado='$id_estado'";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_org_localidad($t_localidad="") {
		global $conn;
		if ($t_localidad=="") {
			$sql="select codi_localidad,desc_localidad from organizacion.org_localidad";
		} else {
			$sql="select codi_localidad,,desc_localidad from organizacion.org_localidad where codi_localidad='$t_localidad'";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_v_usuario($id_usuario="") {
		global $conn;
		if ($id_usuario=="") {
			$sql="select id_usuario, trim(nombres)||' '||trim(apellidos)||' - '||trim(codigo_tecnico)||' - '||trim(TELEFONO_MOVIL) from nnoc.v_usuario order by Nombre01";
		} else {
			$sql="select id_usuario, trim(nombres)||' '||trim(apellidos)||' - '||trim(codigo_tecnico)||' - '||trim(TELEFONO_MOVIL) from nnoc.v_usuario where id_usuario='$id_usuario'";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_v_aprobador($id_usuario="") {
		global $conn;
		if ($id_usuario=="") {
			$sql="select id_usuario,nombres||' '||apellidos from nnoc.v_aprobador order by Nombres";
		} else {
			$sql="select id_usuario,nombres||' '||apellidos from nnoc.v_aprobador where id_usuario='$id_usuario'";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_org_direccion($id_direccion="") {
		global $conn;
		if ($id_direccion=="") {
			$sql="select id_direccion,tipo_direccion.NOMBRE||', '||organizacion.org_localidad.desc_localidad||', '|| direccion.descripcion
				from organizacion.org_direccion direccion
				inner join organizacion.org_localidad on
					direccion.CODIGO_COMUNA=organizacion.org_localidad.CODI_LOCALIDAD
				left join nnoc.tipo_direccion on
					 direccion.ID_TIPO_DIRECCION=nnoc.tipo_direccion.ID_TIPO_DIRECCION
				order by tipo_direccion.NOMBRE,organizacion.org_localidad.desc_localidad,direccion.descripcion";
		} else {
			$sql="select id_direccion,tipo_direccion.NOMBRE||', '||organizacion.org_localidad.desc_localidad||', '|| direccion.descripcion
				from organizacion.org_direccion direccion
				inner join organizacion.org_localidad on
					direccion.CODIGO_COMUNA=organizacion.org_localidad.CODI_LOCALIDAD
				left join nnoc.tipo_direccion on
					 direccion.ID_TIPO_DIRECCION=nnoc.tipo_direccion.ID_TIPO_DIRECCION
				where id_direccion='$id_direccion'";
		}
		return $this->util_combo($sql);
	}	
	function GetCombo_sin_actividad($id_actividad="") {
		global $conn;
		if ($id_actividad=="") {
			$sql="select id_actividad,desc_actividad from nnoc.sin_actividad order by desc_actividad";
		} else {
			$sql="select id_actividad,desc_actividad from nnoc.sin_actividad where id_actividad='$id_actividad'";
		}
		return $this->util_combo($sql);
	}		
	function GetCombo_pat_localidad($codi_localidad="") {
		global $conn;
		if ($codi_localidad=="") {
			$sql="select codi_localidad,desc_localidad from pat_localidad order by desc_localidad";
		} else {
			$sql="select codi_localidad,desc_localidad from pat_localidad where codi_localidad='$codi_localidad'";
		}
		return $this->util_combo($sql);
	}		











		
	
	function ping($ping_param="") {
		return "pong";
	}
	function IngresarSin($i_datos,$i_usuario,&$error) {
		// $i_datos=new CSin();
		$i_datos->_usuario=$i_usuario;
		$i_datos->IngresarSin($error);		
		return $i_datos->id_ticket; 
	}
	function UpdateSin($i_datos, $i_usuario, &$error) {
		// $i_datos=new CSin();
		$i_datos->_usuario=$i_usuario;
		$i_datos->UpdateSin($error);		
		return null;
	}	
	function GetEstado($id_sin, $i_usuario, &$error) {
		$tmp_datos=new CSin();
		return $tmp_datos->GetEstado($id_sin,$i_usuario,$error);
	}
	function UpdateEstadoSin($id_sin, $t_estado,$comentario,$i_usuario, &$error) {
		// $i_datos=new CSin();
		$tmp_datos=new CSin();
		
		$tmp_datos->_usuario=$i_usuario;
		$tmp_datos->GetSin($id_sin);
		$tmp_datos->comentarios=$comentario;
		$tmp_datos->t_estado=$t_estado;
		$tmp_datos->_usuario=$i_usuario;
		$tmp_datos->UpdateSin($error);		
		return null;
	}		
	
	
	function GetSins($filtro,&$error) {
		$tmp_datos=new CSin();
		return $tmp_datos->GetSins($filtro,$error);		
	}
	public function GetSin($id_sin, $i_usuario, &$error) {
		
		$tmp_datos=new CSin();
		$tmp_datos->_usuario=$i_usuario;		
		$tmp_datos->GetSin($id_sin,$error);		
		return $tmp_datos;
	}
	
}



?>