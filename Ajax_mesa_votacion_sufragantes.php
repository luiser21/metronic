<?php 
header('Content-Type: text/html; charset=ISO-8859-1'); 
include_once "includes/GestionBD.new.class.php";
date_default_timezone_set('America/Bogota');
$DBGestion = new GestionBD('AGENDAMIENTO');	
	session_start();
 $mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;

    $sql="SELECT	mesas.ID,
				mesas.MESA				
				FROM
				mesas
				where ID in (".$mesa.") ";
	
 
$DBGestion->ConsultaArray($sql);
$mun=$DBGestion->datos;	
 $SUFRAGANTES =0;
$bloquar=0;
foreach ($mun as $datos){
	 $id = $datos['ID'];
	 $nombre = $datos['MESA'];
	  $sql="SELECT	MOVILIZADOS, META AS BLOQUEO				
				FROM
				boletines_departamentos
				WHERE candidato=".$_SESSION['idcandidato']." AND zona='MESA ".$nombre."'";
		$DBGestion->ConsultaArray($sql);
		$SUFRA=$DBGestion->datos;	
		$SUFRAGANTES = $SUFRA[0]['MOVILIZADOS'];	
		$bloquar=$SUFRA[0]['BLOQUEO'];
	  
 if($SUFRAGANTES==""){			
				    
					 $SUFRAGANTES =0;
					
				}	?>					 
                             
						      <div class="control-group">
                              <label class="control-label">Total Sufragantes<span class="required">*</span></label>
                              <div class="controls">
							               <input id="sufragantes" name="sufragantes" type="text"  value="<?php echo $SUFRAGANTES?>" class="required number" <?php if($bloquar!=0){ ?>  disabled <?php  } ?>/>
								                                           
                              </div>
                           </div>
                           <div class="control-group">
                           
                              <div class="controls">
							   </div> </div>  
							         
							 
						   
                           <div class="form-actions">		
						   
								<button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar!=0){ ?> disabled  <?php  } ?>>Guardar</button>
                            
                            
                           </div>
<?php } ?>