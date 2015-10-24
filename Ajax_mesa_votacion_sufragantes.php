<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;
    $sql="SELECT mesas.ID,mesas.MESA FROM mesas where ID=".$mesa; 
$DBGestion->ConsultaArray($sql);
$mun=$DBGestion->datos;	
$SUFRAGANTES ="";
$bloquar=0;
foreach ($mun as $datos){
	$id = $datos['ID'];
	$nombre = $datos['MESA'];
	$sql="SELECT	MOVILIZADOS, META AS BLOQUEO				
			FROM
			boletines_departamentos
			WHERE candidato=".$_SESSION['idcandidato']." AND zona='MESA ".$nombre."' and encargado='".$_SESSION['usuarioasociado']."'";
	$DBGestion->ConsultaArray($sql);
	$SUFRA=$DBGestion->datos;	
	@$SUFRAGANTES = $SUFRA[0]['MOVILIZADOS'];	
	@$bloquar=$SUFRA[0]['BLOQUEO'];
	  
 } ?>					 
                             
  <div class="control-group">
  <label class="control-label">Total Sufragantes<span class="required">*</span></label>
  <div class="controls">
	<input id="sufragantes" name="sufragantes" type="tel"  value="" class="required number" <?php if($bloquar!=0){ ?>  disabled <?php  } ?>/>
  
  </div>
	</div>
	<div class="form-actions">		
	<button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar!=0){ ?> disabled  <?php  } ?>>Guardar</button>
</div>
