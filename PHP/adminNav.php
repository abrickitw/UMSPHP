<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Links -->
  <ul class="navbar-nav mx-auto">
    <li class="nav-item">
      <a class="nav-link" href="naslovna.php">Home</a>
    </li>
    <?php if (isset($_SESSION["korisnik"])) { ?>
      <!-- Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="korisnici" data-toggle="dropdown">Users</a>
        <div class="dropdown-menu">
          <a class="dropdown-item" id="novikorisnik" href="noviKorisnik.php">New user</a>
          <a class="dropdown-item" id="svikorisnici" href="sviKorisnici.php">All users</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="chat.php">Support</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="urediKorisnika.php?id=<?php echo $_SESSION['id']; ?>">
          <?php echo "Nice to see you " . $_SESSION["korisnik"]; ?>
        </a>
        <form method="POST" action="urediKorisnika.php">
          <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
        </form>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="urediSliku.php?id=<?php echo $_SESSION['id']; ?>">
          <?php echo '<img src="slike/' . $_SESSION["avatar"] . '" style="height: 100px; width: 100px;">'; ?>
        </a>
        <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    <?php } ?>
    <?php if (!isset($_SESSION["korisnik"])) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="Connect with us" data-toggle="dropdown">Connect with us</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
          <a class="dropdown-item" id="novikorisnik" href="logIn.php">Log in</a>
          <a class="dropdown-item" id="svikorisnici" href="noviKorisnik.php">Join us</a>
        </div>
      </li>
    <?php } ?>
  </ul>
</nav>