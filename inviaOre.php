<?php
  session_start();
  require('env.php');
  $conn = mysqli_connect($host, $user, $password, $dbname)
          or die('Something went horribly wrong with the connection' . mysqli_connect_error());
  if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	  $insert_ora  = $conn->prepare("INSERT INTO rend_orerendicontate (`docente`,`progetto`,`dataOra`, `nOre`, `tipologiaOre`) VALUES (?,?,?,?,?)");

	  $docente = $_POST['docente'];
	  $progetto = $_POST['progetto'];
	  $dataOra = $_POST['data'] . $_POST['orainizio'];
	  $nOre = $_POST['nOre'];
	  $tOre = $_POST['tipoOre'];

	  $insert_ora->bind_param("sssii", $docente, $progetto, $dataOra, $nOre, $tOre);

	  if (!$insert_ora->execute()) {
		echo "Something went horribly wrong with the insert\n";
		echo "Errno: " . $conn -> errno . "\n";
		echo "Error: " . $conn -> error . "\n";
		exit;
	  }

	  $insert_ora->close();
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
    <title>Inserimento completato</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' > 
	<link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/>
	<link rel="stylesheet" type="text/css" href="style.css" />
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
				<p>Rendicontazione inserita con successo.</p>
				<p>Clicca <a href="index.php">qui</a> per inserire un'altra ora.</p>
			</div>
		</div>
	</div>		
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>
