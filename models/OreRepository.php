<?php

include_once "Ora.php";

class OreRepository {
    protected $db;

    public function __construct() {
        require '../env.php';
        $this->db = mysqli_connect($host, $user, $password, $dbname);
    }

    private function read($row) {
        $result = new Ora();
        $result->id = intval($row["id"]);
        $result->docente = $row["docente"];
        $result->progetto = $row["progetto"];
        $result->data = $row["data"];
        $result->ora = $row["ora"];
        $result->nOre = $row["nOre"];
        $result->tipologiaOre = intval($row["tipologiaOre"]);
        $result->concluso = intval($row["concluso"]);
        return $result;
    }

    public function getById($id) {
        $sql = "SELECT * FROM rend_orerendicontate WHERE id = ?";
        $q = $this->db->prepare($sql);
        $q->bind_param("i", $id);
        $q->execute();
        $rows = $q->fetchAll();
        return $this->read($rows[0]);
    }

    public function getAll($filter) {
        $progetto = "'%" . $filter["progetto"] . "%'";
        $docente = "'%" . $filter["docente"] . "%'";

        $sql = "SELECT ro.id, DATE(ro.dataOra) AS `data`, TIME(ro.dataOra) AS `ora`, ro.nOre, ro.tipologiaOre, rp.nome_progetto as progetto, CONCAT_WS(' ', rd.cognome, rd.nome) as docente, rp.concluso " .
                " FROM rend_orerendicontate ro " .
                " JOIN rend_progetti rp ON ro.progetto = rp.id " .
                " JOIN rend_docenti rd ON ro.docente = rd.id " .
                " WHERE rp.nome_progetto LIKE $progetto AND rd.email LIKE $docente " .
                " ORDER BY rp.id";
        $ore = mysqli_query($this->db, $sql);

        $result = array();
        if($ore && $ore -> num_rows != 0){
            while($res = mysqli_fetch_assoc($ore)){
                array_push($result, $this->read($res));
            }
        }
        return $result;
    }

    public function insert($data) {
        $sql = "INSERT INTO rend_orerendicontate (name, age, address, married, country_id) VALUES (:name, :age, :address, :married, :country_id)";
        $q = $this->db->prepare($sql);
        $q->bindParam(":name", $data["name"]);
        $q->bindParam(":age", $data["age"], PDO::PARAM_INT);
        $q->bindParam(":address", $data["address"]);
        $q->bindParam(":married", $data["married"], PDO::PARAM_INT);
        $q->bindParam(":country_id", $data["country_id"], PDO::PARAM_INT);
        $q->execute();
        return $this->getById($this->db->lastInsertId());
    }

    public function update($data) {
        $update_values = array();
        if(!empty($data["data"]))
            $update_values[] = "data='".$data["data"]."'";

        if(!empty($data["ora"]))
            $update_values[] = "ora='".$data["ora"]."'";

        if(!empty($data["nOre"]))
            $update_values[] = "nOre='".$data["nOre"]."'";

        if(!empty($data["tipologiaOre"]))
            $update_values[] = "tipologiaOre=".$data["tipologiaOre"];

        $update_values_imploded = implode(', ', $update_values);

        if( !empty($update_values) )
        {
            $sql = "UPDATE rend_orerendicontate SET $update_values_imploded WHERE id = ?";
            $q = mysqli_prepare($this->db, $sql);
            $q->bind_param("i", $data["id"]);
            $q->execute();
        }
    }

    public function remove($id) {
        $sql = "DELETE FROM rend_orerendicontate WHERE id = ?";
        $q = mysqli_prepare($this->db,$sql);
        $q->bind_param("i", $id);
        $q->execute();
    }

}

?>
