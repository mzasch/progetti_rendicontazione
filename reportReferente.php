<?php
    session_start();
    require_once('env.php');
    require_once('reportProgetto.php');

    if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
        $currentUser = $_SESSION['loggedEmail'];
        $connection = mysqli_connect($host, $user, $password, $dbname)
                      or die('Something went horribly wrong with the connection' . mysqli_connect_error());

        $query_ref = "SELECT rd.id as refid ".
                    "FROM rend_docenti rd ".
                    "WHERE rd.email = '$currentUser'";

        if($ref_docente = mysqli_query($connection, $query_fs)) {
            if($ref_docente -> num_rows != 0){
                $res = mysqli_fetch_assoc($ref_docente);

                $progetti_ref = "SELECT rp.id, rp.nome_progetto ".
                              "FROM `rend_progetti` rp ".
                              "WHERE rp.referente = " . $res["refid"] . " " .
                              "ORDER BY rp.nome_progetto";

                if(!$progetti = mysqli_query($connection, $progetti_fs)) {
                    echo "<div class='error'>";
                    echo "<p>Errore nel recupero dei dati dei progetti</p>";
                    echo "<p>Errno: " . $connection -> errno . "</p>";
                    echo "<p>Error: " . $connection -> error . "</p>";
                    echo "</div>";
                    exit;
                }
            }
        }
?>

<div id='reports'>
    <?php while ($res = mysqli_fetch_assoc($progetti)) { ?>
    <div class='report-progetto'>
        <h3><?php echo $res['nome_progetto'] ?></h3>
        <?php renderProgetto($res['id']); ?>
    </div>
    <?php } } ?>
</div>
