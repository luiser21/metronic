<?php 
header('Content-Type: text/html; charset=ISO-8859-1'); 
include_once "includes/GestionBD.new.class.php";
$DBGestion = new GestionBD('AGENDAMIENTO');	
	session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;

echo '<select class="span6 m-wrap; required" name="mesas" onclick="mesa_votos()" id="mesas" >';   

  $sql="SELECT
				mesas.ID,
				mesas.MESA
				FROM
				mesas
				where IDPUESTO='".$puesto."' ";
	if($_SESSION["mesa"]!=0){
		$sql.=" AND MESA in (".$_SESSION["mesa"].") "; 
	}else{
		$sql.=" order by MESA";	
	}
$DBGestion->ConsultaArray($sql);
$mun=$DBGestion->datos;	
	
echo '<option value="">Seleccione....</option>'; 
foreach ($mun as $datos){
	 $id = $datos['ID'];
	 $nombre = $datos['MESA'];
	echo '<option value="'.$id.'">'.$nombre.'</option>';	
		
}
echo '</select>';
?>