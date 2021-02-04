<?php
    session_start();
    require('env.php');
    $conn = mysqli_connect($host, $user, $password, $dbname)
            or die('Something went horribly wrong with the connection' . mysqli_connect_error());

    $sql = "SELECT * FROM rend_docenti WHERE `email` = '$email'";

    if ($docente_info = mysqli_query($conn, $sql)) {
        if ($docente_info->num_rows != 0) {
            $res = mysqli_fetch_assoc($docente_info);
            echo "<div id='profile-pic' class='d-flex align-items-center justify-content-center'>";
            echo "<img src='" . $picture . "' />";
            echo "</div><div id='profile-info'>";
            echo "<h3>" . $res['cognome'] . " " . $res['nome'] . "</h3>";
            echo "<p>Ruoli:</p>";
            echo "<ul>";
            echo "<li class='". ($IsStaff? "role-yes":"role-no") ."'>Staff</li>";
            echo "<li class='". ($IsReferente? "role-yes":"role-no") ."'>Referente progetto</li>";
            echo "<li class='". ($IsFS? "role-yes":"role-no") ."'>Funzione Strumentale</li>";
            echo "</ul>";
            echo "<p>Non sei tu? <a href='$redirect_uri?logout'>Esci</a></p>";
            echo "</div>";
        }
    }
 ?>
