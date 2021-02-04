<?php
  session_start();
  require_once('env.php');
  $conn = mysqli_connect($host, $user, $password, $dbname)
          or die('Something went horribly wrong with the connection' . mysqli_connect_error());
  if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
      $result = "";

      if( !empty($_POST['id']) )
      {
          $sql = "DELETE FROM rend_orerendicontate WHERE id = ?";

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
