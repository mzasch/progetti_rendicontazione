<?php
  session_start();
  require('env.php');
  $IsStaff = false;
  $IsReferente = false;

  if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {

    $connection = mysqli_connect($host, $user, $password, $dbname)
                  or die('Something went horribly wrong with the connection' . mysqli_connect_error());

    $query_docenti  = "SELECT rp.id " .
                      "FROM rend_progetti rp JOIN rend_docenti rd ON rp.docente = rd.id " .
                      "JOIN rend_funzioni_strumentali rfs ON rp.fs_referente = rfs.id " .
                      "WHERE rd.email = '" . $_SESSION['loggedEmail'] . "' AND rd.id = rfs.docente";
    if($dati_docente = mysqli_query($connection,$query_docenti) && ($dati_docente -> num_rows != 0)) {
      $currentId = (mysqli_fetch_assoc($dati_docente)['id']);
      $query_progetti  = "SELECT * FROM rend_progetti rp WHERE rp.referente = $currentId";

      if($dati_referente = mysqli_query($connection,$query_progetti) && ($dati_docente -> num_rows != 0))
    }
