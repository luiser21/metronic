<?php 
date_default_timezone_set('America/Bogota');
if(date('H')>16 && date('d')==25){
header("Location: table_editable.php");
} 
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
    @$mesas=(isset($_POST['mesas']) ? $_POST['mesas'] : 0);
    @$sufragantes=(isset($_POST['sufragantes']) ? $_POST['sufragantes'] : 0);	  	 
	$puestoreg=1;	
	if($puestoreg=='1'){
		
			
			$sql="SELECT MESA from mesas where ID=".$mesas;			
			$DBGestion->ConsultaArray($sql);				
			$mesa=$DBGestion->datos;				
			 foreach ($mesa as $datos2){
				 $mesa1 =$datos2['MESA'];				 
			}	
			 $sql="UPDATE boletines_departamentos set MOVILIZADOS='".$sufragantes."', META=1
				    WHERE candidato=".$_SESSION['idcandidato']." and encargado='".$_SESSION['usuarioasociado']."' and zona='MESA ".$mesa1."'";	
			
			$DBGestion->Consulta($sql);	
			 $sql="UPDATE boletines set MOVILIZADOS=MOVILIZADOS+'".$sufragantes."'
				    WHERE candidato=".$_SESSION['idcandidato']." and hora_real=".date('H');	
			
			$DBGestion->Consulta($sql);					
	 ?>
       	 <script language="javascript">
	       	 alert("Se ingreso los sufragantes exitosamente"); 
	       	 window.location="registrar_sufragantes.php";
       	 </script>
	   <?php	
	}else{
		 ?>
       	 <script language="javascript">
	       	alert("Hubo un Problema); 
	       	window.location="registrar_sufragantes.php";
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

function mesa_votos(){
	var pagina= "Ajax_mesa_votacion_sufragantes.php";
	var capa = "capa_mesas_votos";
	var puesto = document.getElementById('mesas').value;
	var valores = 'mesas=' + puesto + '&' + Math.random();
	FAjax (pagina,capa,valores,'POST',true)     	 
}

function guardar(){
	
	var sufragante = parseFloat($("#sufragantes").val());
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
   <style>
      /* Menu hover rojo para Informes y Simulador */
      .menu-red:hover { background-color: #c9302c !important; }
      .menu-red:hover > a { color: #fff !important; }
      .menu-red:hover .title { color: #fff !important; }
   </style>
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
				<a class="brand" href="index.html">
			<img src="images/logo_movil_original.png" alt="logo" width="100" height="68"/>
				</a>
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
             <? 
				  if($_SESSION['consulta']==1){
				  ?>
            <li class="has-sub ">
               <a href="javascript:;">
               <i class="icon-table"></i> 
               <span class="title">Informes</span>			   
					<span class="arrow "></span>
               </a>
               <ul class="sub">
                  <li class="active"><a href="registrar_miembros.php">Registrar Simpatizante</a></li>
                 
               </ul>
            </li>  
			 <? }?>	
			<li class="active has-sub">
					<a href="diad_electoral.php">
					<i class="icon-calendar"></i> 
					<span class="title">Dia Electoral</span>
					<span class="selected"></span>
					<span class="arrow open"></span>
					</a>
				</li>
			 <li class="menu-red">
               <a href="informe_testigos.php">
               <i class="icon-table"></i>
               <span class="title">Informes</span>
               </a>
            </li>
            <?php if($_SESSION['consulta']==0): ?>
            <li class="menu-red">
               <a href="simulador_testigos.php">
               <i class="icon-signal"></i>
               <span class="title">Simulador</span>
               </a>
            </li>
            <?php endif; ?>
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
								<i class="icon-calendar"></i>
								Dia Electoral
								<i class="icon-angle-right"></i>
							</li>

						</ul>
              
						<div id="dashboard">
					<!-- BEGIN DASHBOARD STATS -->
					<div class="row-fluid">
						<div class="span3 responsive" data-tablet="span6" data-desktop="span3">
							<div class="dashboard-stat blue">
								<div class="visual">
									<i class="icon-bar-chart"></i>
								</div>
								<div class="details">
									<div class="number">
										<?php 

$sql="SELECT sum(COMPROMISO) as COMPROMISO FROM compromisos_candidato WHERE USUARIO='".$_SESSION["usuarioasociado"]."'";
	
$DBGestion->ConsultaArray($sql);
$compromiso=$DBGestion->datos;	
echo number_format( $compromiso[0]['COMPROMISO'], 0, ',', ',')  ;	
										?>
									</div>
									<div class="desc">									
										Votos Compromiso
									</div>
								</div>
								<a class="more" href="#">
								Ver mas <i class="m-icon-swapright m-icon-white"></i>
								</a>						
							</div>
						</div>
						<div class="span3 responsive" data-tablet="span6" data-desktop="span3">
							<div class="dashboard-stat green">
								<div class="visual">
									<i class="icon-shopping-cart"></i>
								</div>
								<div class="details">
									<div class="number">
									<?php
$sql="SELECT sum(MOVILIZADOS) as MOVILIZADOS FROM boletines_departamentos WHERE CANDIDATO=223";	
$DBGestion->ConsultaArray($sql);
$movilizados=$DBGestion->datos;	
echo number_format( $movilizados[0]['MOVILIZADOS'], 0, ',', ',')  ;	

									?>
									</div>
									<div class="desc">Votos Movilizados</div>
								</div>
								<a class="more" href="#">
								Ver mas <i class="m-icon-swapright m-icon-white"></i>
								</a>						
							</div>
						</div>
						<div class="span3 responsive" data-tablet="span6  fix-offset" data-desktop="span3">
							<div class="dashboard-stat purple">
								<div class="visual">
									<i class="icon-globe"></i>
								</div>
								<div class="details">
									<div class="number"><?php
									
									echo '+'.number_format(($movilizados[0]['MOVILIZADOS']/$compromiso[0]['COMPROMISO'])*100, 2, ',', ',').'%';
									
									?></div>
									<div class="desc">% Cumplimiento</div>
								</div>
								<a class="more" href="#">
								Ver mas <i class="m-icon-swapright m-icon-white"></i>
								</a>						
							</div>
						</div>
						<div class="span3 responsive" data-tablet="span6" data-desktop="span3">
							<div class="dashboard-stat yellow">
								<div class="visual">
									<i class="icon-bar-chart"></i>
								</div>
								<div class="details">
									<div class="number">6,000</div>
									<div class="desc">Puestos Votacion</div>
								</div>
								<a class="more" href="#">
								Ver mas <i class="m-icon-swapright m-icon-white"></i>
								</a>						
							</div>
						</div>
					</div>
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