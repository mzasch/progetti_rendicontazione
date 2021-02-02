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
            echo "<p><b>" . $res['cognome'] . " " . $res['nome'] . "</b></p>";
            echo "<p>Ruoli:</p>";
            echo "<ul>";
                if($IsStaff)
                    echo "<li>Staff</li>";
                if($IsReferente)
                    echo "<li>Referente progetto</li>";
                if($IsFS)
                    echo "<li>Funzione Strumentale</li>";
            echo "</ul>";
            echo "<p>Non sei tu? <a href='$redirect_uri?logout'>Esci</a></p>";
            echo "</div>";
        }
    }
 ?>
