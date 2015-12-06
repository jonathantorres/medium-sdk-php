<?php

    require('../vendor/autoload.php');
    require('credentials.php');

    use JonathanTorres\MediumSdk\Medium;

    $medium = new Medium($credentials);
    $medium->authenticate($_GET['code']);

    $authenticatedUser = $medium->getAuthenticatedUser();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Callback</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
        <div class="row">
            <h1>Authenticated user details</h1>
            <hr>
            <pre>
                <?php print_r($authenticatedUser); ?>
            </pre>
        </div>
        <div class="row">
            <h1>Authenticated user publications</h1>
            <hr>
            <pre>
                <?php print_r($medium->publications($authenticatedUser->data->id)); ?>
            </pre>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  </body>
</html>
