<?php
session_start();

if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
  $currentUser = $_SESSION['loggedEmail'];
  $connection = mysqli_connect($host, $user, $password, $dbname)
                or die('Something went horribly wrong with the connection' . mysqli_connect_error());

  $query_progetti = "SELECT p.id, p.codice_bilancio, p.nome_progetto, p.concluso " .
                    "FROM rend_progetti p " .
                    "ORDER BY p.codice_bilancio";

  if(!$progetti = mysqli_query($connection,$query_progetti)) {
      echo "<div class='error'>";
      echo "<p>Errore nel recupero dei dati dei progetti</p>";
      echo "<p>Errno: " . $connection -> errno . "</p>";
      echo "<p>Error: " . $connection -> error . "</p>";
      echo "</div>";
      exit;
  }

  if ($progetti -> num_rows === 0) {
      echo "<div class='error'>";
      echo "<p>Nessun progetto presente</p>";
      echo "</div>";
      exit;
  }
}

?>

<form id="form-request-report" class="form-horizontal needs-validation" method="post" action="generaReport.php" novalidate>
	<div class="form-group">
		<label for="sProgetto" class="cols-sm-2 control-label">Scegli un progetto:</label>
		<div class="cols-sm-10">
			<div class="input-group">
		    <select id='sProgetto' name="progetto" class="selectpicker" title="Seleziona un progetto" data-width="50%" required>
				  <?php
		        while($res = mysqli_fetch_assoc($progetti)) {
			        $nome_progetto = $res['codice_bilancio'] . '-' . $res['nome_progetto'];
				      echo "<option ".(intval($res['concluso']) === 1 ? "data-icon='fa-check-circle' style='color:green; text-weight:bold;' " : "")."value='" . $res['id'] . "'>" . $nome_progetto . "</option>\n";
					  }
		      ?>
		      </select>
          <div class="invalid-feedback">Seleziona un progetto dall'elenco.</div>
			</div>
		</div>
	</div>
  <div id="show-confirm-button" class="form-group">
    <p class=lead">Selezionando questa casella, causerai la chiusura del progetto, impedendo l'inserimento di nuove ore. Confermi?</p>
		<div class="form-check">
      <input type="radio" id="radio-draft" name="confirm-close" value="0" checked />
      <label class="form-check-label" for="radio-draft">No, genera una bozza della scheda progetto</label> 
    </div>
		<div class="form-check">
      <input type="radio" id="radio-confirm" name="confirm-close" value="1" />
			<label class="form-check-label" for="radio-confirm">Confermo, chiudi il progetto e genera la scheda definitiva</label>
    </div>
  		<input type="submit" class="btn btn-success btn-lg btn-block login-button" value="Scarica il report" />
    </div>
	</div>
</form>
