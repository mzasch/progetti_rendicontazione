<?php

require "Progetto.php";

class ProgettiRepository {
    protected $db;

    public function __construct() {
        require '../env.php';
        $this->db = mysqli_connect($host, $user, $password, $dbname);
    }

    private function read($row) {
        $result = new Progetto();
        $result->id = intval($row["id_progetto"]);
        $result->nome_progetto = $row["nome_progetto"];
        $result->obiettivi = $row["obiettivi"];
        $result->referente = $row["referente"];
        return $result;
    }

    public function getAll() {
        $sql = "SELECT * FROM rend_progetti";
        $progetti = mysqli_query($this->db, $sql);

        $result = array();
        if($progetti && $progetti -> num_rows != 0){
            while($res = mysqli_fetch_assoc($progetti)){
                array_push($result, $this->read($res));
            }
        }
        return $result;
    }
}

?>
