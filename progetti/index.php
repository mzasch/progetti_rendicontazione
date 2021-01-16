<?php

include "../models/ProgettiRepository.php";

$progetti = new ProgettiRepository();

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $progetti->getAll();
        break;
}


header("Content-Type: application/json");
echo json_encode($result);

?>
