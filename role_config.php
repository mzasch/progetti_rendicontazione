<?php
  session_start();
  require('env.php');
  $IsStaff = false;
  $IsReferente = false;
  $IsFS = false;

  if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
    $connection = mysqli_connect($host, $user, $password, $dbname)
                  or die('Something went horribly wrong with the connection' . mysqli_connect_error());

    $query  = "SELECT rp.id " .
              "FROM rend_progetti rp JOIN rend_docenti rd ON rp.referente = rd.id " .
              "WHERE rd.email = '" . $_SESSION['loggedEmail'] . "'";

    if($dati = mysqli_query($connection, $query))
        $IsReferente = ($dati -> num_rows != 0);

    $query  = "SELECT rfs.id " .
              "FROM rend_funzioni_strumentali rfs JOIN rend_docenti rd ON rfs.docente = rd.id " .
              "WHERE rd.email = '" . $_SESSION['loggedEmail'] . "'";

    if($dati = mysqli_query($connection, $query))
        $IsFS = ($dati -> num_rows != 0);
  }
