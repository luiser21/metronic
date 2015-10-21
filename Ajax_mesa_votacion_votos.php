<?php 
header('Content-Type: text/html; charset=ISO-8859-1'); 
include_once "includes/GestionBD.new.class.php";
$DBGestion = new GestionBD('AGENDAMIENTO');	
	session_start();
 $mesa= (!empty($_POST['mesas']))? $_POST['mesas']: 0;



    $sql="SELECT
				mesas.ID,
				mesas.MESA,
				mesas.CANDIDATOS,
				mesas.VOTOS_BLANCO,
				mesas.VOTOS_NULOS,
				mesas.VOTOS_NO_MARCADOS,
				mesas.SUFRAGANTES,
				mesas.OBSERVACIONES,
				mesas.VOTOS_CANDIDATOS
				FROM
				mesas
				where ID in (".$mesa.") ";
	
 
$DBGestion->ConsultaArray($sql);
$mun=$DBGestion->datos;	


$bloquar="";

foreach ($mun as $datos){
	 $id = $datos['ID'];
	 $nombre = $datos['MESA'];
	 $bloquar = $datos['CANDIDATOS'];
	 $VOTOS_BLANCO = $datos['VOTOS_BLANCO'];
	 $VOTOS_NULOS = $datos['VOTOS_NULOS'];
	 $VOTOS_NO_MARCADOS = $datos['VOTOS_NO_MARCADOS'];
	 $SUFRAGANTES = $datos['SUFRAGANTES'];
	 $OBSERVACIONES = $datos['OBSERVACIONES'];
	 $VOTOS_CANDIDATOS = $datos['VOTOS_CANDIDATOS'];		
	
	
 if($VOTOS_CANDIDATOS==""){			
				    $VOTOS_BLANCO = 0;
					 $VOTOS_NULOS = 0;
					 $VOTOS_NO_MARCADOS =0;
					 $SUFRAGANTES =0;
					 $OBSERVACIONES = "";
					  $VOTOS_CANDIDATOS = "0,0,0,0,0,0";
				}	?>					 
                             
						      <div class="control-group">
                              <label class="control-label">Total Sufragantes<span class="required">*</span></label>
                              <div class="controls">
							               <input id="sufragantes" name="sufragantes" type="text"  value="<?php echo $SUFRAGANTES?>" class="required number" <?php if($bloquar!=""){ ?>  disabled <?php  } ?>/>
								                                           
                              </div>
                           </div>
                           <div class="control-group">
                           
                              <div class="controls">
							   </div> </div>  
							               <?php 
				$sql="SELECT ID, concat(NOMBRES,' ',APELLIDOS) AS NOMBRES, NTARJETON from candidato where MUNICIPIO=".$_SESSION["idmunicipio"]." order by NTARJETON";
				$DBGestion->ConsultaArray($sql);				
				$candidatos=$DBGestion->datos;	
				

				  $VOTOS_CANDIDATOS=explode(",",$VOTOS_CANDIDATOS);
				  //var_dump($VOTOS_CANDIDATOS);exit;
				  $total=0;
						foreach ($candidatos as $datos2){
							 $id = $datos2['ID'];
							 $orden= $datos2['NTARJETON'];
							 $nombre = $datos2['NOMBRES'];					  			 
						
		?> <div class="control-group">
		                     <div class="controls">
				    <label class="control-label"><?php echo $nombre?><span class="required">*</span></label>
                                 <input id="voto_<?php echo $orden?>" value="<?php echo $VOTOS_CANDIDATOS[($orden-1)]?>" name="voto_<?php echo $orden?>" type="text"  class="required number" onKeyup="cantidad()" <?php if($bloquar!=""){ ?>  disabled <?php  } ?>/>
								 
                              </div>
                           </div>    
						   	<?php $total=$total+$VOTOS_CANDIDATOS[$orden-1];
							} ?>
							  <div class="control-group">
                              <label class="control-label">Votos en Blanco<span class="required">*</span></label>
                              <div class="controls">
							               <input id="votoblanco" name="votoblanco" value="<?php echo $VOTOS_BLANCO?>" type="text"  class="required number" onKeyup="cantidad()" <?php if($bloquar!=""){ ?>  disabled <?php  } ?>/>
								                                           
                              </div>
                           </div>
						   <div class="control-group">
                              <label class="control-label">Votos Nulos<span class="required">*</span></label>
                              <div class="controls">
							               <input id="votonulo" name="votonulo" type="text" value="<?php echo $VOTOS_NULOS?>"  class="required number" onKeyup="cantidad()" <?php if($bloquar!=""){ ?>  disabled <?php  } ?>/>
								                                           
                              </div>
                           </div>
						   <div class="control-group">
                              <label class="control-label">Votos no Marcados<span class="required">*</span></label>
                              <div class="controls">
							               <input id="votonomarcado" name="votonomarcado" type="text"   value="<?php echo $VOTOS_NO_MARCADOS?>" class="required number" onKeyup="cantidad()" <?php if($bloquar!=""){ ?>  disabled <?php  } ?>/>
								                                           
                              </div>
                           </div>
						    <div class="control-group">
                              <label class="control-label">Total Votos<span class="required">*</span></label>
                              <div class="controls">
							   <span id="capa_total" >
							               <input id="total" name="total" disabled value="<?php echo $VOTOS_NO_MARCADOS+$VOTOS_NULOS+$VOTOS_BLANCO+$total?>" type="text"  class="required number" disabled />
								                                           </span>
                              </div>
                           </div>
						    <div class="control-group">
                              <label class="control-label">Observaciones<span class="required">*</span></label>
                              <div class="controls">
									<TEXTAREA COLS=5 ROWS=2 NAME="Observaciones" value="<?php echo $OBSERVACIONES?>" <?php if($bloquar!=""){ ?>   <?php  } ?>></TEXTAREA> 
								                                           
                              </div>
                           </div>
                           <div class="form-actions">		
						   
								<button type="submit" class="btn green" onclick="return guardar()" <?php if($bloquar!=""){ ?> disabled  <?php  } ?>>Guardar</button>
                            
                            
                           </div>
<?php } ?>