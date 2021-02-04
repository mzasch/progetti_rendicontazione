<?php
  session_start();
  require_once('env.php');
  $conn = mysqli_connect($host, $user, $password, $dbname)
          or die('Something went horribly wrong with the connection' . mysqli_connect_error());
  if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
      $result = "";

      $update_values = array();
      if(!empty($_POST["data"]))
          $update_values[] = "dataOra='".$_POST["data"] ." ". $_POST["ora"]."'";

      if(!empty($_POST["nOre"]))
          $update_values[] = "nOre='".$_POST["nOre"]."'";

      if(!empty($_POST["tipologiaOre"]))
          $update_values[] = "tipologiaOre=".$_POST["tipologiaOre"];

      $update_values_imploded = implode(', ', $update_values);

      if( !empty($update_values) )
      {
          $sql = "UPDATE rend_orerendicontate SET $update_values_imploded WHERE id = ?";

          if (!$q = mysqli_prepare($conn, $sql)) {
            $result = "Prepare failed: (" . mysqli_errno($conn) . ") " . implode(" ~~ ", mysqli_error_list($conn));
          }

          if (!$q->bind_param("i", $_POST['id'])) {
            $result = "Binding parameters failed: (" . $q->errno . ") " . $q->error;
          }
          if (!$q->execute()) {
              $result = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
      }

      header("Content-Type: application/json");
      echo json_encode($result);
  }
?>
