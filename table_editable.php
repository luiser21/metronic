<?php
header('Content-Type: text/html; charset=ISO-8859-1'); 
	session_start();
    include_once "includes/GestionBD.new.class.php";
	$DBGestion = new GestionBD('AGENDAMIENTO');	
	// Si la sesion no est? activa y/o autenticada ingresa a este paso
	//imprimir($_SESSION);
	if (!isset($_SESSION["active"]) == 1)
	{
		header("location:logout.php");
	}
	// Si la sesion est? activa y autenticada ingresa a este paso
	else
	{
		// toma las variables de sesion y de edicion de contenidos		
		$usuario = $_SESSION["username"];
		$permiso = $_SESSION["permiso"];
		$consulta=$_SESSION["consulta"];
		$nombre = $_SESSION["nombre"];
		if($consulta==1){
			$nombre="Usuario de Consulta";
		}
		
	}
?>
<?php 

$add = (isset($_GET['add']) ? $_GET['add'] : 0); ;


if($add == 1){

    @$puesto=(isset($_POST['puestos']) ? $_POST['puestos'] : 0);
    @$voto_1=(isset($_POST['voto_1']) ? $_POST['voto_1'] : 0);
	@$voto_2=(isset($_POST['voto_2']) ? $_POST['voto_2'] : 0);
	@$voto_3=(isset($_POST['voto_3']) ? $_POST['voto_3'] : 0);
	@$voto_4=(isset($_POST['voto_4']) ? $_POST['voto_4'] : 0);
	@$voto_5=(isset($_POST['voto_5']) ? $_POST['voto_5'] : 0);
	@$voto_6=(isset($_POST['voto_6']) ? $_POST['voto_6'] : 0);
    @$mesas=(isset($_POST['mesas']) ? $_POST['mesas'] : 0);
	  @$sufragantes=(isset($_POST['sufragantes']) ? $_POST['sufragantes'] : 0);
	  	  @$votoblanco=(isset($_POST['votoblanco']) ? $_POST['votoblanco'] : 0);
		  	  @$votonulo=(isset($_POST['votonulo']) ? $_POST['votonulo'] : 0);
			  	  @$votonomarcado=(isset($_POST['votonomarcado']) ? $_POST['votonomarcado'] : 0);
	 @$Observaciones=(isset($_POST['Observaciones']) ? $_POST['Observaciones'] : 0);
	$puestoreg=1;
	
	if($puestoreg=='1'){
		
			$sql="SELECT ID from candidato where NTARJETON in (1,2,3,4,5,6) and MUNICIPIO=".$_SESSION["idmunicipio"]." order by NTARJETON";
			$DBGestion->ConsultaArray($sql);				
			$candidatos=$DBGestion->datos;	
			$id="0";
			 foreach ($candidatos as $datos2){
				 $id =$id.','. $datos2['ID'];
				 
			}	
			 $sql="UPDATE mesas set VOTOS_CANDIDATOS='".$voto_1.','.$voto_2.','.$voto_3.','.$voto_4.','.$voto_5.','.$voto_6."',
						CANDIDATOS='".$id."',VOTOS_BLANCO=".$votoblanco.",VOTOS_NULOS=".$votonulo.",VOTOS_NO_MARCADOS=".$votonomarcado.",
						SUFRAGANTES=".$sufragantes.",OBSERVACIONES='".$Observaciones."'
						WHERE ID=".$mesas." AND IDPUESTO=".$puesto;										
			$DBGestion->Consulta($sql);	
				
		
		
	 ?>
       	 <script language="javascript">
	       	 alert("Se ingreso el voto exitosamente"); 
	       	 window.location="table_editable.php";
       	 </script>
	   <?php	
	}else{
		 ?>
       	 <script language="javascript">
	       	alert("Hubo un Problema); 
	       	window.location="table_editable.php";
       	 </script>
	   <?php
	}
}
?>
<script type="text/javascript">

function mesa(){
	var pagina= "Ajax_mesa_votacion.php";
	var capa = "capa_mesas";
	var puesto = document.getElementById('puestos').value;
	var valores = 'puesto=' + puesto + '&' + Math.random();
	if(puesto!=''){ 			
	    FAjax (pagina,capa,valores,'POST',true)     	 
	}
}
function cantidad(){
	var pagina= "Ajax_total.php";
	var capa = "capa_total";
	var puesto = $("#voto_1").val();
	var puesto2= $("#voto_2").val();
	var puesto3= $("#voto_3").val();
	var puesto4= $("#voto_4").val();
	var puesto5= $("#voto_5").val();
	var puesto6= $("#voto_6").val();
	var puesto7= $("#votonomarcado").val();
	var puesto8= $("#votoblanco").val();
	var puesto9= $("#votonulo").val();
	var total=parseFloat(puesto)+parseFloat(puesto2)+parseFloat(puesto3)+parseFloat(puesto4)+parseFloat(puesto5)+parseFloat(puesto6)+parseFloat(puesto7)+parseFloat(puesto8)+parseFloat(puesto9);
	var valores = 'puesto=' +total+ '&' + Math.random();
				
	    FAjax (pagina,capa,valores,'POST',true)     	 
	
}
function guardar(){

	var puesto = $("#voto_1").val();
	var puesto2= $("#voto_2").val();
	var puesto3= $("#voto_3").val();
	var puesto4= $("#voto_4").val();
	var puesto5= $("#voto_5").val();
	var puesto6= $("#voto_6").val();
	var puesto7= $("#votonomarcado").val();
	var puesto8= $("#votoblanco").val();
	var puesto9= $("#votonulo").val();
	var total=parseFloat(puesto)+parseFloat(puesto2)+parseFloat(puesto3)+parseFloat(puesto4)+parseFloat(puesto5)+parseFloat(puesto6)+parseFloat(puesto7)+parseFloat(puesto8)+parseFloat(puesto9);

	var sufragante = parseFloat($("#sufragantes").val());
	
	if(total!=sufragante && sufragante!=0){
		alert("Se encontro diferencia entre el total de Votos y Sufragantes  "+ (sufragante-total));		
		return false;
	}
	if(sufragante==0){
		alert("Sufragantes no puede ser cero");		
		return false;
	}else{
		return true;
	}
	
}

</script>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
   <meta charset="utf-8" />
   <title>SIGE</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport" />
   <meta content="" name="description" />
   <meta content="" name="author" />
   <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
   <link href="assets/css/metro.css" rel="stylesheet" />
   <link href="assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
   <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
   <link href="assets/css/style.css" rel="stylesheet" />
   <link href="assets/css/style_responsive.css" rel="stylesheet" />
   <link href="assets/css/style_default.css" rel="stylesheet" id="style_color" />
   <link rel="stylesheet" type="text/css" href="assets/gritter/css/jquery.gritter.css" />
   <link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.default.css" />
   <link rel="stylesheet" type="text/css" href="assets/chosen-bootstrap/chosen/chosen.css" />
   <link rel="stylesheet" type="text/css" href="assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
   <link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css" />
   <link rel="stylesheet" type="text/css" href="assets/bootstrap-timepicker/compiled/timepicker.css" />
   <link rel="stylesheet" type="text/css" href="assets/bootstrap-colorpicker/css/colorpicker.css" />
   <link rel="stylesheet" href="assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css" />
   <link rel="stylesheet" href="assets/data-tables/DT_bootstrap.css" />
   <link rel="stylesheet" type="text/css" href="assets/bootstrap-daterangepicker/daterangepicker.css" />
   <link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.default.css" />
   <link rel="shortcut icon" href="images/favicon(2).ico" />
   <script type="text/javascript" src="js/FAjax.js"></script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
   <!-- BEGIN HEADER -->
   <div class="header navbar navbar-inverse navbar-fixed-top">
      <!-- BEGIN TOP NAVIGATION BAR -->
      <div class="navbar-inner">
         <div class="container-fluid">
            <!-- BEGIN LOGO -->
            <img src="images/logo2_movil.png" alt="logo"  width="159" height="108"/>
           
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
            <img src="assets/img/menu-toggler.png" alt="" />
            </a>          
            <!-- END RESPONSIVE MENU TOGGLER -->            
            <!-- BEGIN TOP NAVIGATION MENU -->              
            <ul class="nav pull-right">
               <!-- BEGIN NOTIFICATION DROPDOWN --><!-- END NOTIFICATION DROPDOWN -->
               <!-- BEGIN INBOX DROPDOWN --><!-- END INBOX DROPDOWN -->
               <!-- BEGIN TODO DROPDOWN --><!-- END TODO DROPDOWN -->
               <!-- BEGIN USER LOGIN DROPDOWN -->
               <li class="dropdown user">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
               <?php if($_SESSION['foto']!=""){?>
						<img src="<?php echo $_SESSION['foto']?>" width="24" height="38" style="border:1px solid #CCCCCC;">
			<?php }else{ ?>		
				<img src="fotos/images.jpg" width="24" height="38" style="border:1px solid #CCCCCC;">
			<?php } ?>	
                  <span class="username"><?php 
			if ($_SESSION["active"] == 1)
			{
				$sesion= "No se ha iniciado sesi&oacute;n";
			}
			else
			{
				$sesion= "<span class=\"style1\"><b> </b></span> ".$nombre; 
			}
		?><?php echo "   ".$sesion?></span>
                  <i class="icon-angle-down"></i>                  </a>
                  <ul class="dropdown-menu">
                    
                     <li><a href="logout.php"><i class="icon-key"></i>Cerrar Session</a></li>
                  </ul>
               </li>
               <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <!-- END TOP NAVIGATION MENU --> 
         </div>
      </div>
      <!-- END TOP NAVIGATION BAR -->
   </div>
   <!-- END HEADER -->
   <!-- BEGIN CONTAINER -->
   <div class="page-container row-fluid">
      <!-- BEGIN SIDEBAR -->
      <div class="page-sidebar nav-collapse collapse">
         <!-- BEGIN SIDEBAR MENU -->         
         <ul>
            <li>
               <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
               <div class="sidebar-toggler hidden-phone"></div>
               <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            </li> 
            
            <li class="active has-sub ">
               <a href="javascript:;">
               <i class="icon-table"></i> 
               <span class="title">Informes</span>
               <span class="selected"></span>
               <span class="arrow open"></span>
               </a>
               <ul class="sub">
                  <li class="active"><a href="table_editable.php">Registrar Resultados</a></li>
                  <li class="active"><a href="registrar_miembros.php">Registrar Simpatizante</a></li>
                 
               </ul>
            </li>           
            <li class="">
               <a href="logout.php">
               <i class="icon-user"></i> 
               <span class="title">Inicio</span>
               </a>
            </li>
         </ul>
         <!-- END SIDEBAR MENU -->
      </div>
      <!-- END SIDEBAR -->
      <!-- BEGIN PAGE -->  
      <div class="page-content">
         <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        
         <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
         <!-- BEGIN PAGE CONTAINER-->
         <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
<div class="row-fluid">
               <div class="span12">
                  <!-- BEGIN VALIDATION STATES-->
                  <!-- END VALIDATION STATES-->
</div>
           </div>
            <div class="row-fluid">
               <div class="span12">
                  <!-- BEGIN VALIDATION STATES-->
                  <div class="">
                     <div class="portlet-title">
                        <h4><i class="icon-reorder"></i><?php echo $_SESSION["tipocandidato"].'  '.$_SESSION["municipio"]?> 2015</h4>
                        
                     </div>
                     <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="table_editable.php?add=1" id="form_sample_1" class="form-horizontal" method="post">
                           <div class="alert alert-error hide">
                              <button class="close" data-dismiss="alert"></button>
                              Usted tiene algunos errores de forma. Por favor , consulte más abajo.
                           </div>
                           <div class="alert alert-success hide">
                              <button class="close" data-dismiss="alert"></button>
                             Su validación de formularios es un éxito!
                           </div>
						    <div class="control-group">
                              <label class="control-label">Puesto de Votacion<span class="required">*</span></label>
                              <div class="controls">
							                                    <?php 
				$sql="SELECT IDPUESTO,NOMBRE_PUESTO from puestos_votacion where IDMUNICIPIO=".$_SESSION["idmunicipio"];
				$DBGestion->ConsultaArray($sql);				
				$lideres=$DBGestion->datos;		
				$puestos=count($lideres);
				
		?>
                               <select class="span6 m-wrap; required" name="puestos" id="puestos" onclick="mesa()" >
						<option value="">Seleccione....</option>
                        <?php
						foreach ($lideres as $datos){
							 $id = $datos['IDPUESTO'];
							 $nombre = $datos['NOMBRE_PUESTO'];					  			 
							if($puestos==1){
				?>
			
						<option value="<?php echo $id?>" selected="selected"><?php echo $nombre?></option>
						<?php }else { ?>
						
							<option value="<?php echo $id?>" ><?php echo $nombre?></option>
						<?php } }?>
						
                                 </select>                               
                              </div>
                           </div>
						     <div class="control-group">
                              <label class="control-label">Mesas<span class="required">*</span></label>
                             <div class="controls">
                                   <span id="capa_mesas" >
<?php if($puestos==1){ 

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
				where IDPUESTO='".$id."' AND MESA=".$_SESSION["mesa"]." order by MESA";
	echo '<select class="span6 m-wrap; required" name="mesas" >';   

 
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
	echo '<option value="'.$id.'" selected="selected">'.$nombre.'</option>';
		
}
echo '</select>';
?>
		
	<?php }else { ?>
								   <select class="span6 m-wrap; required" name="mesas">
						<option value="">Seleccione Puesto de Votacion....</option> </span>
					 </select>     
	<?php }

 if($VOTOS_CANDIDATOS==""){			
				    $VOTOS_BLANCO = 0;
					 $VOTOS_NULOS = 0;
					 $VOTOS_NO_MARCADOS =0;
					 $SUFRAGANTES =0;
					 $OBSERVACIONES = "";
					  $VOTOS_CANDIDATOS = "0,0,0,0,0,0";
				}	?>					 
                              </div>
                           </div> 
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
                        </form>
                        <!-- END FORM-->
                     </div>
                  </div>
                  <!-- END VALIDATION STATES-->
               </div>
            </div>
            
            <!-- END PAGE CONTENT-->         
        </div>
         <!-- END PAGE CONTAINER-->
      </div>
      <!-- END PAGE -->  
   </div>
   <!-- END CONTAINER -->
   <!-- BEGIN FOOTER -->
   <!-- END FOOTER -->
   <!-- BEGIN JAVASCRIPTS -->
   <!-- Load javascripts at bottom, this will reduce page load time -->
<script src="assets/js/jquery-1.8.3.min.js"></script>    
   <script src="assets/breakpoints/breakpoints.js"></script>      
   <script src="assets/bootstrap/js/bootstrap.min.js"></script>
   <script src="assets/js/jquery.blockui.js"></script>
   <script src="assets/js/jquery.cookie.js"></script>
   <!-- ie8 fixes -->
   <!--[if lt IE 9]>
   <script src="assets/js/excanvas.js"></script>
   <script src="assets/js/respond.js"></script>
   <![endif]-->
   <script type="text/javascript" src="assets/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
   <script type="text/javascript" src="assets/uniform/jquery.uniform.min.js"></script>
   <script type="text/javascript" src="assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script> 
   <script type="text/javascript" src="assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
   <script type="text/javascript" src="assets/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js"></script>
   <script type="text/javascript" src="assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
   <script type="text/javascript" src="assets/bootstrap-daterangepicker/date.js"></script>
   <script type="text/javascript" src="assets/bootstrap-daterangepicker/daterangepicker.js"></script> 
   <script type="text/javascript" src="assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>  
   <script type="text/javascript" src="assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
   <script type="text/javascript" src="assets/jquery-validation/dist/jquery.validate.min.js"></script>
   <script type="text/javascript" src="assets/jquery-validation/dist/additional-methods.min.js"></script>
   <script src="assets/js/app.js"></script>     
   <script>
      jQuery(document).ready(function() {   
         // initiate layout and plugins
          App.setPage("form_validation");
        App.init();
      });
   </script>
   <!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>