<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;
$mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;
//imprimir($_SESSION);
if($puesto<>0 && $mesa<>0){
    $sql="SELECT TOTALMESA,MESA FROM mesas_testigo WHERE idpuesto=".$puesto." and id=".$mesa." and IDTESTIGO='".$_SESSION['usuarioasociado']."'"; 
	$DBGestion->ConsultaArray($sql);
	$estado=$DBGestion->datos;	

	$bloquar=$estado[0]['TOTALMESA'];
	$mesaconsulta=$estado[0]['MESA'];
	//imprimir($bloquar);
?>					 
                          
	<div class="control-group">
  <label class="control-label">MESA <? echo $mesaconsulta?></label>   
  <div class="controls">
  </div>
    </div>
  <div class="control-group">
  <label class="control-label">TOTAL MESA<span class="required">*</span></label>
  <div class="controls">
	<input id="totalmesa" name="totalmesa" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>
	
  </div>
    </div>
	<div class="control-group">
  <label class="control-label">1.- MIGUEL ANGEL VARGAS HERNANDEZ<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato1" name="votoscandidato1" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">2.- HNO. ARIOSTO ARDILA SILVA<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato2" name="votoscandidato2" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">3.- JAVIER FUENTES CORTES<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato3" name="votoscandidato3" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">4.- LUIS GERARDO MARTINEZ MORENO<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato4" name="votoscandidato4" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">5.- JAIRO ERNESTO MORENO LOPEZ<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato5" name="votoscandidato5" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">6.- ALVARO SOTELO SOTELO <span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato6" name="votoscandidato6" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">7.- ORLANDO TARAZONA VILLAMIZAR<span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato7" name="votoscandidato7" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">8.-ALFONSO PULIDO LEON <span class="required">*</span></label>
  <div class="controls">
	<input id="votoscandidato8" name="votoscandidato8" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS EN BLANCO<span class="required">*</span></label>
  <div class="controls">
	<input id="votosenblanco" name="votosenblanco" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS NO MARCADOS<span class="required">*</span></label>
  <div class="controls">
	<input id="votosnomarcados" name="votosnomarcados" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
	</div>
	<div class="control-group">
  <label class="control-label">VOTOS NULOS<span class="required">*</span></label>
  <div class="controls">
	<input id="votosnulos" name="votosnulos" type="tel"  value="" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
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