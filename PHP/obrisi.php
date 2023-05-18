<?php
session_start();
if (!isset($_SESSION['korisnik'])) {
    header('Location:login.php');
} else if (!isset($_GET['id'])) {
} else {
    $id = $_GET['id'];
    require_once('povezivanjeBP.php');
    //require_once('sviKorisnici.php');

    $izjava = $veza->prepare('DELETE FROM korisnici WHERE id=?');
    $izjava->bind_param('d', $id);
    if ($izjava->execute()) {

        $stranica = $_GET['stranica'];

        header('Location: sviKorisnici.php?obrisano=da');
    } else {
        header('Location: sviKorisnici.php?obrisano=ne');
    }
}
?>

<?php include('footer.php'); ?>