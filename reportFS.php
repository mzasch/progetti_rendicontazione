<?php
    session_start();
    require_once('env.php');
    require_once('reportProgetto.php');

    if (isset($_SESSION['loggedEmail']) && $_SESSION['loggedEmail']) {
        $currentUser = $_SESSION['loggedEmail'];
        $connection = mysqli_connect($host, $user, $password, $dbname)
                      or die('Something went horribly wrong with the connection' . mysqli_connect_error());

        $query_fs = "SELECT rfs.id as fsid ".
                    "FROM rend_funzioni_strumentali rfs ".
                    "JOIN rend_docenti rd ON rfs.docente = rd.id ".
                    "WHERE rd.email = '$currentUser'";

        if($fs_docente = mysqli_query($connection, $query_fs)) {
            if($fs_docente -> num_rows != 0){
                $res = mysqli_fetch_assoc($fs_docente);

                $progetti_fs = "SELECT rp.id, rp.nome_progetto ".
                              "FROM `rend_progetti` rp ".
                              "WHERE rp.fs_referente = " . $res["fsid"] . " " .
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
    <?php if ($progetti->num_rows === 0): ?>
        <p>Nessun progetto presente</p>
    <?php else: ?>
        <?php while ($res = mysqli_fetch_assoc($progetti)) { ?>
        <div class='report-progetto'>
            <h3><?php echo $res['nome_progetto'] ?></h3>
            <?php renderProgetto($res['id']); ?>
        </div>
        <?php } ?>
    <?php endif ?>
<?php } ?>
</div>
