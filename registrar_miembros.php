<?php
header('Content-Type: text/html; charset=ISO-8859-1'); 
	session_start();
    include_once "includes/GestionBD.new.class.php";
	$DBGestion = new GestionBD('AGENDAMIENTO');	
	// Si la sesion no est? activa y/o autenticada ingresa a este paso
	if (!isset($_SESSION["active"]) == 1)
	{
		header("location:index.html");
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
include_once "example_doc_miembros_add.php";
$add = (isset($_GET['add']) ? $_GET['add'] : 0); ;


if($add == 1){

    @$nombre=(isset($_POST['nombre']) ? $_POST['nombre'] : 'NULL');
    @$cedula=(isset($_POST['cedula']) ? $_POST['cedula'] : 'NULL');
    @$celular=(isset($_POST['celular']) ? $_POST['celular'] : 'NULL');
    @$email=(isset($_POST['correo']) ? $_POST['correo'] : 'NULL'); 
	 @$idlider=(isset($_POST['lider']) ? $_POST['lider'] : 'NULL');
	 @$ocupacion=(isset($_POST['ocupacion']) ? $_POST['ocupacion'] : 'NULL');
		
	$puestoreg=ingresar_manual_miembros($cedula,$nombre,$celular,$email,$idlider,$ocupacion);	
	
	if($puestoreg=='1'){
	 ?>
       	 <script language="javascript">
	       	 alert("Se ingreso el Simpatizante exitosamente"); 
	       	 window.location="registrar_miembros.php";
       	 </script>
	   <?php	
	}else{
		 ?>
       	 <script language="javascript">
	       	alert("Hubo un Problema:  <?php echo $puestoreg?> \n Se crea Registro en Historico"); 
	       	window.location="registrar_miembros.php";
       	 </script>
	   <?php
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
				   <li class="active"><a href="registrar_sufragantes.php">Registrar Sufragantes</a></li>
                 
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
                        <h4><i class="icon-reorder"></i>Registrar Simpatizante</h4>
                        
                     </div>
                     <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="registrar_miembros.php?add=1" id="form_sample_1" class="form-horizontal" method="post">
                           <div class="alert alert-error hide">
                              <button class="close" data-dismiss="alert"></button>
                              Usted tiene algunos errores de forma. Por favor , consulte más abajo.
                           </div>
                           <div class="alert alert-success hide">
                              <button class="close" data-dismiss="alert"></button>
                             Su validación de formularios es un éxito!
                           </div>
						    <div class="control-group">
                              <label class="control-label">Lider<span class="required">*</span></label>
                              <div class="controls">
							                                    <?php 
				$sql="SELECT ID, CONCAT(NOMBRES,' ',APELLIDOS) AS NOMBRES FROM lideres WHERE IDCANDIDATO='".$_SESSION["idcandidato"]."'";
				$DBGestion->ConsultaArray($sql);
				
				$lideres=$DBGestion->datos;
		
		?>
                               <select class="span6 m-wrap; required" name="lider">

						<option value="">Seleccione....</option>
                        <?php
						foreach ($lideres as $datos){
							 $id = $datos['ID'];
							 $nombre = $datos['NOMBRES'];
							 
							  			 
				?>
						<option value="<?php echo $id?>"><?php echo $nombre?></option>
						<?php } ?>
                                 </select>                               
                              </div>
                           </div>
						     <div class="control-group">
                              <label class="control-label">Cedula<span class="required">*</span></label>
                             <div class="controls">
                                 <input name="cedula" type="text" class="span6 m-wrap; required number"/>
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Nombres y Apellidos<span class="required">*</span></label>
                              <div class="controls">
                                 <input type="text" name="nombre" data-required="1" class="span6 m-wrap; required"/>
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Email</label>
                             <div class="controls">
                                 <input name="correo" type="text" class="span6 m-wrap; email"/>
                              </div>
                           </div>
                         
                         
                           <div class="control-group">
                              <label class="control-label">Telefono / Celular</label>
                              <div class="controls">
                                 <input name="celular" type="text" class="span6 m-wrap; number"/>
                              </div>
                           </div>
                          
                           <div class="control-group">
                              <label class="control-label">Ocupacion</label>
                              <div class="controls">
                                 <input name="ocupacion" type="text" class="span6 m-wrap"/>
                         
                              </div>
                           </div>
                          
                           <div class="form-actions">		
						   
								<button type="submit" class="btn green">Guardar</button>
                            
                            
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