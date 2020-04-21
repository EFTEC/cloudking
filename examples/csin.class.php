<?
 // include_once('../../_database/conexion.php'); 
// pasar y retornar objeto como referencia.
// separar la implementacion del web service.


class SINCError {
	var $codigo; // integer
	var $descripcion; // string
}

class SINCFiltro {
	var $status; // string
	var $fechaIni; // string
	var $fechaFIn; // string
}

class SINGrilla {
	var $Ciudad; // string
	var $Localidad; // string
	var $Nodo; // string
	var $Servicio; // string
	var $Sist_Afectado; // string
	var $Subsistema; // string
	var $Solicitante; // string
	var $Ejec_o_Resp; // string
	var $Preap_Tec; // string
	var $Estado; // string
	var $Titulo; // string
	var $Descripcion; // string
	var $Resultado_de_SI; // string
	var $Fec_Hr_PreCierre; // string
}

class SINGrillaArray {
	var $SINGrilla; // SINGrilla
}

class CUsuario {
	var $user; // string
	var $nombre; // string
	var $clave; // string
}



class CSin {
	var $id_ticket; // integer
	var $t_atecnico; // string
	var $t_atecnico_desc; // string
	var $t_nnoc01; // string
	var $t_estado; // string
	var $t_estado_ini; // string
	var $t_servicio; // string
	var $t_sisafec; // string
	var $titulo_si; // string
	var $t_ultmod; // string
	var $t_impacto; // string
	var $t_subsistema; // string
	var $t_ejeores; // string
	var $t_oejec; // string
	var $t_preap; // string
	var $t_rsi; // string
	var $t_d7; // string
	var $t_nodo; // string
	var $t_cuadrante; // string
	var $t_fini; // string
	var $t_festter; // string
	var $t_id_solicitante; // string
	var $t_desc; // string
	var $t_direccion; // string
	var $t_horter; // string
	var $t_minter; // string
	var $t_horini; // string
	var $t_minini; // string
	var $t_dirvtr; // string
	var $t_fec_ingreso; // string
	var $jefatura; // string
	var $t_sinactividad; // integereger
	var $t_mailgrupos; // string
	var $t_explode_reclamos; // string
	var $rec_tv; // string
	var $rec_tel; // string
	var $rec_int; // string
	var $reclamo_t; // string
	var $t_upc; // string
	var $t_abi; // string
	var $precierre_hora; // string
	var $t_eservicio; // string
	var $id_fen; // string
	var $check_acc_equipo; // string
	var $equipo_acceso; // string
	var $check_acc_establecido; // string
	var $fmesa; // string
	var $check_mccnnoc; // string
	var $check_mccti; // string
	var $intervenidos; // integereger
	var $mail_boss; // string
	
	var $zona_local; // string
	var $zona_city; // string
	var $zona_zonas; // string
	/*
	var $ElementoGauss; // string
	var $pot_afec_inte_g; // int;
	var $pot_afec_prem_g; // int;
	var $pot_afec_anag_g; // int;
	var $pot_afec_csv_g; // int;
	var $pot_afec_toip_g; // int;
	var $id_sin_gauss; // int;
	*/
	var $pre_cierre; // tns:CSin_PreCierre
	var $comentarios; // tns:CSin_Comentario


	
	var  $_usuario; // CUsuario
	
	
	
	
	function IngresarSin( &$error) {
		try {
			global $conn;
			// Input Values...
			/*
			$_this='value';
			$_i_usuario=$_CUsuario;
			$_error='value';
			*/
			// End Input Values 
			$Susuario_id=$this->_usuario->user;
			$comu=$_SESSION[zona][local][0];
			$_SESSION["user"]["num_archivos"]=0;
			$_SESSION["user"]["hay_archivos"]=0;
			
			$_SESSION[zona][local]=$this->zona_local;
			$_SESSION[zona][local]=$this->zona_city;
			$_SESSION[zona][local]=$this->zona_zonas;
			
			$t_desc=$this->t_desc;
			$t_fini=$this->t_fini;
			$t_festter=$this->t_festter;
			$t_cuadrante=$this->t_cuadrante;
			$t_nodo=$this->t_nodo;
			$aplicacion=$this->aplicacion;
			$t_direccion=$this->t_direccion;
			$t_estado=$this->t_estado;
			$t_servicio=$this->t_servicio;
			$t_sisafec=$this->t_sisafec;
			$t_subsistema=$this->t_subsistema;
			$t_impacto=$this->t_impacto;
			$titulo_si=$this->titulo_si;
			$t_preap=$this->t_preap;
			$t_oejec=$this->t_oejec;
			$t_ejeores=$this->t_ejeores;
			$Susuario_login=$this->user;
			$t_dirvtr=$this->t_dirvtr;
			$id_usr=$this->_usuario->user;
			$t_sinactividad=$this->t_sinactividad;
			$t_d7=$this->t_d7;
			$comu="???";
			$mail_destino="???";
			$check_acc_equipo=$this->check_acc_equipo;
			$equipo_acceso=$this->equipo_acceso;
			$id_fen=$this->id_fen;
			$fmesa=$this->fmesa;
			$check_mccti=$this->check_mccti;
			$intervenidos=$this->intervenidos;
			
			
			
			include "./_sql/inserta_sin.php";
			echo "evt_code";
			var_dump($evt_code);			
			
			$this->id_ticket=$ticket;
			/*
			
			$_CSinCreacionRespuesta=new CSinCreacionRespuesta();
			
			$_CSinCreacionRespuesta->id_ticket=$ticket;
			$_CSinCreacionRespuesta->id_sin=$id_sin;
			$_CSinCreacionRespuesta->tiempo=$tiempo;
			$_CSinCreacionRespuesta->dias=$dias;
			$_CSinCreacionRespuesta->sistema=$sistema;
			$_CSinCreacionRespuesta->afectacion=$afectacion;
			$_CSinCreacionRespuesta->subsistema=$subsistemas;
			$_CSinCreacionRespuesta->ejecutorvtr=$ejecutorvtr;
			$_CSinCreacionRespuesta->preaprobador=$preaprobador;
			$_CSinCreacionRespuesta->finicio=$finicio;
			$_CSinCreacionRespuesta->ftermino=$ftermino;
			$_CSinCreacionRespuesta->dir_vtr=$dir_vtr;
			*/
			$error=null;
			return $evt_code; 
		} catch (Exception $_exception) {
			$error=array("codigo"=>$_exception->getCode(),"descripcion"=>$_exception->getMessage());
			return(null);
		}
	}
	
	function UpdateSin( &$error) {
		global $rs_generico,$conn,$sql_zona_afectada;
		try {
			
			$Susuario_id="???";
			$comu=$_SESSION[zona][local][0];
			$_SESSION["user"]["num_archivos"]=0;
			$_SESSION["user"]["hay_archivos"]=0;
			
			$_SESSION[zona][local]=$this->zona_local;
			$_SESSION[zona][local]=$this->zona_city;
			$_SESSION[zona][local]=$this->zona_zonas;
			
			$t_preap=$this->t_preap;
			$t_desc=$this->t_desc;
			$t_fini=$this->t_fini;
			$t_festter=$this->t_festter;
			$t_cuadrante=$this->t_cuadrante;
			$t_nodo=$this->t_nodo;
			$aplicacion=$this->aplicacion;
			$t_direccion=$this->t_direccion;
			$t_estado=$this->t_estado;
			$t_servicio=$this->t_servicio;
			$t_sisafec=$this->t_sisafec;
			$t_subsistema=$this->t_subsistema;
			$t_impacto=$this->t_impacto;
			$titulo_si=$this->titulo_si;
			$t_preap=$this->t_preap;
			$t_oejec=$this->t_oejec;
			$t_ejeores=$this->t_ejeores;
			$Susuario_login=$i_usuario->user;
			$t_dirvtr=$this->t_dirvtr;
			$id_usr="??????";
			$t_sinactividad=$this->t_sinactividad;
			$t_d7=$this->t_d7;
			$comu="???";
			$mail_destino="???";
			$check_acc_equipo=$this->check_acc_equipo;
			$equipo_acceso=$this->equipo_acceso;
			$id_fen=$this->id_fen;
			$fmesa=$this->fmesa;
			$check_mccti=$this->check_mccti;
			$intervenidos=$this->intervenidos;
			$mail_boss=$this->mail_boss;
			$t_id_solicitante=$this->t_id_solicitante;
			$id_sin=$this->id_ticket; // ????
			$_POST[id_sin]=$id_sin;
			// $ticket=$this->id_ticket;
			$_GET[id_sin]=$this->id_ticket;
			/*
			$_POST[ElementoGauss]=$this->ElementoGauss;
			$_POST[pot_afec_inte_g]=$this->pot_afec_inte_g;
			$_POST[pot_afec_prem_g]=$this->pot_afec_prem_g;
			$_POST[pot_afec_anag_g]=$this->pot_afec_anag_g;
			$_POST[pot_afec_csv_g]=$this->pot_afec_csv_g;
			$_POST[pot_afec_toip_g]=$this->pot_afec_toip_g;
			$_POST[id_sin_gauss]=$this->id_sin_gauss;
			*/
		
			
			include "./_sql/complemento_mod_sin.php";
			echo "evt_code";
			var_dump($evt_code);

			
			
			
			$_UpdateSinResult="1";
			return $_UpdateSinResult; 
		} catch (Exception $_exception) {
			$error=array("codigo"=>$_exception->getCode(),"descripcion"=>$_exception->getMessage());
			return(null);
		}
	}
	
	function GetSins($filtro,&$error) {
		try {
			global $rs_generico,$conn,$sql_zona_afectada;
			$status="SOGE";
			$status=@$filtro->status;
			include_once('./_sql/ver_gen.php');
			$a=0;
			$objarr=array();
			if(!oci_execute($rs_generico,OCI_DEFAULT)){
				dev_error_oracle(ocierror($rs_generico));
			}	
			while(@$row=oci_fetch_array($rs_generico)){
				$ticket=$row[0];
				$desc=substr($row[1],0,60);
				$titulo=substr($row[27],0,40);
				//saca backslash
				$titulo=str_replace("'"," ",$titulo);
				$titulo=str_replace("\\ ","",$titulo);
				
				$desc=str_replace("'"," ",$desc);
				$desc=str_replace("\\","",$desc);
				
				////////SACA ZONAS
				$sql_zona_afectada="select codi_localidad,tipo_localidad , organizacion.org_zona.DESC_ZONA
						from sin_ciudad_afectada
						inner join organizacion.org_zona on
						organizacion.org_zona.CODI_ZONA = codi_localidad 
						where id_sin=$ticket and tipo_localidad=3";
				$parse_comuna_afectada=oci_parse($conn,$sql_zona_afectada);
				oci_execute($parse_comuna_afectada,OCI_DEFAULT);
				$desc_zonas='';
				$cuenta_zonas=0;
				while ($rw=oci_fetch_array($parse_comuna_afectada)){
					$desc_zonas.=$rw[2].',';
					$cuenta_zonas++;
				}
				if($cuenta_zonas>1){
					$desc_zonas=substr(substr($desc_zonas,0,strlen($desc_zonas)-1),0,25).'...(m�s)';
				}else {
					$desc_zonas=substr($desc_zonas,0,strlen($desc_zonas)-1);
				}
				
				//////SACA CIUDADES
				$sql_ciudad_afectada="select codi_localidad,tipo_localidad , organizacion.org_subzona.DESC_subZONA
						from sin_ciudad_afectada
						inner join organizacion.org_subzona on
						organizacion.org_subzona.CODI_subZONA = codi_localidad 
						where id_sin=$ticket and tipo_localidad=1";
				$parse_ciudad_afectada=oci_parse($conn,$sql_ciudad_afectada);
				oci_execute($parse_ciudad_afectada,OCI_DEFAULT);
				$desc_ciudades='';
				$cuenta_ciudades=0;
				while ($rw=oci_fetch_array($parse_ciudad_afectada)){
					$desc_ciudades.=$rw[2].',';
					$cuenta_ciudades++;
				}
				if ($cuenta_ciudades>1){
					$desc_ciudades=substr(substr($desc_ciudades,0,strlen($desc_ciudades)-1),0,25).'...(m�s)';
				}else{
					$desc_ciudades=substr($desc_ciudades,0,strlen($desc_ciudades)-1);
				}
				
				//////SACA LOCALIDADES
				$sql_localidad_afectada="select sin_ciudad_afectada.codi_localidad,tipo_localidad , trim(organizacion.org_localidad.DESC_localidad)
						from sin_ciudad_afectada
						inner join organizacion.org_localidad on
						organizacion.org_localidad.CODI_localidad = sin_ciudad_afectada.codi_localidad 
						where id_sin=$ticket and tipo_localidad=2";
				$parse_localidad_afectada=oci_parse($conn,$sql_localidad_afectada);
				oci_execute($parse_localidad_afectada,OCI_DEFAULT);
				$desc_localidades='';
				$cuenta_localidades=0;
				while ($rw=oci_fetch_array($parse_localidad_afectada)){
					$desc_localidades.=$rw[2].',';
					$cuenta_localidades++;
				}
				if($cuenta_localidades>1){
					$desc_localidades=substr(substr($desc_localidades,0,strlen($desc_localidades)-1),0,25).'...(m�s)';
				}else{
					$desc_localidades=substr($desc_localidades,0,strlen($desc_localidades)-1);
				}
				
				if ($row[8]=='0'){
					$nodo='No Aplica';
				}else{
					$nodo=$row[8];
				}
				if ($row[7]=='0'){
					$cuadrante='No Aplica';
				}else{
					$cuadrante=$row[7];
				}
				$obj->Tipo=$row[52];
				$obj->Impacto=$row[34];
				$obj->N_SI=$ticket;
				$obj->Fec_Hr_de_Ini=$row[5];
				$obj->Fec_Hr_de_Fin=$row[6];
				$obj->Zona=$desc_zonas;
				$obj->Ciudad=$desc_ciudades;
				$obj->Localidad=$desc_localidades;
				$obj->Nodo=$nodo - $cuadrante;
				$obj->Servicio=$row[30];
				$obj->Sist_Afectado=$row[31];
				$obj->Subsistema=$row[33];
				$obj->Solicitante=$row[55];
				$obj->Ejec_o_Resp=$row[36];
				$obj->Preap_Tec=$row[32];
				$obj->Estado=$row[2];
				$obj->Titulo=$titulo;
				$obj->Descripcion=$desc;
				$obj->Resultado_de_SI=$row[35];
				$obj->Fec_Hr_PreCierre=$row[63];
				$objarr->SINGrilla[]=$obj;
			} // while
			$error->codigo=0;
			$error->descripcion="";
			
			return $objarr;
		} catch (Exception $_exception) {
			return(array("soap:Fault"=>'Caught exception: '. $_exception->getMessage()));
		}
	}
	
	function GetEstado($id_sin, $i_usuario, &$error) {
		try {
			// Input Values...
			/*
			$_id_sin=0;
			$_i_usuario=$_CUsuario;
			$_error='value';
			*/
			// End Input Values 
			$_GetEstadoResult='value';
			$error->codigo=0;
			$error->descripcion="";
			return $_GetEstadoResult; 
		} catch (Exception $_exception) {
			$error=array("codigo"=>$_exception->getCode(),"descripcion"=>$_exception->getMessage());
			return(null);
		}
	}	
	
	function GetSin($id_sin, &$error) {
		try {
			global $conn;
			$sql_sin="select * from v_sin_mini2 where evt_code= '$id_sin'";
			$rs_sin=ociparse($conn,$sql_sin);
			ociexecute($rs_sin,OCI_DEFAULT);
			$row=oci_fetch_array($rs_sin);
			// *********
			$this->id_ticket=$id_sin;
			$this->t_atecnico=$row[55];
			$this->t_nnoc01=$row[26];
			$this->t_estado=$row[2];
			$this->t_estado_ini=$row[2];
			$this->t_servicio=$row[12];
			$this->t_sisafec=$row[15];
			$this->titulo_si=$row[27];
			$this->t_ultmod=$row[57];
			$this->t_impacto=$row[25];
			$this->t_subsistema=$row[24];
			$this->t_ejeores=$row[38];
			$this->t_oejec=$row[39];
			$this->t_preap=$row[17];
			$this->t_rsi=$row[28];
			$this->t_d7=$row[66];
			$this->t_nodo=$row[8];
			$this->t_cuadrante=$row[7];
			$this->t_fini=$row[40]; // fecha y hora
			$this->t_festter=$row[43]; // fecha y hora
			$this->t_id_solicitante=$row[50];
			$this->t_desc=$row[1];
			$this->t_direccion=$row[46];
			$this->t_horter=$row[44];
			$this->t_minter=$row[45];
			$this->t_horini=$row[41];
			$this->t_minini=$row[42];
			$this->t_dirvtr=$row[47];
			$this->t_fec_ingreso=$row[49];
			$this->jefatura=$row[51];
			$this->t_sinactividad=$row[62];
			$this->t_mailgrupos=$row[54];
			if ($row[58]=="") {
				
			} else {
				$this->t_explode_reclamos=explode('-',$row[58]);
			}
			
			$this->rec_tv=$this->t_explode_reclamos[0];
			$this->rec_tel=$this->t_explode_reclamos[1];
			$this->rec_int=$this->t_explode_reclamos[2];
			$this->reclamo_t=$this->rec_tv+$this->rec_tel+$this->rec_int;
			$this->t_upc=$row[65];
			$this->t_abi=$row[59];
			$this->precierre_hora=$row[63];
			$this->t_eservicio=$row[60];
			$this->id_fen=$row[68];
			//acceso a equipos
			$this->check_acc_equipo=$row[69];
			$this->equipo_acceso=$row[70];
			$this->check_acc_establecido=$row[72];
			//fin acceso a equipos
			$this->fmesa=$row[71];
			$this->check_mccnnoc=$row[76];
			$this->check_mccti=$row[75];
			//intervenidos
			$this->intervenidos=$row[78];
			// archivos
			$_SESSION["user"]["num_archivos"]=0;
			
			// falta tener zonas.
			
			return $this;
		} catch (Exception $_exception) {
			$error=array("codigo"=>$_exception->getCode(),"descripcion"=>$_exception->getMessage());
			return(null);
		}
	}	
} // end class CSin

class CSinCreacionRespuesta {
	var $id_ticket; // s:long
	var $id_sin; // s:long
	var $tiempo; // s:long
	var $dias; // s:long
	var $sistema; // string
	var $afectacion; // string
	var $subsistema; // string
	var $ejecutorvtr; // string
	var $preaprobador; // string
	var $finicio; // string
	var $ftermino; // string
	var $dir_vtr; // string
}
class CSin_PreCierre {
	var $t_dest; // s:string
	var $tupc; // s:string
	var $precierre; // s:string
	var $tcomobs; // s:string
	var $teservicio; // s:string
	var $rec_tv; // s:integer
	var $rec_tel; // s:integer
	var $rec_int; // s:integer
	var $tactuabases; // s:string
}

class CSin_Comentario {
	var $autor_comentario; // s:string
	var $descripcion_comentario; // s:string
	var $fecha_comentario; // s:string
	var $id_comentario; // s:integer
	var $si_express_comentario; // s:string
	var $tipo_comentario; // s:integer
}

class CCombobox {
	var $id; // s:string
	var $descripcion; // s:string
}

class ArrayOfCCombobox {
	var $CCombobox; // tns:CCombobox
}




?>