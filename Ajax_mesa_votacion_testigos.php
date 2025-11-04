<?php 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;
$mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;
//imprimir($_SESSION);
if($puesto<>0 && $mesa<>0){
     // obtener valores existentes incluyendo NF000..NF010 si están en la tabla
     $sql="SELECT TOTALMESA, VOTOPARTIDO, VOTOS_CANDIDATOS, VOTOS_BLANCO, VOTOS_NULOS, VOTOS_NO_MARCADOS, OBSERVACIONES,
               IFNULL(NF000,'') AS NF000, IFNULL(NF001,'') AS NF001, IFNULL(NF002,'') AS NF002, IFNULL(NF003,'') AS NF003,
               IFNULL(NF004,'') AS NF004, IFNULL(NF005,'') AS NF005, IFNULL(NF006,'') AS NF006, IFNULL(NF007,'') AS NF007,
               IFNULL(NF008,'') AS NF008, IFNULL(NF009,'') AS NF009, IFNULL(NF010,'') AS NF010
           FROM mesas_testigo
           WHERE idpuesto=".intval($puesto)." and id=".intval($mesa)." and IDTESTIGO='".$_SESSION['usuarioasociado']."'";
    $DBGestion->ConsultaArray($sql);
    $estado=$DBGestion->datos;	

    $bloquar = intval($estado[0]['TOTALMESA']);
    // valores previos
    $prev = $estado[0];
?>					 


 <!-- NF000..NF010 inputs -->
    <div class="control-group">
      <label class="control-label">VOTOS (NF000 .. NF010)</label>
      <div class="controls">
        <div class="row-fluid">
          <?php for($k=0;$k<=10;$k++):
              $key = 'NF' . str_pad($k,3,'0',STR_PAD_LEFT);
              $val = isset($prev[$key]) ? intval($prev[$key]) : 0;
          ?>
            <div class="span4" style="margin-bottom:6px;">
              <label style="font-weight:normal;"><?php echo $key; ?> <?php if($k==0) echo '(VOTOPARTIDO)'; elseif($k==1) echo ''; ?></label>
              <input type="number" name="<?php echo strtolower($key); ?>" id="<?php echo strtolower($key); ?>" class="required number" value="<?php echo htmlspecialchars($prev[$key]); ?>" min="0" <?php if($bloquar>0){ echo 'disabled'; } ?>>
            </div>
          <?php endfor; ?>
        </div>
        <p class="help-block">Complete las casillas NF000..NF010. NF000.</p>
      </div>
    </div>

       
    </div>
    <div class="control-group">
  <label class="control-label">VOTOS NULOS<span class="required">*</span></label>
  <div class="controls">
    <input id="votosnulos" name="votosnulos" type="tel"  value="<?php echo htmlspecialchars($prev['VOTOS_NULOS']); ?>" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
    </div>
    <div class="control-group">
  <label class="control-label">VOTOS NO MARCADOS<span class="required">*</span></label>
  <div class="controls">
    <input id="votosnomarcados" name="votosnomarcados" type="tel"  value="<?php echo htmlspecialchars($prev['VOTOS_NO_MARCADOS']); ?>" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
    </div>
    <div class="control-group">
  <label class="control-label">VOTOS EN BLANCO<span class="required">*</span></label>
  <div class="controls">
    <input id="votosenblanco" name="votosenblanco" type="tel"  value="<?php echo htmlspecialchars($prev['VOTOS_BLANCO']); ?>" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>  
  </div>
    </div>

	<div class="control-group">
  <label class="control-label">TOTAL VOTOS MESA<span class="required">*</span></label>
  <div class="controls">
    <input id="totalmesa" name="totalmesa" type="tel"  value="<?php echo htmlspecialchars($prev['TOTALMESA']); ?>" class="required number" <?php if($bloquar>0){ ?>  disabled <?php  } ?>/>
    
  </div>
    </div>
   

    <div class="control-group">
  <label class="control-label">OBSERVACION</label>
  <div class="controls">
    <textarea id="observacion" name="observacion" rows="5" cols="40" <?php if($bloquar>0){ echo 'disabled'; } ?> ><?php echo htmlspecialchars($prev['OBSERVACIONES']); ?></textarea>
  </div>
    </div>
    <div class="form-actions">		
    <button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar>0){ ?> disabled  <?php  } ?>>Guardar</button>
</div>
<? } ?>