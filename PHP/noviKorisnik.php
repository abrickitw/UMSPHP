<?php
session_start();

if (isset($_SESSION["korisnik"])) {
    header("Location: logout.php");
    exit();
}

$naslov = "Create new user";
require_once "header.php";
require_once "adminNav.php";
require_once "povezivanjeBP.php";
require_once "PFBC/Form.php";
?>

<?php
function isUsernameUnique($username)
{
    global $veza;

    $query = "SELECT * FROM korisnici WHERE korisnik=? LIMIT 1";
    $priprema = $veza->prepare($query);
    $priprema->bind_param("s", $username);
    $priprema->execute();
    $priprema->store_result();

    $isUnique = ($priprema->num_rows === 0);
    $priprema->close();

    return $isUnique;
}

function addUserToDb($username, $password, $avatar)
{
    global $veza;

    $kriptiranalozinka = password_hash($password, PASSWORD_DEFAULT);

    $izjava = $veza->prepare("INSERT INTO korisnici SET korisnik=?, lozinka=?, avatar=?");
    $izjava->bind_param("sss", $username, $kriptiranalozinka, $avatar);

    return $izjava->execute();
    $veza->close();
}


function createUser()
{

    if (Form::isValid("unos", false)) {
        $korisnik = htmlentities(trim($_POST["korisnik"]));
        $lozinka = trim($_POST["lozinka"]);

        if ($_POST["lozinka"] !== $_POST["lozinka2"]) {
            Form::setError("unos", "Lozinke se ne podudaraju");
        } else {
            if (!isUsernameUnique($korisnik)) {
                Form::setError("unos", "Korisnik već postoji u bazi podataka. Koristite drugo korisničko ime");
            } else {
                if (!isset($_FILES["predavanje"])) {
                    Form::clearErrors("unos");
                    require_once "crop.php";
                    $dozvoljeni_MIME = [
                        "image/jpeg",
                        "image/gif",
                        "image/png",
                        "imag/bmp",
                    ];

                    if (
                        !empty($_FILES["slika"]["type"]) &&
                        !in_array($_FILES["slika"]["type"], $dozvoljeni_MIME)
                    ) {
                        Form::setError("unos", "Krivi tip datoteke");
                    } else {
                        $greska = $_FILES["slika"]["error"];
                        $upload_greska = [
                            UPLOAD_ERR_OK => "datoteka je uspjesno predana",
                            UPLOAD_ERR_INI_SIZE => "1",
                            UPLOAD_ERR_FORM_SIZE => "2",
                            UPLOAD_ERR_PARTIAL => "3",
                            UPLOAD_ERR_NO_TMP_DIR => "4",
                            UPLOAD_ERR_CANT_WRITE => "5",
                            UPLOAD_ERR_EXTENSION => "6",
                        ];
                        if ($greska > 0) {
                            Form::setError("unos", $upload_greska[$greska]);
                        } else {
                            $user_id = $_SESSION["id"];
                            $current_date = date("YmdHis");
                            $privremena_datoteka = $_FILES["slika"]["tmp_name"];
                            $datoteka_spremanja = basename($_FILES["slika"]["name"]);
                            $posljedna_tocka = strrpos($datoteka_spremanja, ".");
                            $ekstenzija = substr($datoteka_spremanja, $posljedna_tocka);
                            $datoteka_spremanja = str_replace(".", "", substr($datoteka_spremanja, 0, $posljedna_tocka));
                            $datoteka_spremanja = str_replace(" ", "", $datoteka_spremanja);

                            if (strlen($datoteka_spremanja) > 50) {
                                $datoteka_spremanja = substr($datoteka_spremanja, 0, 50);
                                $datoteka_spremanja .= $ekstenzija;
                            }

                            $current_date = date("YmdHis");
                            $user_id = $_SESSION["id"];
                            $datoteka_spremanja = $current_date . "_";
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
                                }

                                Form::setError("unos", "Izvrsit ce se upisivanje u bazu");

                                if (addUserToDb($korisnik, $lozinka, $datoteka_spremanja)) {
                                    echo '<div class="alert alert-success">Uspješan unos novog korisnika s slikom!</div>';
                                    header("Location: login.php");
                                } else {
                                    Form::setError("unos", "Pogreska pri upisivanju u bazu!");
                                }
                            } else {
                                Form::setError("unos", "Pogreska pri resizanju!");
                            }
                        }
                    }
                }
            }
        }
    }
}
createUser();
?>

<div class="container">

    <?php
    if (!isset($_POST["predavanje"])) {
        Form::clearErrors("unos");
        Form::clearValues("unos");
    }
    // Display form
    Form::open('unos', '', array('enctype' => 'multipart/form-data'));
    Form::Hidden('predavanje', 'predano');
    Form::Textbox('Username:', 'korisnik', array("required" => 1, "validation" => new Validation_RegExp('/^[a-zA-Z0-9_\.\-\ ]{5,50}$/', "Username must contain at least 6 characters.")));
    Form::Password('Password:', 'lozinka', array("required" => 1, "validation" => new Validation_RegExp('/^[a-zA-Z0-9_\.\-\ ]{6,50}$/', "Password must contain at least 6 characters.")));
    Form::Password('Confirm Password:', 'lozinka2', array("required" => 1));
    Form::file('Avatar', 'slika', ['required' => true]);
    Form::Button('Create User');
    Form::close(false);

    require_once "footer.php";
    ?>

</div>


<script>
    // // Set active class on nav bar items
    // document.getElementById('korisnici').classList.add('active');
    // document.getElementById('novikorisnik').classList.add('active');
</script>