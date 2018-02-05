<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;
if($puesto<>0){
	$sql="SELECT ESTADO FROM boletines_departamentos WHERE CANDIDATO=".$_SESSION['idcandidato']." and IDPUESTO=".$puesto." and IDBOLETIN = (SELECT ID FROM
	 boletines WHERE ESTADO=1)"; 
	$DBGestion->ConsultaArray($sql);
	$estado=$DBGestion->datos;	

	$bloquar=$estado[0]['ESTADO'];
?>					 
                             
  <div class="control-group">
  <label class="control-label">Movilizados<span class="required">*</span></label>
  <div class="controls">
	<input id="movilizados" name="movilizados" type="tel"  value="" class="span6 m-wrap; required number" <?php if($bloquar!=1){ ?>  disabled <?php  } ?>/>
  
  </div>
	</div>
	<div class="form-actions">		
	<button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar!=1){ ?> disabled  <?php  } ?>>Guardar</button>
</div>
<? } ?>