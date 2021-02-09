<!DOCTYPE html>
<?php
    session_start();
    require('env.php');
    include_once('google_oauth_config.php');
    include_once('role_config.php');
?>
<html>
	<head>
		<meta charset="utf-8">
	    <title>ITT Chilesotti - Rendicontazione ore progetti A.S. 2020-21</title>
	    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
                               integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
                               crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
		<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' >
		<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <link rel="stylesheet" type="text/css" href="css/jsgrid.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/jsgrid-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
	</head>

	<body>
		<div class="container">
			<div class="row main">
				<div class="panel-heading">
	               <div class="panel-title text-center">
	               		<h1 class="title">ITT Chilesotti - Rendicontazione ore progetti A.S. 2020-21</h1>
	               		<hr />
	               	</div>
	            </div>
				<div class="main-login main-center">
                <?php if (isset($_SESSION['access_token'])): ?>
                    <label for="loginInfo" class="cols-sm-2 control-label">Utente conesso:</label>
                    <div id="loginInfo" class="cols-sm-2" >
                        <?php include_once('renderUserInfo.php') ?>
                    </div>
                    <div id="tabs">
                      <ul>
                        <li><a href="#tabs-1">Ore inserite</a></li>
                        <li><a href="#tabs-2">Aggiungi nuova ora</a></li>
                        <?php if($IsStaff || $IsReferente): ?>
                        <li><a href="#tabs-3">Report ore progetti</a></li>
                        <?php endif ?>
                        <?php if($IsStaff || $IsFS): ?>
                        <li><a href="#tabs-4">Report ore FS</a></li>
                        <?php endif ?>
                      </ul>
                      <div id="tabs-1">
                          <div id="jsGrid"></div>
                      </div>
                      <div id="tabs-2">
                          <?php include_once('rendiForm.php'); ?>
                      </div>

                      <?php if($IsStaff || $IsReferente): ?>
                      <div id="tabs-3">
                          <?php include_once('reportReferente.php'); ?>
                      </div>
                      <?php endif ?>

                      <?php if($IsStaff || $IsFS): ?>
                      <div id="tabs-4">
                          <?php include_once('reportFS.php'); ?>
                      </div>
                      <?php endif ?>
                    </div>
                <?php else: ?>
                    <div class='row d-flex align-items-center justify-content-center'>
                        <img id='itt-logo' src='img/Logo_chilesotti_294.png' alt='Logo ITT Chilesotti' />
                    </div>
                    <div id="loginButton" class="row d-flex align-items-center justify-content-center" >
    					<form action="<?php echo $googleAuthUrl; ?>" method="post">
    						<button type="submit" class="loginBtn loginBtn--google">Login (@chilesotti.it)</button>
    					</form>
                    </div>
	    		<?php endif ?>
				</div>
			</div>
		</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
   	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>
	<script src="https://apis.google.com/js/platform.js" ></script>
    <script src="js/jsgrid.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/griglia.js"></script>
	<script>
        function twoDigits(d) {
            return d.toString().padStart(2, '0');
        }

        function ConvertToMySqlStringDate(d){
          return `${d.getFullYear()}-${twoDigits(1 + d.getMonth())}-${twoDigits(d.getDate())}`;
        }

        function ConvertToMySqlStringTime(d){
          return `${twoDigits(d.getHours())}:${twoDigits(d.getMinutes())}:00`;
        }

		$.datetimepicker.setLocale('it');
		$('#sData').datetimepicker({inline:true,step:10,});

		$("#form").submit( function(eventObj) {
			$(this).find("input[name=data]").remove();
			var newData = $('#sData').datetimepicker('getValue');
			var data = ConvertToMySqlStringDate(newData);
            var ora = ConvertToMySqlStringTime(newData);
    		    $("<input />").attr("type", "hidden")
                .attr("name", "data")
                .attr("value", data)
                .appendTo("#form");
            $("<input />").attr("type", "hidden")
                .attr("name", "ora")
                .attr("value", ora)
                .appendTo("#form");
    		return true;
	    });

        (function() {
          'use strict';
          window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();

        $("#tabs").tabs();
	</script>
</body>
</html>
