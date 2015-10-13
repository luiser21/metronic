<?php 
header('Content-Type: text/html; charset=ISO-8859-1'); 
include_once "includes/GestionBD.new.class.php";
$DBGestion = new GestionBD('AGENDAMIENTO');	
	session_start();
$puesto= (!empty($_POST['puesto']))? $_POST['puesto']: 0;

?>
  <input id="total" name="total" type="text"  value="<?php echo $puesto?>" class="required number" disabled />