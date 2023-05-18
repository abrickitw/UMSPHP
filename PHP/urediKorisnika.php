<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!array_key_exists("korisnik", $_SESSION)) {
    header("Location: login.php");
    exit();
}
if (array_key_exists("id", $_POST)) {
    $id = (int) $_POST["id"];
} else if (array_key_exists("id", $_GET)) {
    $id = (int) $_GET["id"];
} else {
    header("Location: sviKorisnici.php");
    exit();
}
$naslov = "Edit user";
require_once "header.php";
require_once "adminNav.php";
?>

<div class="container">
    <h1>Edit user</h1>

    <?php
    require_once("povezivanjeBP.php");
    require_once("PFBC/Form.php");

    // Validate and sanitize the input for the username
    $korisnik = isset($_POST['korisnik']) ? filter_var(trim($_POST['korisnik']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';

    // Check if the form is submitted and valid
    if (Form::isValid('unos', false)) {
        $lozinka = isset($_POST['lozinka']) ? trim($_POST['lozinka']) : '';
        $lozinka2 = isset($_POST['lozinka2']) ? trim($_POST['lozinka2']) : '';

        // Validate and sanitize the input for the password
        if ($lozinka != $lozinka2) {
            Form::setError("unos", "Lozinke se ne podudaraju");
        } elseif (strlen($lozinka) < 6) {
            Form::setError("unos", "Lozinka mora sadržavati najmanje 6 znakova");
        } elseif (preg_match('/[^a-zA-Z0-9_\.\-\ ]/', $lozinka)) {
            Form::setError("unos", "Lozinka smije sadržavati samo slovne znakove, brojke i . - _");
        } else {
            // Hash the password
            $kriptiranaLozinka = password_hash($lozinka, PASSWORD_DEFAULT);
            // Check if the username already exists in the database
            $query = "SELECT * FROM korisnici WHERE korisnik = ? AND id != ? LIMIT 1";
            $priprema = $veza->prepare($query);
            $priprema->bind_param('sd', $korisnik, $id);
            $priprema->execute();
            $result = $priprema->get_result();

            if ($result->num_rows) {
                Form::setError("unos", "Korisnik već postoji u bazi podataka. Koristite drugo korisničko ime");
            } else {
                // Update the user's information in the database
                $stmt = ($lozinka != '') ?
                    $veza->prepare('UPDATE korisnici SET korisnik = ?, lozinka = ? WHERE id = ?') :
                    $veza->prepare('UPDATE korisnici SET korisnik = ? WHERE id = ?');


                if ($lozinka != '') {
                    $stmt->bind_param('ssd', $korisnik, $kriptiranaLozinka, $id);
                } else {
                    $stmt->bind_param('sd', $korisnik, $id);
                }

                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Uspješna izmjena korisnika!</div>';
                    Form::clearValues('unos');
                } else {
                    Form::setError("unos", "Pogreska prilikom upisa");
                }
            }
        }
    }

    if (!isset($_POST['predavanje'])) {
        Form::clearErrors('unos');
        Form::clearValues('unos');

        $query = "SELECT * FROM korisnici WHERE id = ?";
        $stmt = $veza->prepare($query);
        $stmt->bind_param('d', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $korisnik = $row['korisnik'];
        $slikaKorisnikaIzmj = $row['avatar'];
        echo "<p><img src='slike/" . $slikaKorisnikaIzmj . "' style='height: 100px; width: 100px;'/></p>";
    }
    Form::open('unos', '', array('view' => 'SideBySide4'));
    Form::Hidden('predavanje', 'predano');
    Form::Hidden('id', $id);
    Form::Textbox('User:', 'korisnik', array("value" => $korisnik, "required" => 1, "validation" => new Validation_RegExp('/^[a-zA-Z0-9_\.\-\ ]{5,50}$/', "%element% mora sadržavati više od 5 znaka i mogu se koristiti samo slovni znakovi i brojke te . i -.")));
    Form::Password('Password:', 'lozinka', array("validation" => new Validation_RegExp('/^[a-zA-Z0-9_\.\-\ ]{6,50}$/', "%element% mora sadržavati više od 6 znaka i mogu se koristiti samo slovni znakovi i brojke te . i -.")));
    Form::Password('Retype password:', 'lozinka2');
    Form::Button('Edit');
    Form::Button('Quit', 'button', array("onclick" => "location.href='sviKorisnici.php';"));
    Form::close(false);
    
    $veza->close();
    require_once("footer.php");
    ?>

</div>