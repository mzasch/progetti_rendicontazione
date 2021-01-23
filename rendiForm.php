<?php
  $connection = mysqli_connect($host, $user, $password, $dbname)
                or die('Something went horribly wrong with the connection' . mysqli_connect_error());

  $query_docenti  = "SELECT d.id, d.nome, d.cognome FROM rend_docenti d " .
                    "ORDER BY d.cognome";
  $query_progetti = "SELECT p.id, p.nome_progetto, d.cognome ref_cognome, d.nome ref_nome " .
                    "FROM rend_progetti p JOIN rend_docenti d ON p.referente = d.id " .
                    "ORDER BY p.nome_progetto";

  if(!$docenti = mysqli_query($connection,$query_docenti)) {
    echo "Something went horribly wrong with the query \"docenti\"\n";
    echo "Errno: " . $connection -> errno . "\n";
    echo "Error: " . $connection -> error . "\n";
    exit;
  }

  if ($docenti -> num_rows === 0) {
    echo "Something went horribly wrong with the query \"docenti\"\n";
    echo "Errno: " . $connection -> errno . "\n";
    echo "Error: " . $connection -> error . "\n";
    exit;
  }

  if(!$progetti = mysqli_query($connection,$query_progetti)) {
    echo "Something went horribly wrong with the query \"progetti\"\n";
    echo "Errno: " . $connection -> errno . "\n";
    echo "Error: " . $connection -> error . "\n";
    exit;
  }

  if ($progetti -> num_rows === 0) {
    echo "Something went horribly wrong with the query \"progetti\"\n";
    echo "Errno: " . $connection -> errno . "\n";
    echo "Error: " . $connection -> error . "\n";
    exit;
  }

?>

                    <form id="form" class="form-horizontal" method="post" action="inviaOre.php">
<?php /*						<div class="form-group">
        					<label for="sDocente" class="cols-sm-2 control-label">Scegli il docente:</label>
							<div class="cols-sm-10">
								<div class="input-group">
							        <select id='sDocente' name="docente">
									<option value='' selected></option>
									<?php
							            while($res = mysqli_fetch_assoc($docenti)) {
							              $cognome = $res['cognome'];
							              $nome = $res['nome'];
							              echo "<option value='" . $res['id'] . "'>" . $cognome . " " . $nome . "</option>\n";
							            }
							          ?>
									</select>
								</div>
							</div>
						</div>
*/ ?>
						<div class="form-group">
							<label for="sProgetto" class="cols-sm-2 control-label">Scegli un progetto:</label>
							<div class="cols-sm-10">
								<div class="input-group">
							        <select id='sProgetto' name="progetto">
									<option value='' selected></option>
									<?php
							            while($res = mysqli_fetch_assoc($progetti)) {
								            $nome_progetto = $res['nome_progetto'];
									        echo "<option value='" . $res['id'] . "'>" . $nome_progetto . "</option>\n";
										}
							        ?>
							        </select>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="sData" class="cols-sm-2 control-label">Scegli la data e l'ora d'inizio dell'attivit&agrave;</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<input type="text" class="form-control" name="datapicker" id="sData"/>
								</div>
							</div>
						</div>

						<div class="form-group">
        					<label for="sOre" class="cols-sm-2 control-label">Inserisci la durata dell'attivit&agrave;</label>
							<div class="cols-sm-10">
								<div class="input-group">
							        <input id="sOre" type = "number" name = "nOre" class="form-control" min='0.5' max='8' step='0.5'/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="sTipoOre" class="cols-sm-2 control-label">Tipo delle ore svolte:</label>
							<div class="cols-sm-10">
								<div class="input-group">
							        <select id='sTipoOre' name="tipoOre" class="form-control">
										<option value='1' selected>Docenza retribuita</option>
										<option value='2'>Docenza in obbligo</option>
										<option value='3'>Assistenza/Tutoraggio retribuita</option>
										<option value='4'>Assistenza/Tutoraggio in obbligo</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group ">
							<input type="submit" class="btn btn-success btn-lg btn-block login-button" value="Registra l'attivit&agrave;" />
						</div>
					</form>
