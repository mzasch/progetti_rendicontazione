<?php

include "../models/DocentiRepository.php";

$docenti = new DocentiRepository();

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $docenti->getAll();
        break;
}

header("Content-Type: application/json");
echo json_encode($result);

?>
