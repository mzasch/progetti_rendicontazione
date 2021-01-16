<?php

require "Docente.php";

class DocentiRepository {
    protected $db;

    public function __construct() {
        require '../env.php';
        $this->db = mysqli_connect($host, $user, $password, $dbname);
    }

    private function read($row) {
        $result = new Docente();
        $result->id_docente = intval($row["id"]);
        $result->nome_docente = $row["nome"];
        $result->email = $row["email"];
        $result->cognome_docente = $row["cognome"];
        $result->username = $row["username"];
        return $result;
    }

    public function getAll() {
        $sql = "SELECT * FROM rend_docenti";
        $docenti = mysqli_query($this->db, $sql);

        $result = array();
        if($docenti && $docenti -> num_rows != 0){
            while($res = mysqli_fetch_assoc($docenti)){
                array_push($result, $this->read($res));
            }
        }
        return $result;
    }
}

?>
