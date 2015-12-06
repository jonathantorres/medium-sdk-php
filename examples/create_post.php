<?php

    require('../vendor/autoload.php');
    require('credentials.php');

    use JonathanTorres\MediumSdk\Medium;

    $credentials['redirect-url'] = 'http://localhost:8888/create_post.php';
    $medium = new Medium($credentials);

    if (isset($_GET['code'])) {
        session_start();
        $code = $_GET['code'];
        $medium->authenticate($code);
        $_SESSION['user'] = $medium->getAuthenticatedUser();
        $_SESSION['code'] = $code;
        $_SESSION['token'] = $medium->getAccessToken();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        $authenticatedUser = $_SESSION['user'];

        $data = [
            'title' => $_POST['title'],
            'contentFormat' => 'html',
            'content' => $_POST['content'],
            'publishStatus' => 'draft',
        ];

        $medium->setAccessToken($_SESSION['token']);
        $post = $medium->createPost($authenticatedUser->data->id, $data);
    }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create post</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
        <?php if(isset($_POST['title']) && isset($_POST['content'])) : ?>
            <div class="alert alert-success">Post created successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['code']) || isset($_SESSION['code'])) : ?>
            <div class="row">
                <h1>Create post</h1>
                <hr>
                <form method="POST" action="create_post.php">
                  <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Post Title">
                  </div>
                  <div class="form-group">
                    <label for="content">Post Content</label>
                    <textarea class="form-control" name="content" id="content" name="content" cols="30" rows="10"></textarea>
                  </div>
                  <button type="submit" class="btn btn-default">Create Post</button>
                </form>
            </div>
        <?php else: ?>
            <div class="row">
                <h1>Authenticate to create a post</h1>
                <hr>
                <a href="<?php echo $medium->getAuthenticationUrl(); ?>" class="btn btn-primary btn-large">Authenticate with Medium</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  </body>
</html>
