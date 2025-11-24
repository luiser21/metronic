<?php
// Contenido extraído de monitor_testigos.php para incluir en informe_testigos.php
// No iniciar session ni incluir headers ya que se incluye desde otro archivo

include_once "includes/GestionBD.new.class.php";
$DBGestion = new GestionBD('AGENDAMIENTO');

$candidato = 224; // ID candidato a monitorear
$departamento = isset($_GET['departamento']) ? intval($_GET['departamento']) : 35;
$municipio   = isset($_GET['municipio']) ? intval($_GET['municipio']) : 1145;
$zonaFilter  = isset($_GET['zona']) ? trim($_GET['zona']) : '';

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

// detalle por zona
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

// obtener lista completa de zonas del municipio
$DBGestion->ConsultaArray("SELECT DISTINCT ZONA FROM puestos_votacion WHERE IDMUNICIPIO = ".intval($municipio)." ORDER BY ZONA");
$allZoneRows = $DBGestion->datos;
$allZones = [];
foreach($allZoneRows as $zr){
    $zn = trim($zr['ZONA']);
    if($zn === '') continue;
    $allZones[] = $zn;
}

// construir mapa zona => datos
$zoneMap = [];
foreach($zonas as $z){
    $zoneMap[trim($z['ZONA'])] = $z;
}

// detalle por puesto
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
?>

<style>
   .summary .well{padding:12px;}
   .filters {margin-bottom:12px;}
   .table-scroll{
     overflow-x:auto;
     -webkit-overflow-scrolling:touch;
     margin-bottom:16px;
     border:1px solid #e6e6e6;
     border-radius:4px;
     background:#fff;
     padding:6px;
   }
   .table-scroll .table{ min-width:1200px; }
   .table-scroll thead th{
     position:sticky;
     top:0;
     background:#f7f9fb;
     z-index:3;
     border-bottom:2px solid #ddd;
   }
   .col-mesas{ background:linear-gradient(#f0f8ff,#e8f4ff); font-weight:700; color:#054a7a; text-align:center; }
   .col-votopartido{ background:linear-gradient(#fffef3,#fff4cc); font-weight:800; color:#8a6d00; text-align:right; }
   .col-votoscandidatos{ background:linear-gradient(#f6fff6,#e6ffea); font-weight:800; color:#167a2b; text-align:right; }
   .col-blancos{ background:linear-gradient(#fbfbfb,#f0f0f0); font-weight:700; color:#444; text-align:right; }
   .num { text-align:right; font-family:monospace; padding-right:10px; }
   .table tbody tr:hover{ background:#fff7e6; }
</style>

<div class="portlet box blue">
   <div class="portlet-title">
      <h4><i class="icon-reorder"></i> Monitoreo de carga candidato </h4>
   </div>
   <div class="portlet-body">
       <form class="form-inline filters" method="get">
         <label>Departamento</label>
         <select name="departamento" id="departamento_monitor" class="input-medium">
           <?php foreach($departamentos as $d): ?>
             <option value="<?php echo $d['id'];?>" <?php if($d['id']==$departamento) echo 'selected';?>><?php echo htmlspecialchars($d['nombre']);?></option>
           <?php endforeach;?>
         </select>

         <label style="margin-left:8px">Municipio</label>
         <select name="municipio" id="municipio_monitor" class="input-medium">
           <?php foreach($municipios as $m): if($m['IDDEPARTAMENTO'] != $departamento) continue; ?>
             <option value="<?php echo $m['id'];?>" <?php if($m['id']==$municipio) echo 'selected';?>><?php echo htmlspecialchars($m['nombre']);?></option>
           <?php endforeach;?>
         </select>

         <label style="margin-left:8px">Zona</label>
         <input name="zona" class="input-small" value="<?php echo htmlspecialchars($zonaFilter);?>" placeholder="opcional">

         <button class="btn btn-primary" type="submit" style="margin-left:8px">Filtrar</button>
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

       <!-- TABLA TRANSPOSE: filas = NF000..NF010, columnas = ZONAS -->
<?php
// preparar matriz por zona
$zoneNames = array_map(function($r){ return $r['ZONA']; }, $zonas);
$zoneMap = [];
foreach($zonas as $z){
    $zoneMap[$z['ZONA']] = $z;
}
// filas a mostrar
$metrics = [];
for($k=0;$k<=10;$k++){
    $metrics[] = 'NF' . str_pad($k,3,'0',STR_PAD_LEFT);
}
$metrics[] = 'BLANCOS';
$metrics[] = 'NULOS';
$metrics[] = 'VOTOS_NO_MARCADOS';

$metrics = array_values(array_unique($metrics));

// calcular totales
$zoneTotals = [];
$grandTotals = array_fill_keys(array_merge($metrics, ['TOTAL_VOTOS_NF','TOTAL_VOTOS_VALIDOS','TOTALMESA']), 0);

$nfKeys = [];
for($k=0;$k<=10;$k++){
    $nfKeys[] = 'NF' . str_pad($k,3,'0',STR_PAD_LEFT);
}

foreach($allZones as $zn){
    $z = isset($zoneMap[$zn]) ? $zoneMap[$zn] : [];
    $sumNF = 0;

    foreach($nfKeys as $nf){
        $val = isset($z[$nf]) ? intval($z[$nf]) : 0;
        $zoneTotals[$zn][$nf] = $val;
        $sumNF += $val;
        $grandTotals[$nf] += $val;
    }

    foreach (['BLANCOS','NULOS','VOTOS_NO_MARCADOS'] as $m) {
        $val = isset($z[$m]) ? intval($z[$m]) : 0;
        $zoneTotals[$zn][$m] = $val;
        $grandTotals[$m] += $val;
    }

    $zoneTotals[$zn]['TOTALMESA'] = isset($z['TOTALMESA']) ? intval($z['TOTALMESA']) : 0;
    $grandTotals['TOTALMESA'] += $zoneTotals[$zn]['TOTALMESA'];

    $zoneTotals[$zn]['TOTAL_VOTOS_NF'] = $sumNF;
    $grandTotals['TOTAL_VOTOS_NF'] += $sumNF;

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

             <tr style="background:#e8f8e8; font-weight:800;">
               <td>TOTAL VOTOS NF</td>
               <?php foreach($allZones as $zn):
                   $t = intval($zoneTotals[$zn]['TOTAL_VOTOS_NF']);
               ?>
                 <td class="num"><?php echo $t; ?></td>
               <?php endforeach; ?>
               <td class="num"><?php echo intval($grandTotals['TOTAL_VOTOS_NF']); ?></td>
             </tr>

             <tr style="background:#d9edf7; font-weight:800;">
               <td>TOTAL VOTOS MESAS</td>
               <?php foreach($allZones as $zn):
                   $t = intval($zoneTotals[$zn]['TOTALMESA']);
               ?>
                 <td class="num"><?php echo $t; ?></td>
               <?php endforeach; ?>
               <td class="num"><?php echo intval($grandTotals['TOTALMESA']); ?></td>
             </tr>

             <tr style="background:#fffbe6; font-weight:800;">
               <td>VT BLANCO</td>
               <?php foreach($allZones as $zn):
                   $t = intval($zoneTotals[$zn]['BLANCOS']);
               ?>
                 <td class="num"><?php echo $t; ?></td>
               <?php endforeach; ?>
               <td class="num"><?php echo intval($grandTotals['BLANCOS']); ?></td>
             </tr>

             <tr style="background:#ffecec; font-weight:800;">
               <td>VOTOS NULOS</td>
               <?php foreach($allZones as $zn):
                   $t = intval($zoneTotals[$zn]['NULOS']);
               ?>
                 <td class="num"><?php echo $t; ?></td>
               <?php endforeach; ?>
               <td class="num"><?php echo intval($grandTotals['NULOS']); ?></td>
             </tr>

             <tr style="background:#f7f7f7; font-weight:800;">
               <td>VOTOS NO MARCADO</td>
               <?php foreach($allZones as $zn):
                   $t = intval($zoneTotals[$zn]['VOTOS_NO_MARCADOS']);
               ?>
                 <td class="num"><?php echo $t; ?></td>
               <?php endforeach; ?>
               <td class="num"><?php echo intval($grandTotals['VOTOS_NO_MARCADOS']); ?></td>
             </tr>

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

<script>
  var municipiosMonitor = <?php echo json_encode($municipios); ?>;
  $('#departamento_monitor').on('change', function(){
      var did = $(this).val();
      var opts = '';
      municipiosMonitor.forEach(function(m){
          if (String(m.IDDEPARTAMENTO) === String(did)) {
              opts += '<option value="'+m.id+'">'+m.nombre+'</option>';
          }
      });
      $('#municipio_monitor').html(opts);
  });
</script>
