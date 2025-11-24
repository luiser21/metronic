<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1;

//error_reporting(E_ALL);
session_start();
header('Content-Type: text/html; charset=UTF-8');
// Ajusta ruta a tu clase de BD si es diferente
 include_once "includes/GestionBD.new.class.php";

$DBGestion = new GestionBD('AGENDAMIENTO'); // ajusta si tu constructor usa otro nombre de BD

// --- PRG: leer resultado guardado en sesión (si viene tras redirect) ---
$created_summary = null;
if (isset($_GET['sim_created']) && $_GET['sim_created'] == '1' && isset($_SESSION['sim_created'])) {
    $created_summary = $_SESSION['sim_created'];
    unset($_SESSION['sim_created']);
}

// manejo de formulario: soporta 'preview' (muestra asignaciones) y 'generar' (inserta)
$mensaje = '';
$previewAssignments = [];
$confirm_payload = '';

// valores por defecto para que el formulario conserve lo que el usuario escribió
$cantidad = 10;
$prefix = 'testigo';
$passwordPlain = '123456';
$departamento = 35;
$municipio = 1145;
$cantidad_zonas = 1;
$cantidad_puestos = 10;
$cantidad_mesas_por_puesto = 5;
$usarPuestosAleatorios = true;
$usarMesasAleatorias = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    // modo: aleatorio (default) o seleccionar (zona -> puestos)
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'aleatorio';

    // actualizar variables con lo enviado para que persistan en el formulario
    $cantidad = max(1, intval($_POST['cantidad']));
    $departamento = intval($_POST['departamento']);
    $municipio = intval($_POST['municipio']);
    $prefix = isset($_POST['prefix']) ? trim($_POST['prefix']) : $prefix;
    $usarPuestosAleatorios = isset($_POST['random_puestos']) ? true : false;
    $usarMesasAleatorias = isset($_POST['random_mesas']) ? true : false;
    $passwordPlain = isset($_POST['password']) ? $_POST['password'] : $passwordPlain;

    // nuevos controles: cantidad de zonas, puestos totales, mesas por puesto (pool)
    $cantidad_zonas = max(1, intval($_POST['cantidad_zonas'] ?? $cantidad_zonas));
    $cantidad_puestos = max(1, intval($_POST['cantidad_puestos'] ?? $cantidad_puestos));
    $cantidad_mesas_por_puesto = max(1, intval($_POST['cantidad_mesas'] ?? $cantidad_mesas_por_puesto));

    // obtener zonas disponibles en el municipio
    $sqlZones = "SELECT DISTINCT PV.ZONA FROM puestos_votacion PV WHERE PV.IDMUNICIPIO = " . intval($municipio) . " AND PV.ZONA IS NOT NULL AND PV.ZONA<>''";
    $DBGestion->ConsultaArray($sqlZones);
    $zonasAll = array_column($DBGestion->datos, 'ZONA');

    // si el modo es "seleccionar" tomamos los puestos seleccionados desde POST
    $selected_puestos_post = [];
    if ($mode === 'seleccionar') {
        // esperar arreglo de puestos seleccionados
        if (!empty($_POST['puestos_selected']) && is_array($_POST['puestos_selected'])) {
            $selected_puestos_post = array_map('intval', $_POST['puestos_selected']);
        } else {
            // si no envía puestos, y envía zona, podemos cargar todos los puestos de la zona
            if (!empty($_POST['zona_selected'])) {
                $zona_sel = addslashes($_POST['zona_selected']);
                $DBGestion->ConsultaArray("SELECT IDPUESTO FROM puestos_votacion WHERE IDMUNICIPIO = " . intval($municipio) . " AND ZONA = '" . $zona_sel . "'");
                $selected_puestos_post = array_column($DBGestion->datos, 'IDPUESTO');
            }
        }
    }

    // elegir zonas (aleatorias) limitadas por cantidad_zonas (solo para modo aleatorio)
    if (count($zonasAll) === 0) {
        $mensaje = "No hay zonas definidas para el municipio seleccionado.";
        $zonasSelected = [];
    } else {
        if ($mode === 'aleatorio') {
            shuffle($zonasAll);
            $zonasSelected = array_slice($zonasAll, 0, min($cantidad_zonas, count($zonasAll)));
        } else {
            // modo seleccionar: si envió zona_selected usarla; si envió puestos, derivar zonasSelected desde puestos
            if (!empty($_POST['zona_selected'])) {
                $zonasSelected = [$_POST['zona_selected']];
            } elseif (!empty($selected_puestos_post)) {
                // obtener zonas para los puestos seleccionados
                $inP = implode(',', array_map('intval',$selected_puestos_post));
                $DBGestion->ConsultaArray("SELECT DISTINCT ZONA FROM puestos_votacion WHERE IDPUESTO IN ($inP)");
                $zonasSelected = array_column($DBGestion->datos,'ZONA');
            } else {
                $zonasSelected = [];
            }
        }
    }

    // obtener puestos pertenecientes a zonas seleccionadas dentro del municipio
    $puestos = [];
    if (!empty($zonasSelected)) {
        $inList = "'" . implode("','", array_map(function($z){ return addslashes($z); }, $zonasSelected)) . "'";
        // incluir nombre_puesto para mostrar etiqueta en la vista previa
        $sqlP = "SELECT PV.IDPUESTO, PV.ZONA, PV.nombre_puesto AS PUESTO_NOMBRE FROM puestos_votacion PV WHERE PV.IDMUNICIPIO = " . intval($municipio) . " AND PV.ZONA IN ($inList)";
        $DBGestion->ConsultaArray($sqlP);
        $puestos = $DBGestion->datos;
    }

    if (count($puestos) === 0) {
        $mensaje = $mensaje ? $mensaje : "No hay puestos en las zonas seleccionadas.";
    }

    // seleccionar puestos limitados por cantidad_puestos (modo aleatorio) o usar los seleccionados (modo seleccionar)
    $puestosIdsAll = array_column($puestos, 'IDPUESTO');
    if ($mode === 'seleccionar' && !empty($selected_puestos_post)) {
        // usar intersección entre lo enviado y los puestos disponibles en las zonas (si hay)
        $puestosIds = array_values(array_intersect($selected_puestos_post, $puestosIdsAll));
        if (count($puestosIds) === 0) {
            // si no hay intersección, tomar los puestos enviados directamente
            $puestosIds = $selected_puestos_post;
        }
    } else {
        if (count($puestosIdsAll) > $cantidad_puestos) {
            shuffle($puestosIdsAll);
            $puestosIds = array_slice($puestosIdsAll, 0, $cantidad_puestos);
        } else {
            $puestosIds = $puestosIdsAll;
        }
    }

    // indexar puestos para acceder zona
    $puestosIndex = [];
    foreach ($puestos as $p) $puestosIndex[$p['IDPUESTO']] = $p;

    // obtener mesas por puesto (limitadas por cantidad_mesas_por_puesto)
    $mesasPorPuesto = [];
    foreach ($puestosIds as $pid) {
        $sqlM = "SELECT MT.MESA AS ID, MT.MESA AS MESA FROM mesas_testigo MT WHERE MT.IDPUESTO = " . intval($pid);
        $DBGestion->ConsultaArray($sqlM);
        $list = $DBGestion->datos;
        if (count($list) > $cantidad_mesas_por_puesto) {
            shuffle($list);
            $list = array_slice($list, 0, $cantidad_mesas_por_puesto);
        }
        $mesasPorPuesto[$pid] = $list;
    }

    if ($action === 'preview') {
        // construir lista única de pares (puesto, mesa) disponibles
        $pairs = [];
        foreach ($puestosIds as $pid) {
            $mesasList = isset($mesasPorPuesto[$pid]) ? $mesasPorPuesto[$pid] : [];
            foreach ($mesasList as $m) {
                $pairs[] = [
                    'puesto' => $pid,
                    'puesto_label' => isset($puestosIndex[$pid]['PUESTO_NOMBRE']) ? $puestosIndex[$pid]['PUESTO_NOMBRE'] : $pid,
                    'zona' => isset($puestosIndex[$pid]['ZONA']) ? $puestosIndex[$pid]['ZONA'] : '',
                    'mesa_id' => $m['ID'],
                    'mesa_label' => $m['MESA']
                ];
            }
        }

        if (empty($pairs)) {
            $mensaje = "No hay combinaciones puesto+mesa disponibles para las zonas/puestos seleccionados.";
            $previewAssignments = [];
            $confirm_payload = '';
        } else {
            // mezclar si se pidió aleatorio
            if ($usarPuestosAleatorios || $usarMesasAleatorias) shuffle($pairs);

            // limitar a cantidad disponible
            $maxAvailable = count($pairs);
            if ($cantidad > $maxAvailable) {
                $mensaje = "Solo hay $maxAvailable combinaciones únicas (puesto+mesa). Se mostrarán $maxAvailable asignaciones.";
            }
            $take = min($cantidad, $maxAvailable);

            // generar asignaciones sin repetir el mismo puesto+mesa
            for ($i = 0; $i < $take; $i++) {
                $pair = $pairs[$i];
                $usernameBase = preg_replace('/\s+/', '', strtolower($prefix)) . ($i + 1);
                $nombre = ucfirst($prefix) . ' ' . ($i + 1);

                $previewAssignments[] = [
                    'username' => $usernameBase,
                    'nombre' => $nombre,
                    'puesto' => $pair['puesto'],
                    'puesto_label' => $pair['puesto_label'],
                    'mesa_id' => $pair['mesa_id'],
                    'mesa_label' => $pair['mesa_label'],
                    'zona' => $pair['zona'],
                    'departamento' => $departamento,
                    'municipio' => $municipio,
                ];
            }

            $confirm_payload = base64_encode(json_encode([
                'meta' => [
                    'prefix' => $prefix,
                    'password' => $passwordPlain,
                    'usarPuestosAleatorios' => $usarPuestosAleatorios,
                    'usarMesasAleatorias' => $usarMesasAleatorias,
                    'zonas' => $zonasSelected,
                    'puestos_pool' => $puestosIds,
                    'pairs_pool' => $pairs
                ],
                'rows' => $previewAssignments
            ]));
        }
    } elseif ($action === 'generar') {
        if (!empty($_POST['confirm_data'])) {
            $data = json_decode(base64_decode($_POST['confirm_data']), true);
            if (!is_array($data) || !isset($data['rows'])) {
                $mensaje = 'Confirmación inválida.';
            } else {
                $sqldelmesas="UPDATE mesas_testigo
                            SET NF000 = NULL,
                                NF001 = NULL,
                                NF002 = NULL,
                                NF003 = NULL,
                                NF004 = NULL,
                                NF005 = NULL,
                                NF006 = NULL,
                                NF007 = NULL,
                                NF008 = NULL,
                                NF009 = NULL,
                                NF010 = NULL,
                                VOTOS_CANDIDATOS = NULL,
                                TOTALMESA = NULL,
                                VOTOPARTIDO = NULL,
                                VOTOS_BLANCO = NULL,
                                VOTOS_NULOS = NULL,
                                VOTOS_NO_MARCADOS = NULL
                            WHERE idcandidato = 224";
                $DBGestion->Consulta($sqldelmesas);
                
                $sqldel="DELETE FROM usuario WHERE usuario LIKE 'testigo%' and asosiado='roybarrera'";
                $DBGestion->Consulta($sqldel);
                $created = [];
                foreach ($data['rows'] as $row) {
                    $username = $row['username'];
                    $k = 0;
                    while (true) {
                        $sqlCheck = "SELECT COUNT(*) AS cnt FROM usuario WHERE usuario = '" . addslashes($username) . "' and asosiado='roybarrera'";
                        $DBGestion->ConsultaArray($sqlCheck);
                        $cnt = intval($DBGestion->datos[0]['cnt']);
                        if ($cnt === 0) break;
                        $k++;
                        $username = $row['username'] . '_' . $k;
                    }

                    $nombre = $row['nombre'];
                    $puesto = intval($row['puesto']);
                    $mesaId = intval($row['mesa_id']);
                    $departamento = intval($row['departamento']);
                    $municipio = intval($row['municipio']);
                    $passHash = sha1($data['meta']['password']);
                    
                     $sqlIns = "INSERT INTO usuario (usuario, contrasena, nombre, puesto, mesa, departamentos, municipal, activo,permiso,consulta,ASOSIADO)
                        VALUES ('" . addslashes($username) . "', '" . addslashes($passHash) . "', '" . addslashes($nombre) . "', '" . addslashes($puesto) . "', '" . addslashes($mesaId) . "', '" . addslashes($departamento) . "', '" . addslashes($municipio) . "', 'Y',5,5,'roybarrera')";
                    try {
                        $DBGestion->Consulta($sqlIns);

                        // Actualizar mesa asignada: setear IDTESTIGO (usuario) e IDCANDIDATO = 224
                        $sqlUpdateMesa = "UPDATE mesas_testigo
                            SET IDTESTIGO = '" . addslashes($username) . "',
                                IDCANDIDATO = 224
                            WHERE IDPUESTO = " . intval($puesto) . " AND mesa = " . intval($mesaId);
                        try {
                            $DBGestion->Consulta($sqlUpdateMesa);
                        } catch (Exception $eUpd) {
                            // no detener inserción de usuario; anotar fallo en el resumen
                            $created[] = [
                                'usuario' => $username,
                                'nombre' => $nombre,
                                'puesto' => $puesto,
                                'puesto_label' => isset($row['puesto_label']) ? $row['puesto_label'] : $puesto,
                                'mesa' => $mesaId,
                                'mesa_label' => isset($row['mesa_label']) ? $row['mesa_label'] : $mesaId,
                                'zona' => isset($row['zona']) ? $row['zona'] : '',
                                'mesa_update_error' => $eUpd->getMessage()
                            ];
                            continue;
                        }

                        // guardar labels para mostrar en el resumen (no sólo ids)
                        $created[] = [
                            'usuario' => $username,
                            'nombre' => $nombre,
                            'puesto' => $puesto,
                            'puesto_label' => isset($row['puesto_label']) ? $row['puesto_label'] : $puesto,
                            'mesa' => $mesaId,
                            'mesa_label' => isset($row['mesa_label']) ? $row['mesa_label'] : $mesaId,
                            'zona' => isset($row['zona']) ? $row['zona'] : ''
                        ];
                    } catch (Exception $e) {
                        $created[] = [
                            'usuario' => $username,
                            'error' => $e->getMessage(),
                            'puesto_label' => isset($row['puesto_label']) ? $row['puesto_label'] : $puesto,
                            'mesa_label' => isset($row['mesa_label']) ? $row['mesa_label'] : $mesaId,
                            'zona' => isset($row['zona']) ? $row['zona'] : ''
                        ];
                    }
                }
                // guardar resumen en sesión y redirigir (PRG) para evitar reenvío por recarga
                $_SESSION['sim_created'] = ['count' => count($created), 'created' => $created];
                header('Location: simulador_testigos.php?sim_created=1');
                exit;
             }
        } else {
            $mensaje = 'No hay confirmación. Primero vista previa.';
        }
    }
}

// obtener listados para selects (departamentos y municipios)
// departamentos
$DBGestion->ConsultaArray("SELECT IDDEPARTAMENTO AS id, NOMBRE AS nombre FROM departamentos ORDER BY NOMBRE");
$departamentos = $DBGestion->datos;
// municipios (cargar todos o filtrar)
$DBGestion->ConsultaArray("SELECT ID AS id, NOMBRE AS nombre, IDDEPARTAMENTO FROM municipios ORDER BY NOMBRE");
$municipios = $DBGestion->datos;

// --- añadir mapeo de puestos por zona para el municipio por defecto (1145) ---
$defaultMunicipio = 1145;
$DBGestion->ConsultaArray("SELECT IDPUESTO, ZONA, nombre_puesto FROM puestos_votacion WHERE IDMUNICIPIO = " . intval($defaultMunicipio) . " ORDER BY nombre_puesto");
$puestos_for_default_mun = $DBGestion->datos;
$puestos_by_zone = [];
foreach($puestos_for_default_mun as $p){
    $zona = $p['ZONA'] ?? '';
    if(!isset($puestos_by_zone[$zona])) $puestos_by_zone[$zona] = [];
    $puestos_by_zone[$zona][] = $p;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Simulador Testigos</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/metro.css" rel="stylesheet" />
    <link href="assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/style_responsive.css" rel="stylesheet" />
    <link href="assets/css/style_default.css" rel="stylesheet" id="style_color" />
    <link rel="shortcut icon" href="images/favicon(2).ico" />
    <style>
      .container{margin-top:20px} .portlet-title h4{margin:0;padding:8px 0}
      /* Menu hover rojo para Informes y Simulador */
      .menu-red:hover { background-color: #c9302c !important; }
      .menu-red:hover > a { color: #fff !important; }
      .menu-red:hover .title { color: #fff !important; }
    </style>
</head>
<body class="fixed-top">
   <div class="header navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
         <div class="container-fluid">
            <a class="brand" href="index.html">
            <img src="images/logo_movil_original.png" alt="logo" width="100" height="68"/>
            </a>
            <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
            <img src="assets/img/menu-toggler.png" alt="" />
            </a>
            <ul class="nav pull-right">
               <li class="dropdown user">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?php echo isset($_SESSION['foto'])&&$_SESSION['foto']?$_SESSION['foto']:'fotos/images.jpg'?>" width="24" height="38" style="border:1px solid #CCCCCC;">
                  <span class="username"><?php echo htmlspecialchars($nombre ?? '')?> (<?php echo htmlspecialchars($_SESSION['usuarioasociado'] ?? '')?>)</span>
                  <i class="icon-angle-down"></i>
                  </a>
                  <ul class="dropdown-menu">
                     <li><a href="logout.php"><i class="icon-key"></i>Cerrar Session</a></li>
                  </ul>
               </li>
            </ul>
         </div>
      </div>
   </div>

   <div class="page-container row-fluid">
      <!-- BEGIN SIDEBAR -->
      <div class="page-sidebar nav-collapse collapse">
         <!-- BEGIN SIDEBAR MENU -->
         <ul>
            <li>
               <div class="sidebar-toggler hidden-phone"></div>
            </li>

            <li class="has-sub">
               <a href="diad_electoral.php">
               <i class="icon-calendar"></i>
               <span class="title">Dia Electoral</span>
               <span class="arrow"></span>
               </a>
            </li>
            <li class="menu-red">
               <a href="informe_testigos.php">
               <i class="icon-table"></i>
               <span class="title">Informes</span>
               </a>
            </li>
            <?php if($_SESSION['consulta']==0): ?>
            <li class="active menu-red">
               <a href="simulador_testigos.php">
               <i class="icon-signal"></i>
               <span class="title">Simulador</span>
               <span class="selected"></span>
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
         <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="portlet box blue">
          <div class="portlet-title">
            <h4><i class="icon-reorder"></i> Simulador de Testigos</h4>
          </div>
          <div class="portlet-body form">
            <?php if($mensaje): ?>
              <div class="alert alert-info"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <?php if(!empty($created_summary)): ?>
              <div class="alert alert-success">
                Simulación completada. Creados: <?php echo intval($created_summary['count']); ?>.
                <a href="simulador_testigos.php" class="btn btn-default" style="margin-left:10px;">Volver</a>
              </div>
              <table class="table table-condensed table-striped">
                <thead><tr><th>#</th><th>usuario</th><th>nombre</th><th>puesto</th><th>mesa</th><th>zona</th></tr></thead>
                <tbody>
                <?php foreach($created_summary['created'] as $i=>$r): ?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo htmlspecialchars($r['usuario'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($r['nombre'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($r['puesto_label'] ?? $r['puesto'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($r['mesa_label'] ?? $r['mesa'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($r['zona'] ?? ''); ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>

            <!-- BEGIN FORM -->
            <form method="post" id="simuladorForm" class="form-horizontal">
              <input type="hidden" name="action" value="preview">

              <div class="control-group">
                <label class="control-label">Cantidad</label>
                <div class="controls">
                  <input type="number" name="cantidad" class="span2" value="<?php echo htmlspecialchars($cantidad); ?>" min="1" required>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Prefijo username</label>
                <div class="controls">
                  <input type="text" name="prefix" class="span3" value="<?php echo htmlspecialchars($prefix); ?>">
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Password</label>
                <div class="controls">
                  <input type="text" name="password" class="span3" value="<?php echo htmlspecialchars($passwordPlain); ?>">
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Departamento</label>
                <div class="controls">
                  <select name="departamento" id="departamento" class="span4">
                    <?php foreach($departamentos as $d): ?>
                      <option value="<?php echo $d['id']; ?>" <?php echo ($d['id']==$departamento)?'selected':''; ?>><?php echo htmlspecialchars($d['nombre']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Municipio</label>
                <div class="controls">
                  <select name="municipio" id="municipio" class="span4">
                    <?php foreach($municipios as $m):
                      if ($m['IDDEPARTAMENTO'] != $departamento) continue;
                    ?>
                      <option value="<?php echo $m['id']; ?>" <?php echo ($m['id']==$municipio)?'selected':''; ?>><?php echo htmlspecialchars($m['nombre']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Modo</label>
                <div class="controls">
                  <label class="radio"><input type="radio" name="mode" value="aleatorio" <?php if($mode!=='seleccionar') echo 'checked'; ?>> Aleatorio</label>
                  <label class="radio"><input type="radio" name="mode" value="seleccionar" <?php if(isset($mode) && $mode==='seleccionar') echo 'checked'; ?>> Seleccionar puesto</label>
                </div>
              </div>

              <div id="select_mode_controls" class="control-group" style="display:<?php echo (isset($mode) && $mode==='seleccionar')?'block':'none'; ?>;">
                <label class="control-label">Zona / Puestos</label>
                <div class="controls">
                  <select id="zona_select" name="zona_selected" class="span3">
                      <option value="">-- elegir zona --</option>
                  </select>
                  <select id="puestos_select" name="puestos_selected[]" class="span5" multiple size="6" style="margin-top:6px;">
                  </select>
                </div>
              </div>

              <div class="control-group" id="grp_zonas">
                <label class="control-label"># Zonas</label>
                <div class="controls">
                  <input type="number" name="cantidad_zonas" class="span2" value="<?php echo htmlspecialchars($cantidad_zonas); ?>" min="1" max="50">
                </div>
              </div>

              <div class="control-group" id="grp_puestos_pool">
                <label class="control-label"># Puestos (pool)</label>
                <div class="controls">
                  <input type="number" name="cantidad_puestos" class="span2" value="<?php echo htmlspecialchars($cantidad_puestos); ?>" min="1">
                </div>
              </div>

              <div class="control-group">
                <label class="control-label"># Mesas por puesto (pool)</label>
                <div class="controls">
                  <input type="number" name="cantidad_mesas" class="span2" value="<?php echo htmlspecialchars($cantidad_mesas_por_puesto); ?>" min="1">
                </div>
              </div>

              <div class="control-group">
                <div class="controls">
                  <label class="checkbox inline"><input type="checkbox" name="random_puestos" <?php if($usarPuestosAleatorios) echo 'checked'; ?>> Puestos aleatorios</label>
                  <label class="checkbox inline"><input type="checkbox" name="random_mesas" <?php if($usarMesasAleatorias) echo 'checked'; ?>> Mesas aleatorias</label>
                </div>
              </div>

              <div class="form-actions">
                <button class="btn btn-primary" type="submit">Vista previa</button>
                <a href="simulador_testigos.php" class="btn">Cancelar</a>
              </div>
            </form>
            <!-- END FORM -->

            <?php if(!empty($previewAssignments)): ?>
              <hr>
              <h4>Asignaciones generadas (vista previa)</h4>
              <form method="post" class="form-horizontal">
                <input type="hidden" name="action" value="generar">
                <input type="hidden" name="confirm_data" value="<?php echo htmlspecialchars($confirm_payload); ?>">
                <table class="table table-bordered table-condensed">
                  <thead><tr><th>#</th><th>Usuario</th><th>Nombre</th><th>Puesto</th><th>Mesa</th><th>Zona</th></tr></thead>
                  <tbody>
                  <?php foreach($previewAssignments as $i => $row): ?>
                      <tr>
                          <td><?php echo $i+1; ?></td>
                          <td><?php echo htmlspecialchars($row['username']); ?></td>
                          <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                          <td><?php echo htmlspecialchars($row['puesto_label']); ?></td>
                          <td><?php echo htmlspecialchars($row['mesa_label']); ?></td>
                          <td><?php echo htmlspecialchars($row['zona']); ?></td>
                      </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
                <div class="form-actions">
                  <button class="btn btn-success" type="submit">Confirmar e insertar</button>
                  <a href="simulador_testigos.php" class="btn">Cancelar</a>
                </div>
              </form>
            <?php endif; ?>

          </div><!-- portlet-body -->
        </div><!-- portlet -->
      </div><!-- span12 -->
    </div><!-- row -->
  </div><!-- container-fluid -->
</div><!-- page-container -->

<script src="assets/js/jquery-1.8.3.min.js"></script>
<script src="assets/breakpoints/breakpoints.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.blockui.js"></script>
<script src="assets/js/jquery.cookie.js"></script>
<script src="assets/js/app.js"></script>
<script>
    jQuery(document).ready(function() {
        App.init();
    });
</script>
<script>
    var municipios = <?php echo json_encode($municipios); ?>;
    var puestosByZone = <?php echo json_encode($puestos_by_zone); ?>;

    $('#departamento').on('change', function(){
        var did = $(this).val();
        var opts = '';
        municipios.forEach(function(m){
            if (String(m.IDDEPARTAMENTO) === String(did)) {
                opts += '<option value="'+m.id+'">'+m.nombre+'</option>';
            }
        });
        $('#municipio').html(opts);
    });

    function updateModeUI(mode){
        if(mode === 'seleccionar'){
            $('#select_mode_controls').show();
            $('#grp_zonas, #grp_puestos_pool').hide();
        }else{
            $('#select_mode_controls').hide();
            $('#grp_zonas, #grp_puestos_pool').show();
        }
    }

    $('input[name="mode"]').on('change', function(){
        updateModeUI($(this).val());
    });

    function populateZones(){
        var zonaSel = $('#zona_select');
        zonaSel.empty();
        zonaSel.append('<option value="">-- elegir zona --</option>');
        Object.keys(puestosByZone).forEach(function(z){
            if(!z) return;
            zonaSel.append('<option value="'+z+'">'+z+'</option>');
        });
    }

    $('#zona_select').on('change', function(){
        var z = $(this).val();
        var puestosSelect = $('#puestos_select');
        puestosSelect.empty();
        if(!z || !puestosByZone[z]) return;
        puestosByZone[z].forEach(function(p){
            puestosSelect.append('<option value="'+p.IDPUESTO+'">'+ (p.PUESTO_NOMBRE || p.nombre_puesto || p.IDPUESTO) +'</option>');
        });
    });

    $(function(){
        populateZones();
        updateModeUI($('input[name="mode"]:checked').val());
        <?php if(!empty($_POST['zona_selected'])): ?>
            $('#zona_select').val('<?php echo addslashes($_POST['zona_selected']); ?>').trigger('change');
            <?php if(!empty($_POST['puestos_selected']) && is_array($_POST['puestos_selected'])): ?>
                var sel = <?php echo json_encode(array_map('intval', $_POST['puestos_selected'])); ?>;
                setTimeout(function(){ $('#puestos_select').val(sel); },100);
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
</body>
</html>
