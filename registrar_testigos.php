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
	try{		
		$sql="UPDATE mesas_testigo set 
				TOTALMESA=".$totalmesa.",
				VOTOPARTIDO=".$votospartido.",
				VOTOS_BLANCO=".$votosenblanco.",
				VOTOS_NULOS=".$votosnulos.",
				VOTOS_NO_MARCADOS=".$votosnomarcados.",
				OBSERVACIONES='".$observacion."',
				VOTOS_CANDIDATOS=".$votoscandidado."
				WHERE 				
				IDCANDIDATO=".$_SESSION['idcandidato']." and IDPUESTO=".$puesto." and ID=".$mesa." and IDTESTIGO='".$_SESSION['usuarioasociado']."'";		
		$DBGestion->Consulta($sql);	
		//exit;			
	 ?>
       	 <script language="javascript">
	       	 alert("Se ingreso los votos exitosamente"); 
	       	 window.location="registrar_testigos.php";
       	 </script>
	   <?php	
	}catch(Exception $e){
		
		 ?>
       	 <script language="javascript">
	       	alert("Hubo un Problema <?  echo 'Excepción capturada: '.$e->getMessage()."\n"; ?>"); 
	       	window.location="registrar_miembros.php";
       	 </script>
	   <?php
	}
}
?>
<script type="text/javascript">

function mesa_votos(){
	var pagina= "Ajax_mesa_votacion_testigos.php";
	var capa = "capa_mesas_votos";
	var puesto = document.getElementById('puesto').value;
	var mesas = document.getElementById('mesas').value;
	var valores = 'puesto=' + puesto + '&mesas='+mesas+'&' + Math.random();
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
                  <li class="active"><a href="registrar_testigos.php">Registrar Mesas Testigo Electoral</a></li>
                 
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
								Registrar
								<i class="icon-angle-right"></i>
							</li>
							Mesas Escrutadas</li>
							<li class="pull-right no-text-shadow">
								
							</li>
						</ul>
               <div class="span12">
                  <!-- BEGIN VALIDATION STATES-->
                  <div class="">
                     <div class="portlet-title">
                        <h4><i class="icon-reorder"></i><?php echo $_SESSION["tipocandidato"].'  '.$_SESSION["ntarjeton"].'  '.$_SESSION["municipio"].'  '.$_SESSION["periodo"]?></h4>
                        
                     </div>
                     <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="registrar_testigos.php?add=1" id="form_sample_1" class="form-horizontal" method="post">
                           <div class="alert alert-error hide">
                              <button class="close" data-dismiss="alert"></button>
                              Usted tiene algunos errores de forma. Por favor , consulte más abajo.
                           </div>
                           <div class="alert alert-success hide">
                              <button class="close" data-dismiss="alert"></button>
                             Su validación de formularios es un éxito!
                           </div>
						    <div class="control-group">
                              <label class="control-label">Departamentos<span class="required">*</span></label>
                              <div class="controls">
							                                    <?php 
				$sql="SELECT DISTINCT DEP.IDDEPARTAMENTO, DEP.NOMBRE FROM 
puestos_votacion PV
INNER JOIN municipios MUN ON MUN.ID=PV.IDMUNICIPIO
INNER JOIN departamentos DEP ON DEP.IDDEPARTAMENTO=MUN.IDDEPARTAMENTO
WHERE PV.IDPUESTO IN (".$_SESSION["PUSTOSASIGNADOS"].")";
				$DBGestion->ConsultaArray($sql);
				
				$departamentos=$DBGestion->datos;
				
		
		?>
                               <select class="span6 m-wrap; required" name="departamentos" id="departamentos">
						<? if(count($departamentos)>1){ ?>
						<option value="">Seleccione....</option>
						<? } ?>
                        <?php
						foreach ($departamentos as $datos){
							 $id = $datos['IDDEPARTAMENTO'];
							 $nombre = $datos['NOMBRE'];
							 
							  			 
				?>
						<option value="<?php echo $id?>"><?php echo $nombre?></option>
						<?php } ?>
                                 </select>                               
                              </div>
                           </div>
						     <div class="control-group">
                              <label class="control-label">Municipios<span class="required">*</span></label>
                             <div class="controls">
							      <?php 
				$sql="SELECT DISTINCT MUN.ID AS IDMUNICIPIO, MUN.NOMBRE FROM 
puestos_votacion PV
INNER JOIN municipios MUN ON MUN.ID=PV.IDMUNICIPIO
INNER JOIN departamentos DEP ON DEP.IDDEPARTAMENTO=MUN.IDDEPARTAMENTO
WHERE PV.IDPUESTO IN (".$_SESSION["PUSTOSASIGNADOS"].")";
				$DBGestion->ConsultaArray($sql);
				
				$municipios=$DBGestion->datos;
				
		
		?>
		
                               <select class="span6 m-wrap; required" name="municipios" id="municipios">
						<? if(count($municipios)>1){ ?>
						<option value="">Seleccione....</option>
						<? } ?> 
                        <?php
						foreach ($municipios as $datos){
							 $id = $datos['IDMUNICIPIO'];
							 $nombre = $datos['NOMBRE'];
							 
							  			 
				?>
						<option value="<?php echo $id?>"><?php echo $nombre?></option>
						<?php } ?>
                                 </select>     
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Puestos de Votaci&oacute;n<span class="required">*</span></label>
                              <div class="controls"> 
                               <?php 
				$sql="SELECT DISTINCT PV.IDPUESTO,PV.NOMBRE_PUESTO FROM 
puestos_votacion PV
INNER JOIN municipios MUN ON MUN.ID=PV.IDMUNICIPIO
INNER JOIN departamentos DEP ON DEP.IDDEPARTAMENTO=MUN.IDDEPARTAMENTO
WHERE PV.IDPUESTO IN (".$_SESSION["PUSTOSASIGNADOS"].")";
				$DBGestion->ConsultaArray($sql);
				
				$puestos=$DBGestion->datos;
				
		
		?>
		
                               <select class="span6 m-wrap; required" name="puesto" id="puesto" ">
					
						
                        <?php
						foreach ($puestos as $datos){
							 $id = $datos['IDPUESTO'];
							 $nombre = $datos['NOMBRE_PUESTO'];
							 
							  			 
				?>
						<option value="<?php echo $id?>"><?php echo $nombre?></option>
						<?php } ?>
                                 </select>     
                              </div>
                           </div>
						    <div class="control-group">
                              <label class="control-label">Mesas de Votaci&oacute;n<span class="required">*</span></label>
                              <div class="controls"> 
                               <?php 
				$sql="SELECT DISTINCT MT.ID,MT.MESA FROM 
mesas_testigo MT
INNER JOIN puestos_votacion PV ON PV.IDPUESTO=MT.IDPUESTO
INNER JOIN municipios MUN ON MUN.ID=PV.IDMUNICIPIO
INNER JOIN departamentos DEP ON DEP.IDDEPARTAMENTO=MUN.IDDEPARTAMENTO
WHERE PV.IDPUESTO IN (".$_SESSION["PUSTOSASIGNADOS"].") AND MT.MESA IN (".$_SESSION["MESASASIGNADAS"].")";
				$DBGestion->ConsultaArray($sql);
				
				$puestos=$DBGestion->datos;
				
		
		?>
		
                               <select class="span6 m-wrap; required" name="mesas" id="mesas" onclick="mesa_votos()">
					<option value="">Seleccione....</option>
						
                        <?php
						foreach ($puestos as $datos){
							 $id = $datos['ID'];
							 $nombre = $datos['MESA'];
							 
							  			 
				?>
						<option value="<?php echo $id?>"><?php echo $nombre?></option>
						<?php } ?>
                                 </select>     
                              </div>
                           </div>
						   <span id="capa_mesas_votos">							
							</span>	
                                               
                           
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