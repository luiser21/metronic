<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
include_once "includes/GestionBD.new.class.php";
$DBGestion = new GestionBD('AGENDAMIENTO');

$candidato = 224; // ID candidato a monitorear
$departamento = isset($_GET['departamento']) ? intval($_GET['departamento']) : 35;
$municipio   = isset($_GET['municipio']) ? intval($_GET['municipio']) : 1145;
$zonaFilter  = isset($_GET['zona']) ? trim($_GET['zona']) : '';
$exportCsv   = isset($_GET['export']) ? true : false;

// cargar selects
$DBGestion->ConsultaArray("SELECT IDDEPARTAMENTO AS id, NOMBRE AS nombre FROM departamentos ORDER BY NOMBRE");
$departamentos = $DBGestion->datos;
$DBGestion->ConsultaArray("SELECT ID AS id, NOMBRE AS nombre, IDDEPARTAMENTO FROM municipios ORDER BY NOMBRE");
$municipios = $DBGestion->datos;

// filtros adicionales
$zoneWhere = $zonaFilter !== '' ? " AND PV.ZONA = '" . addslashes($zonaFilter) . "' " : "";

// resumen general por municipio (con NF000..NF010)
$sqlTotal = "
  SELECT 
    SUM(IFNULL(MT.NF000,0)) AS NF000,
    SUM(IFNULL(MT.NF001,0)) AS NF001,
    SUM(IFNULL(MT.NF002,0)) AS NF002,
    SUM(IFNULL(MT.NF003,0)) AS NF003,
    SUM(IFNULL(MT.NF004,0)) AS NF004,
    SUM(IFNULL(MT.NF005,0)) AS NF005,
    SUM(IFNULL(MT.NF006,0)) AS NF006,
    SUM(IFNULL(MT.NF007,0)) AS NF007,
    SUM(IFNULL(MT.NF008,0)) AS NF008,
    SUM(IFNULL(MT.NF009,0)) AS NF009,
    SUM(IFNULL(MT.NF010,0)) AS NF010,
    SUM(IFNULL(MT.TOTALMESA,0)) AS TOTALMESA,
    SUM(IFNULL(MT.VOTOPARTIDO,0)) AS VOTOPARTIDO,
    SUM(IFNULL(MT.VOTOS_CANDIDATOS,0)) AS VOTOS_CANDIDATOS,
    SUM(IFNULL(MT.VOTOS_BLANCO,0)) AS BLANCOS,
    SUM(IFNULL(MT.VOTOS_NULOS,0)) AS NULOS,
    SUM(IFNULL(MT.VOTOS_NO_MARCADOS,0)) AS VOTOS_NO_MARCADOS,
    COUNT(DISTINCT MT.ID) AS MESAS
  FROM mesas_testigo MT
  JOIN puestos_votacion PV ON PV.IDPUESTO = MT.IDPUESTO
  WHERE MT.IDCANDIDATO = " . intval($candidato) . "
    AND PV.IDMUNICIPIO = " . intval($municipio) . "
    $zoneWhere
";
$DBGestion->ConsultaArray($sqlTotal);
$tot = $DBGestion->datos[0];

// detalle por zona (ahora incluye VOTOS_NO_MARCADOS)
$sqlZona = "
  SELECT 
    PV.ZONA,
    SUM(IFNULL(MT.NF000,0)) AS NF000,
    SUM(IFNULL(MT.NF001,0)) AS NF001,
    SUM(IFNULL(MT.NF002,0)) AS NF002,
    SUM(IFNULL(MT.NF003,0)) AS NF003,
    SUM(IFNULL(MT.NF004,0)) AS NF004,
    SUM(IFNULL(MT.NF005,0)) AS NF005,
    SUM(IFNULL(MT.NF006,0)) AS NF006,
    SUM(IFNULL(MT.NF007,0)) AS NF007,
    SUM(IFNULL(MT.NF008,0)) AS NF008,
    SUM(IFNULL(MT.NF009,0)) AS NF009,
    SUM(IFNULL(MT.NF010,0)) AS NF010,
    SUM(IFNULL(MT.TOTALMESA,0)) AS TOTALMESA,
    SUM(IFNULL(MT.VOTOS_BLANCO,0)) AS BLANCOS,
    SUM(IFNULL(MT.VOTOS_NULOS,0)) AS NULOS,
    SUM(IFNULL(MT.VOTOS_NO_MARCADOS,0)) AS VOTOS_NO_MARCADOS,
    COUNT(DISTINCT PV.IDPUESTO) AS PUESTOS,
    COUNT(DISTINCT MT.ID) AS MESAS
  FROM mesas_testigo MT
  JOIN puestos_votacion PV ON PV.IDPUESTO = MT.IDPUESTO
  WHERE MT.IDCANDIDATO = " . intval($candidato) . "
    AND PV.IDMUNICIPIO = " . intval($municipio) . "
  GROUP BY PV.ZONA
  ORDER BY PV.ZONA
";
$DBGestion->ConsultaArray($sqlZona);
$zonas = $DBGestion->datos;

// obtener lista completa de zonas del municipio (mostrar también zonas con 0)
// Reemplazado: no incluir zonas vacías "(SIN ZONA)"
$DBGestion->ConsultaArray("SELECT DISTINCT ZONA FROM puestos_votacion WHERE IDMUNICIPIO = ".intval($municipio)." ORDER BY ZONA");
$allZoneRows = $DBGestion->datos;
$allZones = [];
foreach($allZoneRows as $zr){
    $zn = trim($zr['ZONA']);
    if($zn === '') continue; // OMITIR zonas vacías
    $allZones[] = $zn;
}

// construir mapa zona => datos (si no existe usar ceros)
// Asegurar clave trim() para coincidencia correcta
$zoneMap = [];
foreach($zonas as $z){
    $zoneMap[trim($z['ZONA'])] = $z; // contiene NF000..NF010, BLANCOS, NULOS, MESAS
}

// detalle por puesto (con NF000..NF010) — solo puestos con testigos asignados
$sqlPuesto = "
  SELECT 
    PV.IDPUESTO,
    PV.nombre_puesto AS PUESTO_NOMBRE,
    PV.ZONA,
    SUM(IFNULL(MT.NF000,0)) AS NF000,
    SUM(IFNULL(MT.NF001,0)) AS NF001,
    SUM(IFNULL(MT.NF002,0)) AS NF002,
    SUM(IFNULL(MT.NF003,0)) AS NF003,
    SUM(IFNULL(MT.NF004,0)) AS NF004,
    SUM(IFNULL(MT.NF005,0)) AS NF005,
    SUM(IFNULL(MT.NF006,0)) AS NF006,
    SUM(IFNULL(MT.NF007,0)) AS NF007,
    SUM(IFNULL(MT.NF008,0)) AS NF008,
    SUM(IFNULL(MT.NF009,0)) AS NF009,
    SUM(IFNULL(MT.NF010,0)) AS NF010,
    SUM(IFNULL(MT.TOTALMESA,0)) AS TOTALMESA,
    SUM(IFNULL(MT.VOTOS_BLANCO,0)) AS BLANCOS,
    SUM(IFNULL(MT.VOTOS_NULOS,0)) AS NULOS,
    COUNT(DISTINCT MT.ID) AS MESAS,
    (SELECT COUNT(*) FROM usuario U WHERE U.puesto = PV.IDPUESTO) AS TESTIGOS_ASIGNADOS
  FROM mesas_testigo MT
  JOIN puestos_votacion PV ON PV.IDPUESTO = MT.IDPUESTO
  WHERE MT.IDCANDIDATO = " . intval($candidato) . "
    AND PV.IDMUNICIPIO = " . intval($municipio) . "
    $zoneWhere
  GROUP BY PV.IDPUESTO, PV.nombre_puesto, PV.ZONA
  HAVING TESTIGOS_ASIGNADOS>0
  ORDER BY PV.nombre_puesto
";
$DBGestion->ConsultaArray($sqlPuesto);
$puestos = $DBGestion->datos;

// CSV export
if ($exportCsv) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=monitor_testigos_mun_' . $municipio . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Tipo','Zona','Puesto ID','Puesto','Mesas','TOTALMESA','NF000','NF001','NF002','NF003','NF004','NF005','NF006','NF007','NF008','NF009','NF010','Votos Blanco','Votos Nulos','TestigosAsignados']);
    foreach ($puestos as $p) {
        fputcsv($out, [
            'PUESTO', $p['ZONA'], $p['IDPUESTO'], $p['PUESTO_NOMBRE'], $p['MESAS'],
            intval($p['TOTALMESA']),
            $p['NF000'],$p['NF001'],$p['NF002'],$p['NF003'],$p['NF004'],$p['NF005'],$p['NF006'],$p['NF007'],$p['NF008'],$p['NF009'],$p['NF010'],
            $p['BLANCOS'], $p['NULOS'], $p['TESTIGOS_ASIGNADOS']
        ]);
    }
    foreach ($zonas as $z) {
        fputcsv($out, [
            'ZONA', $z['ZONA'], '', '', $z['MESAS'],
            intval($z['TOTALMESA']),
            $z['NF000'],$z['NF001'],$z['NF002'],$z['NF003'],$z['NF004'],$z['NF005'],$z['NF006'],$z['NF007'],$z['NF008'],$z['NF009'],$z['NF010'],
            $z['BLANCOS'], $z['NULOS'], ''
        ]);
    }
    fputcsv($out, [
        'TOTAL', '', '', '', $tot['MESAS'],
        intval($tot['TOTALMESA']),
        $tot['NF000'],$tot['NF001'],$tot['NF002'],$tot['NF003'],$tot['NF004'],$tot['NF005'],$tot['NF006'],$tot['NF007'],$tot['NF008'],$tot['NF009'],$tot['NF010'],
        $tot['BLANCOS'],$tot['NULOS'],''
    ]);
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <title>Monitoreo - Testigos</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport" />
   <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
   <link href="assets/css/metro.css" rel="stylesheet" />
   <link href="assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
   <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
   <link href="assets/css/style.css" rel="stylesheet" />
   <link href="assets/css/style_responsive.css" rel="stylesheet" />
   <link href="assets/css/style_default.css" rel="stylesheet" id="style_color" />
   <link rel="shortcut icon" href="images/favicon(2).ico" />
   <style>
       /* layout básico */
       .summary .well{padding:12px;}
       .filters {margin-bottom:12px;}
       .portlet-title h4{margin:0;padding:8px 0}
       table th, table td{font-size:13px;}

       /* contenedor responsive con scroll horizontal */
       .table-scroll{
         overflow-x:auto;
         -webkit-overflow-scrolling:touch;
         margin-bottom:16px;
         border:1px solid #e6e6e6;
         border-radius:4px;
         background:#fff;
         padding:6px;
       }
       .table-scroll .table{ min-width:1200px; /* ajustar si se añaden columnas */ }

       /* estilos tipo Bootstrap para tablas (hover / striped / condensed) */
       .table-hover tbody tr:hover{ background-color:#f5f5f5; }
       .table-striped>tbody>tr:nth-of-type(odd){ background-color:#ffffff; }
       .table-striped>tbody>tr:nth-of-type(even){ background-color:#fbfbfb; }
       .table-condensed th, .table-condensed td{ padding:6px 8px; vertical-align:middle; }
       .table-bordered{ border:1px solid #ddd; }
       .table-bordered>thead>tr>th, .table-bordered>tbody>tr>td{ border:1px solid #e9e9e9; }

       /* sticky header para facilitar lectura en scroll */
       .table-scroll thead th{
         position:sticky;
         top:0;
         background:#f7f9fb;
         z-index:3;
         border-bottom:2px solid #ddd;
       }

       /* resaltados de columnas importantes (colores suaves) */
       .col-mesas{ background:linear-gradient(#f0f8ff,#e8f4ff); font-weight:700; color:#054a7a; text-align:center; }
       .col-votopartido{ background:linear-gradient(#fffef3,#fff4cc); font-weight:800; color:#8a6d00; text-align:right; }
       .col-votoscandidatos{ background:linear-gradient(#f6fff6,#e6ffea); font-weight:800; color:#167a2b; text-align:right; }
       .col-blancos{ background:linear-gradient(#fbfbfb,#f0f0f0); font-weight:700; color:#444; text-align:right; }

       /* celdas numéricas */
       .num { text-align:right; font-family:monospace; padding-right:10px; }

       /* hover general para filas */
       .table tbody tr:hover{ background:#fff7e6; }

       @media (max-width:480px){
         .table-scroll{ padding:4px; }
         .table-scroll .table{ min-width:1000px; }
       }
    </style>
    <script type="text/javascript" src="js/FAjax.js"></script>
</head>
<body class="fixed-top">
   <!-- header like registrar_testigos -->
   <div class="header navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
         <div class="container-fluid">
            <img src="images/logo2_movil.png" alt="logo"  width="159" height="108"/>
            <ul class="nav pull-right">
               <li class="dropdown user">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <?php if(!empty($_SESSION['foto'])): ?>
                    <img src="<?php echo $_SESSION['foto']?>" width="24" height="38" style="border:1px solid #CCCCCC;">
                  <?php else: ?>
                    <img src="fotos/images.jpg" width="24" height="38" style="border:1px solid #CCCCCC;">
                  <?php endif; ?>
                  <span class="username"><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?> (<?php echo htmlspecialchars($_SESSION['usuarioasociado'] ?? ''); ?>)</span>
                  <i class="icon-angle-down"></i>
                  </a>
               </li>
            </ul>
         </div>
      </div>
   </div>

   <div class="page-container row-fluid" style="margin-top:120px;">
      <div class="container-fluid">
         <div class="row-fluid">
            <div class="span12">
               <div class="portlet box blue">
                  <div class="portlet-title">
                     <h4><i class="icon-reorder"></i> Monitoreo de carga — candidato <?php echo $candidato;?></h4>
                  </div>
                  <div class="portlet-body">
                      <form class="form-inline filters" method="get">
                        <label>Departamento</label>
                        <select name="departamento" id="departamento" class="input-medium">
                          <?php foreach($departamentos as $d): ?>
                            <option value="<?php echo $d['id'];?>" <?php if($d['id']==$departamento) echo 'selected';?>><?php echo htmlspecialchars($d['nombre']);?></option>
                          <?php endforeach;?>
                        </select>

                        <label style="margin-left:8px">Municipio</label>
                        <select name="municipio" id="municipio" class="input-medium">
                          <?php foreach($municipios as $m): if($m['IDDEPARTAMENTO'] != $departamento) continue; ?>
                            <option value="<?php echo $m['id'];?>" <?php if($m['id']==$municipio) echo 'selected';?>><?php echo htmlspecialchars($m['nombre']);?></option>
                          <?php endforeach;?>
                        </select>

                        <label style="margin-left:8px">Zona</label>
                        <input name="zona" class="input-small" value="<?php echo htmlspecialchars($zonaFilter);?>" placeholder="opcional">

                        <button class="btn btn-primary" type="submit" style="margin-left:8px">Filtrar</button>
                        <a class="btn btn-default" style="margin-left:8px" href="?departamento=<?php echo $departamento;?>&municipio=<?php echo $municipio;?>&export=1">Export CSV</a>
                      </form>

                      <div class="row-fluid summary">
                        <div class="span3">
                          <div class="well">
                            <strong>Mesas</strong><br><?php echo intval($tot['MESAS']);?>
                          </div>
                        </div>
                        <div class="span3">
                          <div class="well">
                            <strong>NF000 (VOTOPARTIDO)</strong><br><?php echo intval($tot['NF000']);?>
                          </div>
                        </div>
                        <div class="span3">
                          <div class="well">
                            <strong>NF001</strong><br><?php echo intval($tot['NF001']);?>
                          </div>
                        </div>
                        <div class="span3">
                          <div class="well">
                            <strong>Blancos / Nulos</strong><br><?php echo intval($tot['BLANCOS']);?> / <?php echo intval($tot['NULOS']);?>
                          </div>
                        </div>
                      </div>

                      <h4>Resumen por Zona</h4>
                      <div class="table-responsive">
                      <div class="table-scroll">
                      <table class="table table-hover table-striped table-bordered table-condensed">
                         <thead>
                           <tr>
                             <th>#</th>
                             <th>Zona</th>
                             <th class="col-mesas">Mesas</th>
                             <th class="col-votopartido">NF000<br><small>VOTOPARTIDO</small></th>
                             <th class="col-votoscandidatos">NF001<br><small></small></th>
                             <th>NF002</th><th>NF003</th><th>NF004</th>
                             <th>NF005</th><th>NF006</th><th>NF007</th><th>NF008</th><th>NF009</th><th>NF010</th>
                            <th class="col-blancos">Blancos</th><th>Nulos</th>
                           </tr>
                         </thead>
                         <tbody>
                           <?php $i=0; foreach($zonas as $z): $i++; ?>
                             <tr>
                               <td><?php echo $i;?></td>
                               <td><?php echo htmlspecialchars($z['ZONA']);?></td>
                               <td class="col-mesas num"><?php echo intval($z['MESAS']);?></td>
                               <td class="col-votopartido num"><?php echo intval($z['NF000']);?></td>
                               <td class="col-votoscandidatos num"><?php echo intval($z['NF001']);?></td>
                               <td class="num"><?php echo intval($z['NF002']);?></td>
                               <td class="num"><?php echo intval($z['NF003']);?></td>
                               <td class="num"><?php echo intval($z['NF004']);?></td>
                               <td class="num"><?php echo intval($z['NF005']);?></td>
                               <td class="num"><?php echo intval($z['NF006']);?></td>
                               <td class="num"><?php echo intval($z['NF007']);?></td>
                               <td class="num"><?php echo intval($z['NF008']);?></td>
                               <td class="num"><?php echo intval($z['NF009']);?></td>
                               <td class="num"><?php echo intval($z['NF010']);?></td>
                               <td class="col-blancos num"><?php echo intval($z['BLANCOS']);?></td>
                               <td class="num"><?php echo intval($z['NULOS']);?></td>
                             </tr>
                           <?php endforeach;?>
                         </tbody>
                      </table>
                      </div>
                      </div>

                      <h4>Detalle por Puesto</h4>
                      <div class="table-responsive">
                      <div class="table-scroll">
                      <table class="table table-bordered table-condensed table-striped">
                         <thead>
                           <tr>
                             <th>#</th><th>Puesto</th><th>Zona</th>
                            <th class="col-mesas">Mesas</th>
                            <th class="col-votopartido">NF000</th><th class="col-votoscandidatos">NF001</th><th>NF002</th><th>NF003</th><th>NF004</th>
                            <th>NF005</th><th>NF006</th><th>NF007</th><th>NF008</th><th>NF009</th><th>NF010</th>
                            <th class="col-blancos">Blancos</th><th>Nulos</th><th>Testigos asign.</th>
                           </tr>
                         </thead>
                         <tbody>
                           <?php $i=0; foreach($puestos as $p): $i++; ?>
                             <tr>
                               <td><?php echo $i;?></td>
                               <td><?php echo htmlspecialchars($p['PUESTO_NOMBRE'] ?: 'Puesto '.$p['IDPUESTO']);?></td>
                               <td><?php echo htmlspecialchars($p['ZONA']);?></td>
                           
                               <td class="col-mesas"><?php echo intval($p['MESAS']);?></td>
                               <td class="col-votopartido"><?php echo intval($p['NF000']);?></td>
                               <td class="col-votoscandidatos"><?php echo intval($p['NF001']);?></td>
                               <td><?php echo intval($p['NF002']);?></td>
                               <td><?php echo intval($p['NF003']);?></td>
                               <td><?php echo intval($p['NF004']);?></td>
                               <td><?php echo intval($p['NF005']);?></td>
                               <td><?php echo intval($p['NF006']);?></td>
                               <td><?php echo intval($p['NF007']);?></td>
                               <td><?php echo intval($p['NF008']);?></td>
                               <td><?php echo intval($p['NF009']);?></td>
                               <td><?php echo intval($p['NF010']);?></td>
                               <td class="col-blancos"><?php echo intval($p['BLANCOS']);?></td>
                               <td><?php echo intval($p['NULOS']);?></td>
                               <td><?php echo intval($p['TESTIGOS_ASIGNADOS']);?></td>
                             </tr>
                           <?php endforeach;?>
                         </tbody>
                       </table>
                       </div>


                      <!-- TABLA TRANSPOSE: filas = NF000..NF010, columnas = ZONAS -->
<?php
// preparar matriz por zona (usar $zonas ya calculado)
$zoneNames = array_map(function($r){ return $r['ZONA']; }, $zonas);
$zoneMap = [];
foreach($zonas as $z){
    $zoneMap[$z['ZONA']] = $z; // contiene NF000..NF010, BLANCOS, NULOS, MESAS
}
// filas a mostrar
$metrics = [];
for($k=0;$k<=10;$k++){
    $metrics[] = 'NF' . str_pad($k,3,'0',STR_PAD_LEFT);
}
$metrics[] = 'BLANCOS';
$metrics[] = 'NULOS';
$metrics[] = 'VOTOS_NO_MARCADOS';

// evitar duplicados accidentales (preserva orden)
$metrics = array_values(array_unique($metrics));

// calcular totales por zona y totales generales (TOTAL VOTOS NF = suma NF000..NF010)
$zoneTotals = [];
$grandTotals = array_fill_keys(array_merge($metrics, ['TOTAL_VOTOS_NF','TOTAL_VOTOS_VALIDOS','TOTALMESA']), 0);
 
// lista explícita de NF keys
$nfKeys = [];
for($k=0;$k<=10;$k++){
    $nfKeys[] = 'NF' . str_pad($k,3,'0',STR_PAD_LEFT);
}

foreach($allZones as $zn){
    $z = isset($zoneMap[$zn]) ? $zoneMap[$zn] : [];
    $sumNF = 0;
 
    // sumar y asignar cada NFxxx
    foreach($nfKeys as $nf){
        $val = isset($z[$nf]) ? intval($z[$nf]) : 0;
        $zoneTotals[$zn][$nf] = $val;
        $sumNF += $val;
        $grandTotals[$nf] += $val;
    }
    
    // otros campos (blancos, nulos, no marcados)
    foreach (['BLANCOS','NULOS','VOTOS_NO_MARCADOS'] as $m) {
        $val = isset($z[$m]) ? intval($z[$m]) : 0;
        $zoneTotals[$zn][$m] = $val;
        $grandTotals[$m] += $val;
    }
 
    // TOTALMESA por zona (si viene)
    $zoneTotals[$zn]['TOTALMESA'] = isset($z['TOTALMESA']) ? intval($z['TOTALMESA']) : 0;
    $grandTotals['TOTALMESA'] += $zoneTotals[$zn]['TOTALMESA'];
 
    // TOTAL VOTOS NF = suma de NF000..NF010
    $zoneTotals[$zn]['TOTAL_VOTOS_NF'] = $sumNF;
    $grandTotals['TOTAL_VOTOS_NF'] += $sumNF;
 
    // TOTAL VOTOS VALIDOS = TOTALMESA + VT BLANCO (nuevo requisito)
    $zoneTotals[$zn]['TOTAL_VOTOS_VALIDOS'] = $zoneTotals[$zn]['TOTALMESA'] + $zoneTotals[$zn]['BLANCOS'];
    $grandTotals['TOTAL_VOTOS_VALIDOS'] += $zoneTotals[$zn]['TOTAL_VOTOS_VALIDOS'];
}
?>
                      <h4>CONSOLIDADO POR E14 POR ZONAS  BOGOTA</h4>
                      <div class="table-scroll">
                        <table  class="table table-hover table-striped table-bordered table-condensed">
                          <thead>
                            <tr>
                              <th style="min-width:160px; font-size: 12px;">NUESTRA FUERZA</th>
                              <?php foreach($allZones as $zn): ?>
                                <th style="font-size: 10px;">ZONA <?php echo htmlspecialchars($zn); ?></th>
                              <?php endforeach; ?>
                              <th>SubTotal</th>
                            </tr>
                          </thead>
                          <tbody style="font-size: 10px;">
                            <?php  for($k=0;$k<=10;$k++){ 
                                    // etiqueta: si es NF mostrar "CANDIDATO NFxxx"
                                    if (strpos($metrics[$k],'NF') === 0) {
                                        $label = 'CANDIDATO ' . $metrics[$k];
                                    }
                            ?>
                              <tr>
                                <td style="font-weight:700; "><?php echo $label; ?></td>
                                <?php foreach($allZones as $zn): 
                                      $v = isset($zoneTotals[$zn][$metrics[$k]]) ? intval($zoneTotals[$zn][$metrics[$k]]) : 0;
                                ?>
                                  <td class="num"><?php echo $v; ?></td>
                                <?php endforeach; ?>
                                <td class="num" style="font-weight:800;"><?php echo intval($grandTotals[$metrics[$k]]); ?></td>
                              </tr>
                            <?php } ?>

                            <!-- fila: TOTAL VOTOS NF (suma NF000..NF010) -->
                            <tr style="background:#e8f8e8; font-weight:800;">
                              <td>TOTAL VOTOS NF</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['TOTAL_VOTOS_NF']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['TOTAL_VOTOS_NF']); ?></td>
                            </tr>

                            <!-- fila: TOTAL VOTOS MESAS (campo TOTALMESA) -->
                            <tr style="background:#d9edf7; font-weight:800;">
                              <td>TOTAL VOTOS MESAS</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['TOTALMESA']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['TOTALMESA']); ?></td>
                            </tr>

                            <!-- fila: VT BLANCO -->
                            <tr style="background:#fffbe6; font-weight:800;">
                              <td>VT BLANCO</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['BLANCOS']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['BLANCOS']); ?></td>
                            </tr>

                            <!-- fila: VOTOS NULOS -->
                            <tr style="background:#ffecec; font-weight:800;">
                              <td>VOTOS NULOS</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['NULOS']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['NULOS']); ?></td>
                            </tr>

                            <!-- fila: VOTOS NO MARCADO -->
                            <tr style="background:#f7f7f7; font-weight:800;">
                              <td>VOTOS NO MARCADO</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['VOTOS_NO_MARCADOS']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['VOTOS_NO_MARCADOS']); ?></td>
                            </tr>

                            <!-- fila: TOTAL VOTOS VALIDOS (se toma = TOTAL_VOTOS_NF) -->
                            <tr style="background:#e6fff0; font-weight:900;">
                              <td>TOTAL VOTOS VÁLIDOS</td>
                              <?php foreach($allZones as $zn):
                                  $t = intval($zoneTotals[$zn]['TOTAL_VOTOS_VALIDOS']);
                              ?>
                                <td class="num"><?php echo $t; ?></td>
                              <?php endforeach; ?>
                              <td class="num"><?php echo intval($grandTotals['TOTAL_VOTOS_VALIDOS']); ?></td>
                            </tr>

                          </tbody>
                        </table>
                      </div>
                  </div><!-- portlet-body -->
               </div><!-- portlet -->
            </div><!-- span12 -->
         </div><!-- row -->
      </div><!-- container-fluid -->
   </div><!-- page-container -->

<script src="assets/js/jquery-1.8.3.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script>
  var municipios = <?php echo json_encode($municipios); ?>;
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
</script>
</body>
</html>
?>