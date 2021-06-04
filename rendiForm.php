<?php
session_start();

if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
  $currentUser = $_SESSION['loggedEmail'];
  $connection = mysqli_connect($host, $user, $password, $dbname)
                or die('Something went horribly wrong with the connection' . mysqli_connect_error());

  $query_progetti = "SELECT p.id, p.nome_progetto " .
                    "FROM rend_progetti p " .
                    "JOIN rend_docenti_progetti rdp ON rdp.progetti_id = p.id " .
                    "JOIN rend_docenti exe_d ON rdp.docenti_id = exe_d.id " .
                    "WHERE exe_d.email = '$currentUser'" .
                    "ORDER BY p.nome_progetto";

  if(!$progetti = mysqli_query($connection,$query_progetti)) {
      echo "<div class='error'>";
      echo "<p>Errore nel recupero dei dati dei progetti</p>";
      echo "<p>Errno: " . $connection -> errno . "</p>";
      echo "<p>Error: " . $connection -> error . "</p>";
      echo "</div>";
      exit;
  }

  if ($progetti -> num_rows === 0 ) {
      echo "<div class='error'>";
      echo "<p>Nessun progetto presente</p>";
      echo "</div>";
  } else {

?>

<form id="form" class="form-horizontal needs-validation" method="post" action="inviaOre.php" novalidate>
	<div class="form-group">
		<label for="sProgetto" class="cols-sm-2 control-label">Scegli un progetto:</label>
		<div class="cols-sm-10">
			<div class="input-group">
		        <select id='sProgetto' name="progetto" required>
				<option value='' selected></option>
				<?php
		            while($res = mysqli_fetch_assoc($progetti)) {
			            $nome_progetto = $res['nome_progetto'];
				        echo "<option value='" . $res['id'] . "'>" . $nome_progetto . "</option>\n";
					}
		        ?>
		        </select>
                <div class="invalid-feedback">
                    Seleziona un progetto dall'elenco.
                </div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="sData" class="cols-sm-2 control-label">Scegli la data e l'ora d'inizio dell'attivit&agrave;</label>
		<div class="cols-sm-10">
			<div class="input-group">
				<input type="text" class="form-control" name="datapicker" id="sData" required />
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="sOre" class="cols-sm-2 control-label">Inserisci la durata dell'attivit&agrave;</label>
		<div class="cols-sm-10">
			<div class="input-group">
		        <input id="sOre" type = "number" name = "nOre" class="form-control" min='0.5' max='8' step='0.5' required/>
                <div class="invalid-feedback">
                    Inserisci il numero di ore svolte.
                </div>
            </div>
		</div>
	</div>

	<div class="form-group">
		<label for="sTipoOre" class="cols-sm-2 control-label">Tipo delle ore svolte:</label>
		<div class="cols-sm-10">
			<div class="input-group">
		        <select id='sTipoOre' name="tipoOre" class="form-control" required>
                    <option value='5'>Progettazione - Ore retribuite</option>
					<option value='6'>Progettazione - Ore in obbligo</option>
                    <option value='1'selected>Realizzazione - Docenza retribuita</option>
                    <option value='2'>Realizzazione - Docenza in obbligo</option>
					<option value='3'>Realizzazione - Assistenza/Tutoraggio retribuita</option>
					<option value='4'>Realizzazione - Assistenza/Tutoraggio in obbligo</option>
				</select>
                <div class="invalid-feedback">
                    Seleziona la tipologia di ore.
                </div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<input type="submit" class="btn btn-success btn-lg btn-block login-button" value="Registra l'attivit&agrave;" />
	</div>
</form>

<?php
}
}
?>
