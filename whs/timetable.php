<?php 
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// error_reporting(E_ALL);
  include 'timetable.class.php';
  session_start();

  if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    
  } else {
    header('Location: login.php');
  }

  date_default_timezone_set("Australia/Sydney");

  $timetable = json_decode(Timetable::getTimetableJSON($_SESSION['username'], $_SESSION['password']) , true);

  if (isset($_SESSION['method']) && !empty($_SESSION['method']) && $_SESSION['method'] == "today") {
      foreach ($timetable as $key => $information) {
          if (Timetable::filterArray($information['period_start'], date('d/m/Y', time()))) {

          }
          else {
              unset($timetable[$key]);
          }
      }
  }

  $timetable = array_values($timetable);

  $fullTimetable = json_decode(Timetable::getTimetableJSON($_SESSION['username'], $_SESSION['password']) , true);

  // if (Timetable::isWeekend(time())) {
  //     $timetable = '[
  //     {
  //         "period_start": "30\/01\/2018 12:00 AM",
  //         "fetch_time": "11\/02\/2018 12:00 AM",
  //         "period_end": "30\/01\/2018 11:59 PM",
  //         "UID": "WEEKEND",
  //         "teacher": "WEEKEND",
  //         "period": "WEEKEND",
  //         "class": "WEEKEND: WEEKEND",
  //         "year": "WEEKEND",
  //         "room": "WEEKEND"
  //     }
  // ]';

  //     $timetable = json_decode($timetable , true);
  // }

  // echo print_r($timetable, JSON_PRETTY_PRINT);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Wyong High School Time Table">
    <meta name="author" content="Joseph Shenton">
    <title>WHS Timetable</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">WHS</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li> -->
        </ul>
        <!-- <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form> -->
      </div>
    </nav>

    <main role="main" class="container">

      <div class="starter-template">
        <h1>Today's Timetable</h1>
        <p class="lead">These are your current classes.</p>
        <center>
          <div class="row">
            <?php 
              $used = array();
            ?>
            <?php foreach ($timetable as $key => $class): ?>
              <?php 
                $start_time = explode(date('Y')." ", $class['period_start']);
                $start = $start_time[1];
                $end_time = explode(date('Y')." ", $class['period_end']);
                $end = $end_time[1];
                $teacher = $class['teacher'];
                $period = $class['period'];
                $room = $class['room'];
                $classInfo = explode(": ", $class['class']);
                $classNumber = $classInfo[0];
                $className = $classInfo[1];
              ?>
              <div class="col">
                <div class="card" style="width: 18rem;">
                  <div class="card-img-top" style="background-image: url('images/<?php echo $period; ?>.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center center;" alt="Card image cap"></div>
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $className; ?></h5>
                    <p class="card-text"><?php echo $teacher; ?></p>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">Period: <?php echo $period; ?></li>
                    <li class="list-group-item">Room: <?php echo $room; ?></li>
                    <li class="list-group-item"><?php echo $start; ?> - <?php echo $end; ?></li>
                  </ul>
                </div>
              </div>
            <?php endforeach ?>
          </div>
        </center>
        <br><br><br>
        <h1>Full Timetable</h1>
        <p class="lead">This is your full timetable for the next two terms.</p>
        <center>
          <div class="row">
            <?php 
              $used = array();
            ?>
            <?php foreach ($fullTimetable as $key => $class): ?>
              <?php 
                $start_time = explode(date('Y')." ", $class['period_start']);
                $start = $start_time[1];
                $end_time = explode(date('Y')." ", $class['period_end']);
                $end = $end_time[1];
                $day = date("l", strtotime($class['period_start']));
                $teacher = $class['teacher'];
                $period = $class['period'];
                $room = $class['room'];
                $classInfo = explode(": ", $class['class']);
                $classNumber = $classInfo[0];
                $className = $classInfo[1];
              ?>
              <div class="col">
                <div class="card" style="width: 18rem;">
                  <div class="card-img-top" style="background-image: url('images/<?php echo $period; ?>.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center center;" alt="Card image cap"></div>
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $className; ?></h5>
                    <p class="card-text"><?php echo $teacher; ?></p>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">Period: <?php echo $period; ?></li>
                    <li class="list-group-item">Room: <?php echo $room; ?></li>
                    <li class="list-group-item"><?php echo $day; ?> | <?php echo $start; ?> - <?php echo $end; ?></li>
                  </ul>
                </div>
              </div>
            <?php endforeach ?>
          </div>
        </center>
      </div>

    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
