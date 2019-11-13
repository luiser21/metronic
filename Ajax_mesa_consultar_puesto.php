<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$documento= (!empty($_POST['documento']))? $_POST['documento']: 0;

if($documento<>0){
     $sql="SELECT DOCUMENTO,MESA  FROM puesto_votacion_rector WHERE DOCUMENTO=".$documento."  ORDER BY mesa desc"; 
	$DBGestion->ConsultaArray($sql);
	$estado=$DBGestion->datos;	

	@$bloquar=$estado[0]['DOCUMENTO'];
	@$mesaconsulta=$estado[0]['MESA'];
	//imprimir($bloquar);
?>					 
                          
	<div class="control-group">
  <label class="control-label">DOCUMENTO <? echo $documento?></label>   
  <div class="controls">
  </div>
    </div>
	<? if(!empty($bloquar)){?>
  <div class="control-group">
  SE ENCUENTRA HABILITADO PARA VOTAR 
  <? if($mesaconsulta<>0){?>
  <br><br>
   POSIBLE MESA DONDE VOTA NUMERO:  <? echo @$mesaconsulta?>
  <? }?>
    </div>
<? }else{?>
		NO SE ENCUENTRA HABILITADO PARA VOTAR POR FAVOR REVISAR
<? }
} ?>