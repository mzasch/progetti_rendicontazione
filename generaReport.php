<?php
    session_start();
    require_once __DIR__ . '/vendor/autoload.php';

    require('env.php');
    $connection = mysqli_connect($host, $user, $password, $dbname)
                      or die('Something went horribly wrong with the connection' . mysqli_connect_error());

    const COSTO_PROG = 17.50;
    const COSTO_DOC = 35.00;
    const COSTO_TUT = 17.50;

    function categoria($intType) {
      switch ($intType) {
        case 1:  return "Docenza oltre orario di servizio";
        case 2:  return "Docenza in orario di servizio";
        case 3:  return "Non docenza oltre orario di servizio";
        case 4:  return "Non docenza in orario di servizio";
        case 5:  return "Progettazione oltre orario di servizio";
        case 6:  return "Progettazione in orario di servizio";
        default: break;
      }
      return "";
    }

    if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
      $currentUser = $_SESSION['loggedEmail'];
      $idProgetto = $_POST['progetto'];

      $query_progetti = '
        SELECT p.nome_progetto, p.codice_bilancio,
          p.ore_progettazione_extra,
          p.ore_realizzazione_doc_extra,
          p.ore_realizzazione_tut_extra,

          SUM(CASE When ore.tipologiaOre = 1 THEN ore.nOre ELSE 0 END ) AS ore_doc_extra_rend,
          SUM(CASE When ore.tipologiaOre = 3 THEN ore.nOre ELSE 0 END ) AS ore_tut_extra_rend,
          SUM(CASE When ore.tipologiaOre = 5 THEN ore.nOre ELSE 0 END ) AS ore_progettazione_rend

          FROM rend_progetti p JOIN rend_orerendicontate ore ON p.id = ore.progetto
          WHERE p.id = '.$idProgetto;

      if(!$dati_progetto = mysqli_query($connection,$query_progetti)) {
          echo "<div class='error'>";
          echo "<p>Errore nel recupero dei dati del progetto</p>";
          echo "<p>Errno: " . $connection -> errno . "</p>";
          echo "<p>Error: " . $connection -> error . "</p>";
          echo "</div>";
          exit;
      }

      if ($dati_progetto -> num_rows === 0) {
          echo "<div class='error'>";
          echo "<p>Nessun progetto presente</p>";
          echo "</div>";
          exit;
      }

      $query_ore = '
        SELECT
          IF(d.id IS NULL, NULL, d.cognome) Cognome,
          IF(d.id IS NULL, NULL, d.nome) Nome,

          ANY_VALUE(ore_progettista_extra) AS ore_progettista_extra,
          ANY_VALUE(ore_realizzatore_doc_extra) AS ore_realizzatore_doc_extra,
          ANY_VALUE(ore_realizzatore_tut_extra) AS ore_realizzatore_tut_extra,

          SUM(CASE When ore.tipologiaOre = 1 THEN ore.nOre ELSE 0 END ) AS Doc_Retribuita,
          SUM(CASE When ore.tipologiaOre = 3 THEN ore.nOre ELSE 0 END ) AS Tut_Retribuita,
          SUM(CASE When ore.tipologiaOre = 5 THEN ore.nOre ELSE 0 END ) AS Prog_Retribuita,

          SUM(CASE When ore.tipologiaOre % 2 = 1 THEN ore.nOre ELSE 0 END ) AS Totale_ore_retribuite

        FROM rend_orerendicontate ore JOIN rend_progetti p ON ore.progetto = p.id
                                      JOIN rend_docenti d ON ore.docente = d.id
                                      JOIN rend_docenti_progetti rdp
                                    	ON ore.progetto = rdp.progetti_id AND ore.docente = rdp.docenti_id
        WHERE ore.progetto = '.$idProgetto.'
        GROUP BY d.id
      ';

      if(!$ore = mysqli_query($connection,$query_ore)) {
          echo "<div class='error'>";
          echo "<p>Errore nel recupero delle ore del progetto</p>";
          echo "<p>Errno: " . $connection -> errno . "</p>";
          echo "<p>Error: " . $connection -> error . "</p>";
          echo "</div>";
          exit;
      }

      if ($ore -> num_rows === 0) {
          echo "<div class='error'>";
          echo "<p>Nessuna ora presente</p>";
          echo "</div>";
          exit;
      }

      $mpdf = new \Mpdf\Mpdf([
        'tempDir' => __DIR__ . '/tempPdf',
        'format' => 'A4-L',
        'setAutoTopMargin' => 'pad',
        'margin_top' => 5,
        'margin_footer' => 5,

      ]);
      $mpdf->shrink_tables_to_fit = 1;

      // Define the Headers before writing anything so they appear on the first page
      $mpdf->SetHTMLHeader('
      <table id="header-table">
          <tr>
              <td width="10%"><img src="img/logo_left.jpg" /></td>
              <td width="80%">
                <p>ISTITUTO TECNICO TECNOLOGICO "Giacomo Chilesotti"</p>
                <p>Elettronica ed Elettrotecnica-Informatica e Telecomunicazioni-Trasporti e Logistica</p>
              </td>
              <td width="10%"><img src="img/logo_right.jpg" /></td>
          </tr>
      </table>');

      $dati_prog = mysqli_fetch_assoc($dati_progetto);
      $intestazione = '<h1>'.$dati_prog['codice_bilancio'].' - '.$dati_prog['nome_progetto'].'</h1>';

      $tot_previste = floatval($dati_prog['ore_progettazione_extra']) +
                      floatval($dati_prog['ore_realizzazione_doc_extra']) +
                      floatval($dati_prog['ore_realizzazione_tut_extra']);

      $lordo_prog_prev = (round(floatval($dati_prog['ore_progettazione_extra'])) * COSTO_PROG)+
                         (round(floatval($dati_prog['ore_realizzazione_doc_extra'])) * COSTO_DOC)+
                         (round(floatval($dati_prog['ore_realizzazione_tut_extra'])) * COSTO_TUT);

      $ottoecinquanta_prog_prev = $lordo_prog_prev * 0.085;
      $ventiquattroeventi_prog_prev = $lordo_prog_prev * 0.2420;

      $lordo_stato_prog_prev = $lordo_prog_prev + $ottoecinquanta_prog_prev + $ventiquattroeventi_prog_prev;

      $tot_rendicontate = floatval($dati_prog['ore_progettazione_rend']) +
                          floatval($dati_prog['ore_doc_extra_rend']) +
                          floatval($dati_prog['ore_tut_extra_rend']);

      $tot_arrotondate = round(floatval($dati_prog['ore_progettazione_rend'])) +
                         round(floatval($dati_prog['ore_doc_extra_rend'])) +
                         round(floatval($dati_prog['ore_tut_extra_rend']));

      $lordo_prog_rend = (round(floatval($dati_prog['ore_progettazione_rend'])) * COSTO_PROG)+
                         (round(floatval($dati_prog['ore_doc_extra_rend'])) * COSTO_DOC)+
                         (round(floatval($dati_prog['ore_tut_extra_rend'])) * COSTO_TUT);

      $ottoecinquanta_prog_rend = $lordo_prog_rend * 0.085;
      $ventiquattroeventi_prog_rend = $lordo_prog_rend * 0.2420;

      $lordo_stato_prog_rend = $lordo_prog_rend + $ottoecinquanta_prog_rend + $ventiquattroeventi_prog_rend;

      $generale = '
        <h2>Riepilogo generale</h2>
        <table id="progetto-generale" >
          <thead>
          <tr>
              <th><br></th>
              <th><b>Progettazione</b></th>
              <th><b>Docenza</b></th>
              <th><b>Non docenza</b></th>
              <th><b>Totale Ore</b></th>

      	<th><b>Lordo progetto</b></th>
              <th><b>8.50%</b></th>
              <th><b>24.20%</b></th>
              <th><b>Lordo Stato</b></th>
          </tr>
          </thead>
          <tbody>
          <tr>
              <td class="table-row">Preventivate</td>
              <td class="single-hour">'.$dati_prog['ore_progettazione_extra'].'</td>
              <td class="single-hour">'.$dati_prog['ore_realizzazione_doc_extra'].'</td>
              <td class="single-hour">'.$dati_prog['ore_realizzazione_tut_extra'].'</td>
              <td class="single-hour">'.$tot_previste.'</td>

              <td class="money">'.number_format($lordo_prog_prev, 2, ',', '.').' &euro;</td>
              <td class="money">'.number_format($ottoecinquanta_prog_prev, 2, ',', '.').' &euro;</td>
              <td class="money">'.number_format($ventiquattroeventi_prog_prev, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($lordo_stato_prog_prev, 2, ',', '.').' &euro;</td>
              </tr>
              <tr>
                  <td class="table-row">Rendicontate</td>
                  <td class="hour">'.$dati_prog['ore_progettazione_rend'].'</td>
                  <td class="hour">'.$dati_prog['ore_doc_extra_rend'].'</td>
                  <td class="hour">'.$dati_prog['ore_tut_extra_rend'].'</td>
                  <td class="hour">'.$tot_rendicontate.'</td>
              </tr>
              <tr>
                  <td class="table-row">Arrotondamento rendicontate</td>
                  <td class="hour">'.round($dati_prog['ore_progettazione_rend']).'</td>
                  <td class="hour">'.round($dati_prog['ore_doc_extra_rend']).'</td>
                  <td class="hour">'.round($dati_prog['ore_tut_extra_rend']).'</td>
                  <td class="hour">'.$tot_arrotondate.'</td>

                  <td class="money">'.number_format($lordo_prog_rend, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($ottoecinquanta_prog_rend, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($ventiquattroeventi_prog_rend, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($lordo_stato_prog_rend, 2, ',', '.').' &euro;</td>
              </tr>
              </tbody>
          </table>
          ';

          $docenti_gen = '
          <h2>Riepilogo per docenti</h2>
          <table id="progetto-docenti" width="100%" cellspacing="0" border="0">
              <thead>
              <tr>
                  <th height="20" align="left"><b>Docente</b></th>
                  <th align="center"><b>Prog. Prev.</b></th>
                  <th align="center"><b>Prog. Rend.</b></th>

                  <th align="center"><b>Doc. Prev.</b></th>
                  <th align="center"><b>Doc. Rend.</b></th>

                  <th align="center"><b>Non doc. Prev.</b></th>
                  <th align="center"><b>Non doc. Rend.</b></th>

                  <th align="center"><b>Totale Ore</b></th>

          	<th align="center"><b>Lordo dip.</b></th>
                  <th align="center"><b>8.50%</b></th>
                  <th align="center"><b>24.20%</b></th>

                  <th align="center"><b>Lordo Stato</b></th>
              </tr>
              </thead>
              <tbody>
          ';

          while($res = mysqli_fetch_assoc($ore)) {
            $lordo_dip = (floatval($res['Prog_Retribuita']) * COSTO_PROG)+
                         (floatval($res['Doc_Retribuita']) * COSTO_DOC)+
                         (floatval($res['Tut_Retribuita']) * COSTO_TUT);

            $ottoecinquanta_dip = $lordo_dip * 0.085;
            $ventiquattroeventi_dip = $lordo_dip * 0.2420;

            $lordo_stato_dip = $lordo_dip + $ottoecinquanta_dip + $ventiquattroeventi_dip;

            $docenti_gen = $docenti_gen.
            '
              <tr>
                  <td class="table-row">'.$res['Cognome'].' '.$res['Nome'].'</td>
                  <td class="hour">'.$res['ore_progettista_extra'].'</td>
                  <td class="hour">'.$res['Prog_Retribuita'].'</td>

                  <td class="hour">'.$res['ore_realizzatore_doc_extra'].'</td>
                  <td class="hour">'.$res['Doc_Retribuita'].'</td>

                  <td class="hour">'.$res['ore_realizzatore_tut_extra'].'</td>
                  <td class="hour">'.$res['Tut_Retribuita'].'</td>

                  <td class="hour">'.$res['Totale_ore_retribuite'].'</td>

                  <td class="money">'.number_format($lordo_dip, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($ottoecinquanta_dip, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($ventiquattroeventi_dip, 2, ',', '.').' &euro;</td>
                  <td class="money">'.number_format($lordo_stato_dip, 2, ',', '.').' &euro;</td>
              </tr>
            ';
          }

          $docenti_gen = $docenti_gen.'
              </tbody>
          </table>
          ';

          $query_ore_docenti = '
              SELECT d.id, d.cognome, d.nome, ore.dataOra, ore.nOre, ore.tipologiaOre
              FROM `rend_orerendicontate` ore JOIN `rend_docenti` d ON ore.docente = d.id
              WHERE ore.progetto = '.$idProgetto.'
              ORDER BY d.id, ore.dataOra';

          if(!$dati_dettagli = mysqli_query($connection,$query_ore_docenti)) {
              echo "<div class='error'>";
              echo "<p>Errore nel recupero dei dettagli</p>";
              echo "<p>Errno: " . $connection -> errno . "</p>";
              echo "<p>Error: " . $connection -> error . "</p>";
              echo "</div>";
              exit;
          }

          if ($dati_dettagli -> num_rows === 0) {
              echo "<div class='error'>";
              echo "<p>Nessun dettaglio presente</p>";
              echo "</div>";
              exit;
          }

          $groupedResult = array();
          $i = 0;
          $prev_res = null;
          while ($res = mysqli_fetch_assoc($dati_dettagli)) {
            if ($prev_res == null || $prev_res != $res['id']) {
              $prev_res = $res['id'];
              $i = 0;
            }
            $groupedResult[$res['id']][$i] = $res;
            $i++;
          }

          $docenti_dett = '<h2>Dettaglio per docenti</h2>';

          foreach($groupedResult as $doc_id => $ore_rend) {
            $docenti_dett = $docenti_dett . '
              <h3>'.$ore_rend[0]['cognome'].' '.$ore_rend[0]['nome'].'</h3>
                <table id="dettaglio-docente-'.$doc_id.'" class="dettaglio-docente">
                  <thead>
                    <tr>
                      <th ><b>Data - Ora</b></th>
                      <th ><b>Numero Ore</b></th>
                      <th ><b>Tipo Ore</b></th>
                    </tr>
                  </thead>
                  <tbody>
            ';

            foreach($ore_rend as $singola_ora){
              $dataOra = new DateTime($singola_ora['dataOra']);
              $docenti_dett = $docenti_dett.
              '
                <tr class="'.(($dataOra->format("H") < 14) ? "mattina" : "").'">
                  <td class="detail-time">'.$dataOra->format("d-m-Y H:i").'</td>
                  <td class="detail-hour">'.$singola_ora['nOre'].'</td>
                  <td class="detail-type">'.categoria(intval($singola_ora['tipologiaOre'])).'</td>
                </tr>
              ';
            }

            $docenti_dett = $docenti_dett.'
                </tbody>
              </table>
            ';
          }

          $stylesheet = file_get_contents('css/pdfstyle.css');
          $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

          $mpdf->WriteHTML($intestazione, \Mpdf\HTMLParserMode::HTML_BODY);
          $mpdf->WriteHTML($generale, \Mpdf\HTMLParserMode::HTML_BODY);
          $mpdf->WriteHTML($docenti_gen, \Mpdf\HTMLParserMode::HTML_BODY);
          $mpdf->AddPage();
          $mpdf->WriteHTML($docenti_dett, \Mpdf\HTMLParserMode::HTML_BODY);

          $mpdf->Output('scheda_progetto.pdf', 'D');
//          $mpdf->Output();

      }
