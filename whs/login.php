<?php 
  include 'timetable.class.php';
  $hasSuccess = false;
  $hasError = false;
  session_start();
  if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    header('Location: timetable.php');
  } else {
    
  }
  if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && !empty($_POST['username'])) {
      $username = $_POST['username'];
      if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
        $loginTest = Timetable::testLogin($username, $password);
        if ($loginTest) {
          $hasSuccess = true;
          $_SESSION['username'] = $username;
          $_SESSION['password'] = $password;
          $_SESSION['method'] = "today";
          header('Location: timetable.php');
        } else {
          $hasError = true;
          $errorMSG = "You're username or password is incorrect. Please try again.";
        }
      } else {
        $hasError = true;
        $errorMSG = "Please enter your password.";
      }
    } else {
      $hasError = true;
      $errorMSG = "Please enter your username.";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Wyong High School Time Table">
    <meta name="author" content="Joseph Shenton">
    <title>Sign in</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="signin.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <form class="form-signin" method="POST">
      <br>
      <br>
      <br>
      <br>
      <?php if ($hasError): ?>
        <div class="alert alert-danger">
          <strong>Error!</strong> <?php echo $errorMSG; ?>
        </div>
      <?php endif ?>
      <?php if ($hasSuccess): ?>
        <div class="alert alert-success">
          <strong>Success!</strong> <?php echo $successMSG; ?>
        </div>
      <?php endif ?>
      <img class="mb-4" src="whs.png" alt="" width="120" height="120">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in with your DET login</h1>
      <label for="username" class="sr-only">Username</label>
      <input type="text" id="username" value="<?php if(isset($username)) { echo $username; } ?>" name="username" class="form-control" placeholder="john.doe" required autofocus>
      <br>
      <label for="password" class="sr-only">Password</label>
      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
      <br>
      <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Sign in</button>
      <p class="mt-5 mb-3 text-muted">&copy; JJS Digital 2017-<?php echo date("Y"); ?></p>
    </form>
  </body>
</html>
