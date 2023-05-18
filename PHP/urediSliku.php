<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!array_key_exists("korisnik", $_SESSION)) {
    header("Location: login.php");
}
if (array_key_exists("id", $_POST)) {
    $id = (int) $_POST["id"];
} else if (array_key_exists("id", $_GET)) {
    $id = (int) $_GET["id"];
} else {
    header("Location: sviKorisnici.php");
    exit();
}

$naslov = "Edit users image";
include('header.php');
include('adminNav.php');
echo "<script> document.getElementById('korisnici').classList.add('active'); </script>";
require_once("PFBC/Form.php");
?>

<div class="container">
    <h1>Edit user image</h1>

    <?php

    require_once("crop.php");
    require_once("povezivanjeBP.php");
    if (isset($_POST['predavanje'])) {

        // Validate user inputs and sanitize them before using them in SQL queries or displaying them on the page
        $staraSlika = filter_input(INPUT_POST, 'avatar', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $korisnikIzmj = filter_input(INPUT_POST, 'korisnikIzmj', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!Form::isValid("slikaKorisnik", false)) {
            $dozvoljeni_MIME = array("image/jpeg", "image/dif", "image/png", "imag/bmp");

            if (!empty($_FILES['slika']['type']) && !in_array($_FILES['slika']['type'], $dozvoljeni_MIME)) {
                Form::setError("slikaKorisnik", "Krivi tip datoteke");
            } else {
                $greska = $_FILES['slika']['error'];
                $upload_greska = array(
                    UPLOAD_ERR_OK => "datoteka je uspjesno predana",
                    UPLOAD_ERR_INI_SIZE => "1",
                    UPLOAD_ERR_FORM_SIZE => "2",
                    UPLOAD_ERR_PARTIAL => "3",
                    UPLOAD_ERR_NO_TMP_DIR => "4",
                    UPLOAD_ERR_CANT_WRITE => "5",
                    UPLOAD_ERR_EXTENSION => "6"
                );

                if ($greska > 0) {
                    Form::setError("slikaKorisnik", $upload_greska[$greska]);
                } else {
                    $privremena_datoteka = $_FILES['slika']['tmp_name'];
                    $datoteka_spremanja = basename($_FILES['slika']['tmp_name']);

                    $posljedna_tocka = strrpos($datoteka_spremanja, ".");
                    $ekstenzija = substr($datoteka_spremanja, $posljedna_tocka);
                    $datoteka_spremanja = str_replace(".", "", substr($datoteka_spremanja, 0, $posljedna_tocka));
                    $datoteka_spremanja = str_replace(" ", "", $datoteka_spremanja);

                    if (strlen($datoteka_spremanja) > 50) {
                        $datoteka_spremanja = substr($datoteka_spremanja, 0, 50);
                        $datoteka_spremanja .= $ekstenzija;
                    }

                    $upload_dir = "slike";
                    $i = 0;
                    while (file_exists($upload_dir . "/" . $datoteka_spremanja)) {

                        list($naziv, $ekstenzija) = explode(".", $datoteka_spremanja);
                        $datoteka_spremanja = rtrim($naziv, strval($i - 1)) . $i . "." . $ekstenzija;
                        $i++;
                    }

                    $slika = $upload_dir . "/" . $datoteka_spremanja;
                    if (move_uploaded_file($privremena_datoteka, $slika)) {

                        if (!true == ($greska_slike = image_resize($slika, $slika, 200, 200, 1))) {
                            unlink($slika);
                            echo "Došlo je do greške prilikom promjene veličine slike. Molimo pokušajte ponovo.<br>";
                            exit;
                        } else {
                            $izjava = $veza->prepare("UPDATE korisnici SET avatar = ? WHERE ID = ?");
                            $izjava->bind_param('sd', $datoteka_spremanja, $id);
                            if ($izjava->execute()) {
                                echo '<div class="alert alert-success">Uspješan unos izmjenjena slika korisnika s slikom!</div>';
                                unlink("slike/$staraSlika");
                                $staraSlika = $datoteka_spremanja;
                                Form::clearErrors('slikaKorisnik');
                                Form::clearValues('slikaKorisnik');
                            } else {
                                Form::setError("slikaKorisnik", "Greska pri radu s bazom");
                            }
                        }
                    }
                }
            }
        } else {
            Form::setError("slikaKorisnik", "Greska 1");
        }
    } else {
        Form::setError("slikaKorisnik", "Greska 1");
    }

    ?>

    <?php
    require_once('povezivanjeBP.php');
    $query = ('SELECT * FROM korisnici WHERE id=? LIMIT 1');
    $izjava = $veza->prepare($query);
    $izjava->bind_param('d', $id);
    if ($izjava->execute()) {
        $rezultat = $izjava->get_result();
        $redak = $rezultat->fetch_assoc();
        $korisnikIzmj = $redak['korisnik'];
        $staraSlika = $redak['avatar'];
        $veza->close();
    }
    Form::clearErrors('slikaKorisnik');
    Form::clearValues('slikaKorisnik');

    echo ("<p>User to edit: " . $korisnikIzmj . "</p>");
    echo "<p><img src='slike/" . $staraSlika . "' style='height: 100px; width: 100px;'/></p>";

    Form::open('SlikaKorisnik', '', array('enctype' => 'multipart/form-data'));
    Form::Hidden('predavanje', 'predano');
    Form::Hidden('id', $id);
    Form::Hidden('korisnikIzmj', $korisnikIzmj);
    Form::Hidden('avatar', $staraSlika);
    Form::File('Avatar', 'slika', array("required" => 1));
    Form::Button('Change');
    Form::Button('Quit', 'button', array('onclick' => 'location.href="sviKorisnici.php"'));
    Form::close(false);
    echo "</div>";

    include('footer.php');


    ?>