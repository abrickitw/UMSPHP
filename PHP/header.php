<!DOCTYPE html>
<html>

<head>
  <title><?php echo $naslov ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-confirmation2@4.1.0/dist/bootstrap-confirmation.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/kute.js@2.2.4/dist/kute.min.js"></script>


  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false) {
    // User is on a mobile device, serve mobile CSS
    echo '<link rel="stylesheet" type="text/css" href="Front/stilGray.css">';
  } else {
    // User is on a desktop device, serve desktop CSS
    echo '<link rel="stylesheet" type="text/css" href="Front/stil.css">';
  }
  ?>
</head>

<body>