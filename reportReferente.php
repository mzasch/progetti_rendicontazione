<?php
    session_start();
    require_once('env.php');
    if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
        $currentUser = $_SESSION['loggedEmail'];
        $connection = mysqli_connect($host, $user, $password, $dbname)
                      or die('Something went horribly wrong with the connection' . mysqli_connect_error());

        $query_dati = "SELECT rp.nome_progetto, ro.progetto AS id_progetto, ".
            "CONCAT_WS(' ', rd_rend.Cognome, rd_rend.Nome) AS docente, ".
            "ro.dataOra, ro.nOre, ro.tipologiaOre ".
            "FROM `rend_orerendicontate` ro ".
            "JOIN `rend_docenti` rd_rend ON ro.docente = rd_rend.id ".
            "JOIN `rend_progetti` rp ON ro.progetto = rp.id ".
            "JOIN `rend_docenti` rd_ref ON rp.referente = rd_ref.id ".
            "WHERE rd_ref.email = '" . $currentUser . "' ".
            "ORDER BY ro.progetto, ro.docente, ro.dataOra";

        if(!$dati = mysqli_query($connection, $query_dati)) {
          echo "Something went horribly wrong with the query \"dati\"\n";
          echo "Errno: " . $connection -> errno . "\n";
          echo "Error: " . $connection -> error . "\n";
          exit;
        }

        $groupedResult = array();
        $i = 0;
        while ($res = mysqli_fetch_assoc($dati)) {
            $groupedResult[$res['nome_progetto']][$i] = $res;
            $i++;
        }
        ksort($groupedResult);
    }

    echo "<div id='reports'>\n";
    foreach($groupedResult as $progetto => $ore) {
?>
<div class='report-progetto'>
    <h3><?php echo $progetto ?></h3>
    <table class="table table-hover table-sm">
      <thead class="thead-dark">
        <tr>
          <th scope="col">Docente</th>
          <th scope="col">Data/Ora</th>
          <th scope="col">Ore</th>
          <th scope="col">Tipologia</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($ore as $ora) { ?>
        <tr>
          <th scope="row"><?php echo $ora['docente'] ?></th>
          <td><?php echo $ora['dataOra'] ?></td>
          <td><?php echo $ora['nOre'] ?></td>
          <td><?php echo $ora['tipologiaOre'] ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <hr />
</div>
<?php } ?>
</div>
