<?php
    session_start();

    function renderProgetto($progettoId) {
        require('env.php');
        $connection = mysqli_connect($host, $user, $password, $dbname)
                      or die('Something went horribly wrong with the connection' . mysqli_connect_error());

        $progetto = "SELECT CONCAT_WS(' ', rd_rend.Cognome, rd_rend.Nome) AS docente, ".
                      "ro.dataOra, ro.nOre, ro.tipologiaOre ".
                      "FROM `rend_orerendicontate` ro ".
                      "JOIN `rend_docenti` rd_rend ON ro.docente = rd_rend.id ".
                      "JOIN `rend_progetti` rp ON ro.progetto = rp.id ".
                      "WHERE rp.id = $progettoId";

        if(!$ore = mysqli_query($connection, $progetto)) {
            echo "<div class='error'>";
            echo "<p>Errore nel recupero dei dati del progetto $progettoId</p>";
            echo "<p>Errno: " . $connection -> errno . "</p>";
            echo "<p>Error: " . $connection -> error . "</p>";
            echo "</div>";
            exit;
        }


        $tipoOre = array(
            1 => "Realizzazione - Doc. retribuita",
            2 => "Realizzazione - Doc. in obbligo",
            3 => "Realizzazione - A/T retribuita",
            4 => "Realizzazione - A/T in obbligo",
            5 => "Progettazione - Retribuita",
            6 => "Progettazione - In obbligo",
        );

?>
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
          <th scope="row"><?php echo $ora['docente']; ?></th>
          <td><?php echo $ora['dataOra']; ?></td>
          <td><?php echo $ora['nOre']; ?></td>
          <td><?php echo $tipoOre[$ora['tipologiaOre']]; ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <hr />
<?php } ?>
