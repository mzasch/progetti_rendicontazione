<?php
  session_start();
  require_once('env.php');
  $conn = mysqli_connect($host, $user, $password, $dbname)
          or die('Something went horribly wrong with the connection' . mysqli_connect_error());

  function permessiDocente($conn, $docente, $progetto, $tOre) {
      $query_permessi = "
          SELECT inc.progettista, inc.realizzatore, p.bloccato
          FROM rend_docenti_progetti inc JOIN rend_progetti p ON inc.progetti_id = p.id
          WHERE inc.docenti_id = $docente AND inc.progetti_id = $progetto";

      /*
        Friendly reminder dei codici delle tipologie di ore:
          1-Realizzazione - Docenza retribuita
          2-Realizzazione - Docenza in obbligo
          3-Realizzazione - Assistenza/Tutoraggio retribuita
          4-Realizzazione - Assistenza/Tutoraggio in obbligo
          5-Progettazione - Ore retribuite
          6-Progettazione - Ore in obbligo
      */
      if($permessi_docente = mysqli_query($conn,$query_permessi)) {
          $permessi = mysqli_fetch_assoc($permessi_docente);
          $isProgettista = intval($permessi['progettista']) === 1;
          $isRealizzatore = intval($permessi['realizzatore']) === 1;
          $isBloccato = intval($permessi['bloccato']) === 1;

          return array($isBloccato, (($isProgettista) && ($tOre >= 5)), (($isRealizzatore) && ($tOre <= 4)));
      }
      else
      {
          return array(False, False, False);
      }
  }

  if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
    $query_docenti  = "SELECT d.id FROM rend_docenti d WHERE d.email = '" . $_SESSION['loggedEmail'] . "'";

    if(!$dati_docente = mysqli_query($conn,$query_docenti)) {
      echo "<div class='error'>";
      echo "<p>Errore nel recupero dei dati</p>";
      echo "<p>Errno: " . $connection -> errno . "</p>";
      echo "<p>Error: " . $connection -> error . "</p>";
      echo "</div>";
      exit;
    }

	  $docente = (mysqli_fetch_assoc($dati_docente)['id']);
	  $progetto = $_POST['progetto'];
	  $dataOra = $_POST['data']." ".$_POST['ora'];
	  $nOre = $_POST['nOre'];
	  $tOre = intval($_POST['tipoOre']);

    list($isBloccato, $puoInsProgettazione, $puoInsRealizzazione) = permessiDocente($conn, $docente, $progetto, $tOre);

    $risultato = "";

    if ($isBloccato){
      $risultato = "<p class='error'>Impossibile inserire nuove ore, il progetto è bloccato.</p>";
    }
    else if ($puoInsProgettazione || $puoInsRealizzazione) {
      $insert_ora  = $conn->prepare("INSERT INTO rend_orerendicontate (`docente`,`progetto`,`dataOra`, `nOre`, `tipologiaOre`) VALUES (?,?,?,?,?)");
      $insert_ora->bind_param("issdi", $docente, $progetto, $dataOra, $nOre, $tOre);

  	  if (!$insert_ora->execute()) {
        $risultato = "<p class='error'>Errore di inserimento</p>\n".
        "<p>Errno: " . $conn -> errno . "</p>\n".
        "<p>Error: " . $conn -> error . "</p>\n".
        "</div>";
  	  } else {
  	     $insert_ora->close();
         $risultato = "<p>Rendicontazione inserita con successo.</p>";
      }
    }
    else {
      $risultato = "<p class='error'>Non sei autorizzato ad inserire questo tipo di ore per questo progetto.</p>";
    }
  }
  else
  {
	  $redirect_uri = 'https://www.chilesotti.it/rendicontazione/';
	  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  }

  ?>
<html>
  <head>
	<meta charset="utf-8">
  <title>Risultato Inserimento</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' >
	<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
	<div class="container">
		<div class="row main">
			<div class="panel-heading">
	        	<div class="panel-title text-center">
	            	<h1 class="title">Operazione completata</h1>
	               	<hr />
	            </div>
			</div>
			<div class="main-login main-center">
				<?php echo $risultato; ?>
				<p>Clicca <a href="index.php">qui</a> per tornare indietro.</p>
			</div>
		</div>
	</div>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>
