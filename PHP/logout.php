<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 36000, '/');
}

// Destroy the session
session_destroy();
$naslov = "All users";
require_once("header.php");
require_once("adminNav.php");
require_once("povezivanjeBP.php");
?>

<div class="container">
    <h1>Redy to redefine and </h1>
    <h1> reenvision world??</h1>
    <h1>See you soon</h1>
    <p></p>
    <h2>Join us</h2>
    <h2>We are all here</h2>
    <table style="margin: 0 auto;">
        <tbody>
            <?php
            require_once("povezivanjeBP.php");
            // Use a prepared statement to select the latest 6 users
            $upit = "SELECT * FROM korisnici ORDER BY id DESC LIMIT 6";
            $stmt = $veza->prepare($upit);
            $stmt->execute();
            $rezultat = $stmt->get_result();

            $count = 0;

            while ($row = mysqli_fetch_array($rezultat, MYSQLI_ASSOC)) {
                if ($count % 3 === 0) {
                    echo '<tr>';
                }
            ?>
                <td style="padding: 5vw;">
                    <img width='150vw' height='150vw' src="slike/<?php echo $row['avatar']; ?>" />
                </td>
            <?php
                $count++;
                if ($count % 3 === 0) {
                    echo '</tr>';
                }
            }

            if ($count % 3 !== 0) {
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>