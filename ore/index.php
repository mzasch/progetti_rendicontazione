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
/*
    case "POST":
        $result = $clients->insert(array(
            "name" => $_POST["name"],
            "age" => intval($_POST["age"]),
            "address" => $_POST["address"],
            "married" => $_POST["married"] === "true" ? 1 : 0,
            "country_id" => intval($_POST["country_id"])
        ));
        break;
*/
    case "PUT":
        parse_str(file_get_contents("php://input"), $_PUT);

        $result = $ore->update(array(
            "id" => intval($_PUT["id"]),
            "dataOra" => $_PUT["dataOra"],
            "nOre" => $_PUT["nOre"],
            "tipologiaOre" => $_PUT["tipologiaOre"]
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
