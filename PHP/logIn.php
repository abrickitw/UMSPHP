<?php

$naslov = "Log-in";
require_once("header.php");
require_once("adminNav.php");
require_once("PFBC/Form.php");
session_start();

?>


<div class="container">
    <h1>Log in</h1>


    <?php
    function login()
    {
        require_once("povezivanjeBP.php");

        if (Form::isValid('login', false)) {
            $korisnik = trim(strip_tags($_POST['korisnik']));
            $lozinka = trim(strip_tags($_POST['lozinka']));

            $query = "SELECT * FROM korisnici WHERE korisnik = ?";
            $stmt = $veza->prepare($query);
            $stmt->bind_param("s", $korisnik);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->num_rows;

            if ($count == 1) {
                $row = $result->fetch_assoc();
                $avatar = $row['avatar'];
                $id = $row['id'];

                if (password_verify($lozinka, $row['lozinka'])) {
                    echo '<div class="alert alert-success">Uspješan login!</div>';
                    Form::clearValues('login');
                    $_SESSION["korisnik"] = $korisnik;
                    $_SESSION['avatar'] = $avatar;
                    $_SESSION['id'] = $id;
                    $current_date = date('Y-m-d H:i:s');
                    $_SESSION['Ltime'] = $current_date;
                    header("Location: naslovna.php");
                    $veza->close();
                    exit;
                } else {
                    Form::setError("login", "Netočna lozinka.");
                }
            } else {
                Form::setError("login", "Netočno korisničko ime.");
            }
        }

        if (!isset($_POST['predavanje'])) {
            Form::clearErrors('login');
            Form::clearValues('login');
        }
    }

    function renderLoginForm()
    {
        Form::open('login', '', array('view' => 'SideBySide4'));
        Form::Hidden('predavanje', 'predano');
        Form::Textbox('Username:', 'korisnik', array("required" => 1));
        Form::Password('Password:', 'lozinka', array("required" => 1));
        Form::Button('Log in');
        Form::close(false);
    }

    login();
    renderLoginForm();

    require_once("footer.php");
    ?>
</div>