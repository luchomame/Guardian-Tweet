<?php
  session_start();
  session_regenerate_id();
  $id =  $_SESSION['id']."</br>";

  $user_location = 'Atlanta'; /* location is hardcoded... major update to dynamically determine user location */
  $tweets = array();

  //connect to database
  /* YOU MUST CHANGE 'password' to whatever your actual password is */
  $db = new mysqli('127.0.0.1', 'root', 'CHANGE_TO_YOUR_PASSWORD', 'checkin'); //connect to db

  //check for db connection error
  if ($db->connect_errno) {
    echo "Unable to connect to Db: (".$db->connect_errno.") ".$db->connect_error;
    exit();
  };

  //call python script for the user (id) location
  // $output = shell_exec("/usr/local/bin/python3 ./sentiment2.py 2>&1"); //good

  //query database for tweets in the area
  $query = "select tweets from $user_location";
  $stmt = $db->prepare($query);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($tweet);

  if ($stmt->num_rows > 0) {
    while ($stmt->fetch()) {
      //add tweets to an array
      array_push($tweets, $tweet);
    }
  }

  $_SESSION['tweets'] = $tweets; //set tweet session variable
  $_SESSION['city'] = $user_location;




  ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Tweet</title>

      <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat|Ubuntu" rel="stylesheet">

  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="checkinresultPosStyles.css">

  <!-- Font Awesome -->
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>

  <!-- Bootstrap Scripts -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>
<body>

    <!--Nav Bar-->
    <section class="colored-section1" id="title">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark">
          <a class="navbar-brand" href="../index.html">Guardian Tweet</a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="theteam.html">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="http://www.southernhacks.org/">Southern Hacks</a>
            </li>
          </ul>

       </nav>
      </div>
    </section>

    <!--Check In Result-->

    <div class="colored-section2">
      <div class="container-fluid">
        <div class="text-center">
          <h1 class="analysis">Based on sentiment analysis for your location: </h1>
            <!--If disaster has occurred [1] php will allow this to be displayed-->
            <p class="hasOccurred btn-secondary">

              <?php
                if (count($_SESSION['tweets']) > 0) {
                  echo "A negative event <b>may have occurred</b> near";
                } else {
                  echo "No negative events detected near";
                }

               ?>

              <?php echo $_SESSION['city']; ?></p>
        </div>

        <div class="safetyWrapper">
          <!--Clicking either of these will direct next to the loved one safety page -->

          <?php
            if (count($_SESSION['tweets']) > 0) {
              echo "<form class='markSafeForm' action='lovedOneStatus.html' onsubmit='setTimeout(function () { window.location = '../lovedOneStatus/lovedOneStatus.html'; }, 10)'>
              <button type='submit' class='btn btn-success btn-lg'>Mark Safe</button>
              </form>

              <form class='markUnsafeForm' action='lovedOneStatus.html' onsubmit='setTimeout(function () { window.location = '../lovedOneStatus/lovedOneStatus.html'; }, 10)'>
              <button type='submit' class='btn btn-warning btn-lg'>Mark Unsafe</button>
              </form>";
            }

           ?>

          <!--Clicking unsafe should ideally ask people if they would like to write an email to their loved ones explaining
          + inviting to this service -->
        </div>

        <div class="twitterNewsWrapper">
          <div class="my-3 p-3 bg-white rounded box-shadow">
            <h6 class="border-bottom border-gray pb-2 mb-0 tweetHeader">Tweets about this disaster</h6>

            <?php

              for ($i = 0; ($i < 5) && ($i < count($_SESSION['tweets'])); $i++) {

                echo "<div class='media text-muted pt-3'>
                  <img data-src='holder.js/32x32?theme=thumb&amp;bg=007bff&amp;fg=007bff&amp;size=1' alt='32x32' class='mr-2 rounded' style='width: 32px; height: 32px;' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2232%22%20height%3D%2232%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2032%2032%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1709246cb63%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A2pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1709246cb63%22%3E%3Crect%20width%3D%2232%22%20height%3D%2232%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2212.2890625%22%20y%3D%2216.9%22%3E32x32%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' data-holder-rendered='true'>
                  <p class='media-body pb-3 mb-0 small lh-125 border-bottom border-gray'>
                    <strong class='d-block text-gray-dark'>@username</strong>";

                echo $_SESSION['tweets'][$i];

                echo "</p></div>";

              }


            ?>

            <small class="d-block text-right mt-3">
              <a href="#">All updates</a>
            </small>
          </div>
        </div>

      </div>
    </div>

    </div>



<!--Jquery-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!--JS-->
<script src='checkinresultScript.js'></script>
</body>

</html>

<!--

  col-lg-6 col-sm-6
-->
