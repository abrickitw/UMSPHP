<?php
session_start();
if (!isset($_SESSION["korisnik"])) {
    header("Location:login.php");
    exit;
}

$naslov = "User list";
require_once("header.php");
require_once("adminNav.php");
require_once("povezivanjeBP.php");

handleDeleteUser($veza);

$brojPostranici = 5;
$stranica = empty($_GET['stranica']) ? 1 : (int)$_GET['stranica'];
$ukupnoKorisnika = getTotalUsersCount($veza);
$brojStranica = ceil($ukupnoKorisnika / $brojPostranici);

if ($stranica < 1) {
    $stranica = 1;
} else if ($stranica > $brojStranica - 1) {
    $stranica = $brojStranica;
}

optimizeDatabaseQueries($veza);
$result = getUsers($veza, $stranica, $brojPostranici);



function getUsers($veza, $stranica, $brojPostranici)
{
    $odmak = $brojPostranici * ($stranica - 1);
    $query = "SELECT * FROM korisnici ORDER BY id ASC LIMIT ?, ?";
    $priprema = $veza->prepare($query);
    $priprema->bind_param("ii", $odmak, $brojPostranici);
    $priprema->execute();
    $result = $priprema->get_result();
    return $result;
}

function displayUsers($result)
{
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td><img width="100px" height="100px" src="slike/' . $row['avatar'] . '" /></td>';
        echo '<td>' . $row['korisnik'] . '</td>';
        echo '<td>';
        echo '<a href="obrisi.php?id=' . $row['id'] . '" class="btn btn-danger">Delete</a>';
        echo '<form method="POST" action="urediKorisnika.php">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="display" value="Edit user" class="btn btn-primary">';
        echo '</form>';
        echo '<form method="POST" action="urediSliku.php">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="display" value="Edit image" class="btn btn-primary">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
}

function getTotalUsersCount($veza)
{
    $query = "SELECT COUNT(*) FROM korisnici";
    $result = $veza->query($query);
    if ($result) {
        $field = $result->fetch_row();
        return $field[0];
    } else {
        return 0;
    }
}
function handleDeleteUser($veza)
{
    if (isset($_GET['obrisano'])) {
        if ($_GET['obrisano'] == 'da') {
            echo '<div class="alert alert-success">Uspiješno obrisan korisnik!</div>';
        } else {
            echo '<div class="alert alert-danger">Dogodila se greška kod brisanja korisnika!</div>';
        }
    }
}

function optimizeDatabaseQueries($veza)
{
    if (!$veza->set_charset("utf8")) {
        die("Error loading character set utf8: " . $veza->error);
    }
}

$veza->close();
?>



<div class="container">
    <h1 class="text-center">All users</h1>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php displayUsers($result); ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php if ($stranica > 1) : ?>
                <li class="page-item">
                    <a class="page-link" href="sviKorisnici.php?stranica=<?php echo $stranica - 1; ?>">Prew</a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $brojStranica; $i++) : ?>
                <?php if ($i == $stranica) : ?>
                    <li class="page-item active">
                        <a class="page-link" href="sviKorisnici.php?stranica=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php else : ?>
                    <li class="page-item">
                        <a class="page-link" href="sviKorisnici.php?stranica=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($stranica < $brojStranica) : ?>
                <li class="page-item">
                    <a class="page-link" href="sviKorisnici.php?stranica=<?php echo $stranica + 1; ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php
require_once("footer.php");
?>