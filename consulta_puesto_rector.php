<?php
date_default_timezone_set('America/Bogota');

//if(date('H')>16 && date('d')==25){
header('Content-Type: text/html; charset=ISO-8859-1'); 
	session_start();
    include_once "includes/GestionBD.new.class.php";

	$DBGestion = new GestionBD('AGENDAMIENTO');	
	// Si la sesion no est? activa y/o autenticada ingresa a este paso
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

$add = (isset($_GET['add']) ? $_GET['add'] : 0); ;


if($add == 1){
	
    @$puesto=(isset($_POST['puesto']) ? $_POST['puesto'] : 0);  
    @$mesa=(isset($_POST['mesas']) ? $_POST['mesas'] : 0);	  	 
	@$totalmesa=(isset($_POST['totalmesa']) ? $_POST['totalmesa'] : 0);  
    @$votospartido=(isset($_POST['votospartido']) ? $_POST['votospartido'] : 0);	  	 
	@$votoscandidado=(isset($_POST['votoscandidado']) ? $_POST['votoscandidado'] : 0);  
    @$votosnulos=(isset($_POST['votosnulos']) ? $_POST['votosnulos'] : 0);	  	 
	@$votosnomarcados=(isset($_POST['votosnomarcados']) ? $_POST['votosnomarcados'] : 0);  
    @$votosenblanco=(isset($_POST['votosenblanco']) ? $_POST['votosenblanco'] : 0);	  	 
	@$observacion=(isset($_POST['observacion']) ? $_POST['observacion'] : 0);   
@$votoscandidato1=(isset($_POST['votoscandidato1']) ? $_POST['votoscandidato1'] : 0);  
@$votoscandidato2=(isset($_POST['votoscandidato2']) ? $_POST['votoscandidato2'] : 0);  
@$votoscandidato3=(isset($_POST['votoscandidato3']) ? $_POST['votoscandidato3'] : 0);  
@$votoscandidato4=(isset($_POST['votoscandidato4']) ? $_POST['votoscandidato4'] : 0);  
@$votoscandidato5=(isset($_POST['votoscandidato5']) ? $_POST['votoscandidato5'] : 0);  
@$votoscandidato6=(isset($_POST['votoscandidato6']) ? $_POST['votoscandidato6'] : 0);  
@$votoscandidato7=(isset($_POST['votoscandidato7']) ? $_POST['votoscandidato7'] : 0);  
@$votoscandidato8=(isset($_POST['votoscandidato8']) ? $_POST['votoscandidato8'] : 0);  
	try{		
		$sql="UPDATE mesas_testigo set 
				TOTALMESA=".$totalmesa.",
			
				VOTOS_BLANCO=".$votosenblanco.",
				VOTOS_NULOS=".$votosnulos.",
				VOTOS_NO_MARCADOS=".$votosnomarcados.",
				OBSERVACIONES='".$observacion."',
				votoscandidato1=".$votoscandidato1.",
				votoscandidato2=".$votoscandidato2.",
				votoscandidato3=".$votoscandidato3.",
				votoscandidato4=".$votoscandidato4.",
				votoscandidato5=".$votoscandidato5.",
				votoscandidato6=".$votoscandidato6.",
				votoscandidato7=".$votoscandidato7.",
				votoscandidato8=".$votoscandidato8."
				WHERE 				
				IDCANDIDATO=".$_SESSION['idcandidato']." and IDPUESTO=".$puesto." and ID=".$mesa." and IDTESTIGO='".$_SESSION['usuarioasociado']."'";		
		$DBGestion->Consulta($sql);	
		//exit;			
	 ?>
       	 <script language="javascript">
	       	 alert("Se ingreso los votos exitosamente"); 
	       	 window.location="consulta_puesto_rector.php";
       	 </script>
	   <?php	
	}catch(Exception $e){
		
		 ?>
       	 <script language="javascript">
	       	alert("Hubo un Problema <?  echo 'Excepción capturada: '.$e->getMessage()."\n"; ?>"); 
	       	window.location="consulta_puesto_rector.php";
       	 </script>
	   <?php
	}
}
?>
<script type="text/javascript">

function mesa_votos(){
	var pagina= "Ajax_mesa_consultar_puesto.php";
	var capa = "capa_mesas_votos";
	var documento = document.getElementById('documento').value;
	var valores = 'documento=' + documento + '&' + Math.random();
	FAjax (pagina,capa,valores,'POST',true)     	 
}

function guardar(){
	
	var sufragante = parseFloat($("#votospatido").val());
	if(sufragante<0){
		alert("Votos no puede ser menor a cero");		
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
		?><?php echo "   ".$sesion." (".$_SESSION['usuarioasociado'].") "?></span>
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
                  <li class="active"><a href="consulta_puesto_rector.php">Consultar Puesto Votacion</a></li>
                 
               </ul>
            </li>  
			<? if($_SESSION['consulta']==0){?>
			<li class="">
					<a href="diad_electoral.php">
					<i class="icon-calendar"></i> 
					<span class="title">Dia Electoral</span>
					</a>
				</li>
			<? }?>				
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
				<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								Consultar Puesto Votacion
								<i class="icon-angle-right"></i>
							</li>
							Consulte aquí su posible lugar de votación o si esta habilitado para Votar</li>
							<li class="pull-right no-text-shadow">
								
							</li>
						</ul>
               <div class="span12">
                  <!-- BEGIN VALIDATION STATES-->
                  <div class="">
                     <div class="portlet-title">
                        <h4><i class="icon-reorder"></i><?php echo $_SESSION["tipocandidato"].' ETITC N° '.$_SESSION["ntarjeton"].' PERIODO '.$_SESSION["periodo"]?></h4>
                        
                     </div>
                     <div class="portlet-body form">
                        <!-- BEGIN FORM-->
						
                           <div class="alert alert-error hide">
                              <button class="close" data-dismiss="alert"></button>
                              Usted tiene algunos errores de forma. Por favor , consulte más abajo.
                           </div>
                           <div class="alert alert-success hide">
                              <button class="close" data-dismiss="alert"></button>
                             Su validación de formularios es un éxito!
                           </div>
						    <div class="control-group">
                              <label class="control-label">Número de Documento<span class="required">*</span></label>
                              <div class="controls">
							       <input id="documento" name="documento" type="tel"  value=""  class="required number" />  
  
                                 </select>                               
                              </div>
                           </div>		                         
						    <div class="form-actions">		
							<button type="submit" class="btn green" onclick="mesa_votos()" >Consultar</button>
						</div>
						   <span id="capa_mesas_votos">							
							</span>	
                                               
                           
                      
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