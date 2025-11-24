<?php 
//date_default_timezone_set('America/Bogota');

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

			<li class="has-sub">
				<a href="diad_electoral.php">
				<i class="icon-calendar"></i>
				<span class="title">Dia Electoral</span>
				<span class="arrow"></span>
				</a>
			</li>
			<li class="active menu-red">
               <a href="informe_testigos.php">
               <i class="icon-table"></i>
               <span class="title">Informes</span>
               <span class="selected"></span>
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
                  <?php include 'monitor_testigos_content.php'; ?>
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
	<script type="text/javascript" src="assets/uniform/jquery.uniform.min.js"></script>
	<script type="text/javascript" src="assets/data-tables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="assets/data-tables/DT_bootstrap.js"></script>
	<script src="assets/js/app.js"></script>		
	<script>
		jQuery(document).ready(function() {			
			// initiate layout and plugins
			App.setPage("table_managed");
			App.init();
		});
		
	</script>
   <!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>