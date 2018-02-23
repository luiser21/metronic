<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;
$mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;
//imprimir($_SESSION);
if($puesto<>0 && $mesa<>0){
	 $sql="SELECT TOTALMESA FROM mesas_testigo WHERE idpuesto=".$puesto." and id=".$mesa." and IDTESTIGO='".$_SESSION['usuarioasociado']."'"; 
	$DBGestion->ConsultaArray($sql);
	$estado=$DBGestion->datos;	

	$bloquar=$estado[0]['TOTALMESA'];
	//imprimir($bloquar);
?>					 
                             
  <div class="control-group">
  <label class="control-label">TOTAL MESA<span class="required">*</span></label>
  <div class="controls">
	<input id="totalmesa" name="totalmesa" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>
	
  </div>
    </div>
	<div class="control-group">
  <label class="control-label">VOTOS <? echo substr($_SESSION["ntarjeton"], -3, 1).'00'; ?> <span class="required">*</span></label>
  <div class="controls">
	<input id="votospartido" name="votospartido" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS <? echo $_SESSION["ntarjeton"] ?><span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidado" name="votoscandidado" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS NULOS<span class="required">*</span></label>
  <div class="controls">
	<input id="votosnulos" name="votosnulos" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS NO MARCADOS<span class="required">*</span></label>
  <div class="controls">
	<input id="votosnomarcados" name="votosnomarcados" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS EN BLANCO<span class="required">*</span></label>
  <div class="controls">
	<input id="votosenblanco" name="votosenblanco" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">OBSERVACION</label>
  <div class="controls">
	<textarea id="observacion" name="observacion" rows="5" cols="40"></textarea>
	<!--<input id="observacion" name="observacion" type="text"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  -->
  </div>
	</div>
	<div class="form-actions">		
	<button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar>0){ ?> disabled  <?php  } ?>>Guardar</button>
</div>
<? } ?>