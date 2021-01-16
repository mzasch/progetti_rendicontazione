<!DOCTYPE html>
<?php
  session_start();
  require('env.php');
  include_once('google_oauth_config.php');
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
                    <div id="loginInfo" class="cols-sm-2 text-center" >
                        <?php include_once('renderUserInfo.php') ?>
                        <p>Non sei tu? <a href="<?php echo $redirect_uri ?>?logout">Esci</a></p>
                    </div>
                    <div id="tabs">
                      <ul>
                        <li><a href="#tabs-1">Ore inserite</a></li>
                        <li><a href="#tabs-2">Aggiungi nuova ora</a></li>
                        <li><a href="#tabs-3">Test</a></li>
                      </ul>
                      <div id="tabs-1">
                          <div id="jsGrid"></div>
                      </div>
                      <div id="tabs-2">
                          <?php include_once('rendiForm.php'); ?>
                      </div>
                      <div id="tabs-3">
                      </div>
                    </div>
                <?php else: ?>
					<form action="<?php echo $googleAuthUrl; ?>" method="post">
						<button type="submit" class="loginBtn loginBtn--google">Login with Google</button>
					</form>
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
            if(0 <= d && d < 10) return "0" + d.toString();
            if(-10 < d && d < 0) return "-0" + (-1*d).toString();
            return d.toString();
        }

        function ConvertToMySqlString(d){
	        return d.getUTCFullYear() + "-"
                    + twoDigits(1 + d.getUTCMonth()) + "-"
                    + twoDigits(d.getUTCDate()) + " "
                    + twoDigits(d.getUTCHours()) + ":"
                    + twoDigits(d.getUTCMinutes()) + ":"
                    + twoDigits(d.getUTCSeconds());
        }

		$.datetimepicker.setLocale('it');
		$('#sData').datetimepicker({inline:true,step:10,});

		$("#form").submit( function(eventObj) {
			$(this).find("input[name=data]").remove();
			var newData = $('#sData').datetimepicker('getValue');
			var data = ConvertToMySqlString(newData);
		    $("<input />").attr("type", "hidden")
        	.attr("name", "data")
          	.attr("value", data)
          	.appendTo("#form");
		    return true;
	  	});

        $("#tabs").tabs();
	</script>
</body>
</html>
