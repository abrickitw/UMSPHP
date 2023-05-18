<?php
session_start();

(!isset($_SESSION["korisnik"])) ? header("Location:logout.php") : $naslov = "Home Page";

require_once("header.php");
require_once("adminNav.php");
?>


<div class="layerTop spacer">
  <div class="container">
    <div class="naslov">
      <h1>Make millions from a verb</h1>
      <p>No need to be a rapper,</p>
      <p>Just Add value</p>
    </div>
    <svg class="blob-motion" id="visual" viewBox="0 0 900 600" height="1000" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
      <g transform="translate(407.94677291355544 286.8247951027955)">
        <path id="blob2" d="M141 -116C191 -91 245.5 -45.5 243.7 -1.8C242 42 183.9 83.9 133.9 118.9C83.9 153.9 42 182 2.2 179.7C-37.5 177.5 -75 145 -102.1 110C-129.3 75 -146.1 37.5 -154.6 -8.5C-163.1 -54.4 -163.2 -108.9 -136.1 -133.9C-108.9 -158.9 -54.4 -154.5 -4.5 -150C45.5 -145.5 91 -141 141 -116" fill="#001122"></path>
      </g>
      <g transform="translate(456.5126234764815 296.21313191086546)" style="visibility: hidden;">
        <path id="blob3" d="M180.1 -168.9C218.8 -141.4 225.4 -70.7 222.3 -3.1C219.2 64.6 206.5 129.2 167.8 161.5C129.2 193.8 64.6 193.9 4.8 189.1C-54.9 184.3 -109.8 174.5 -157.2 142.2C-204.5 109.8 -244.3 54.9 -234.5 9.8C-224.7 -35.4 -165.4 -70.7 -118 -98.2C-70.7 -125.7 -35.4 -145.4 17.7 -163C70.7 -180.7 141.4 -196.4 180.1 -168.9" fill="#001122"></path>
      </g>
    </svg>

    <?php
    $current_date = $_SESSION["Ltime"];
    echo "<h3>Log in time: $current_date.</h3><br>";
    echo $_SESSION["korisnik"] . ", What are your plans ?<br>";
    ?>


    <p id="current-time"></p>
    <p id="spend-hours"></p>
    <p id="spend-minutes"></p>
    <p id="spend-time"></p>
  </div>
</div>

<div class="spacer layer2 flip">
  <div class="flip-back">
  </div>
</div>


<script>
  function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    document.getElementById("current-time").innerHTML = "Time: " + hours + ":" + minutes + ":" + seconds + "<br>" + "You have spent";
  }

  function spendTime() {
    var startTime = new Date("<br><?php echo $current_date ?>").getTime(); // convert PHP date to milliseconds
    var currentTime = new Date().getTime();
    var elapsedTime = currentTime - startTime;

    var hours = Math.floor(elapsedTime / (1000 * 60 * 60));
    var minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);


    if (hours > 0) {
      document.getElementById("spend-hours").innerHTML = hours + " hours,<br> and ";
    }
    if (minutes > 0) {
      document.getElementById("spend-minutes").innerHTML = minutes + " minutes,<br> and ";
    }
    document.getElementById("spend-time").innerHTML = seconds + " seconds on this page.<br>";
  }

  setInterval(updateTime, 1000);
  setInterval(spendTime, 1000);

  const tween = KUTE.fromTo(
    '#blob2', {
      path: '#blob2'
    }, {
      path: '#blob3'
    }, {
      repeat: 999,
      duration: 3000,
      yoyo: true
    }
  ).start();
</script>

<?php
include('footer.php');
?>