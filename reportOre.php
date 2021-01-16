<?php
    session_start();
    require('env.php');
    // $_SESSION['loggedEmail']

    $connection = mysqli_connect($host, $user, $password, $dbname)
                  or die('Something went horribly wrong with the connection' . mysqli_connect_error());

    $query_docenti  = "SELECT d.id, d.nome, d.cognome FROM rend_docenti d " .
                      "ORDER BY d.cognome";

    if(!$docenti = mysqli_query($connection,$query_docenti)) {
      echo "Something went horribly wrong with the query \"docenti\"\n";
      echo "Errno: " . $connection -> errno . "\n";
      echo "Error: " . $connection -> error . "\n";
      exit;
    }

    if ($docenti -> num_rows === 0) {
      echo "Something went horribly wrong with the query \"docenti\"\n";
      echo "Errno: " . $connection -> errno . "\n";
      echo "Error: " . $connection -> error . "\n";
      exit;
    }

    $categories = array();
    $result= mysql_query("SELECT category_id, product_name  FROM `table` GROUP BY `catagory_id` ORDER BY `catagory_id`");
    while($row = mysql_fetch_assoc($result)){
        $categories[$row['category_id']][] = $row['product_name'];
    }

    // any type of outout you like
    foreach($categories as $key => $category){
        echo $key.'<br/>';
        foreach($category as $item){
            echo $item.'<br/>';
        }
    }
?>
