<?php
session_start();

include "../models/OreRepository.php";

$ore = new OreRepository();

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $ore->getAll(array(
            "progetto" => $_GET["progetto"],
            "docente" => $_SESSION['loggedEmail'],
        ));
        break;

    case "POST":
        $result = $ore->update(array(
            "id" => intval($_POST["id"]),
            "data" => $_POST['data'],
            "ora" => $_POST['ora'],
            "nOre" => $_POST["nOre"],
            "tipologiaOre" => intval($_POST["tipologiaOre"])
        ));
        break;

    case "DELETE":
        parse_str(file_get_contents("php://input"), $_DELETE);

        $result = $ore->remove(intval($_DELETE["id"]));
        break;

}

header("Content-Type: application/json");
echo json_encode($result);

?>
