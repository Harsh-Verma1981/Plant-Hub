<?php

session_start();

// restore session from cookies
if(!isset($_SESSION['email']) && isset($_COOKIE['email'])){
  $_SESSION['email'] = $_COOKIE['email'];
  $_SESSION['name'] = $_COOKIE['name'];
}
  
// if already logged in redirect
if(isset($_SESSION['email'])){
  header("Location: index.php");
  exit();
}
    
// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
$login = 0;
$invalid = 0;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  include 'SignupDatabase.php';

  $email = mysqli_real_escape_string($connect,$_POST['email']);
  // $password = $_POST['password'];
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM signup WHERE email='$email'";
  $result = mysqli_query($connect,$sql);

  if(mysqli_num_rows($result) == 1){

    $row = mysqli_fetch_assoc($result);

    if(password_verify($password,$row['password'])){

      $login = 1;

      $name = $row['name'];

      $_SESSION['name'] = $name;
      $_SESSION['email'] = $email;

      setcookie("name",$name,time()+(86400*30),"/");
      setcookie("email",$email,time()+(86400*30),"/");

      /* PHPMailer */

      require 'PHPMailer/Exception.php';
      require 'PHPMailer/PHPMailer.php';
      require 'PHPMailer/SMTP.php';

      $mail = new PHPMailer(true);

      try{

      $mail->isSMTP();
      $mail->Host=$_ENV['MAILER_HOST'];
      $mail->SMTPAuth=true;
      $mail->Username=$_ENV['MAILER_FROM'];
      $mail->Password=$_ENV['MAILER_PASS'];
      $mail->SMTPSecure=$_ENV['MAILER_SECURE'];
      $mail->Port=$_ENV['MAILER_PORT'];

      $mail->setFrom($_ENV['MAILER_FROM'], 'Plant-Hub');
      $mail->addAddress($email,$name);

      $mail->isHTML(true);
      $mail->Subject='Login Success';

      $mail->Body="Dear $name,<br><br>
      Welcome back to Plant-Hub 🌱<br>
      Your login was successful.<br><br>
      Happy Gardening!<br>
      Plant-Hub";

      $mail->send();

      }catch(Exception $e){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oops!</strong> User not existed.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
      }

      header("Location: index.php");
      exit();

    }else {
      $invalid = 1;
    }
  }
  else{
    $invalid = 1;
  }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="plant.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Log-in Form</title>
    <style>

  body{
    background-color: whitesmoke;
  }    
        /* From Uiverse.io by mi-series */ 
  .container {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    margin: 100px;
  }
  
  .form_area {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: #EDDCD9;
    height: auto;
    width: auto;
    border: 2px solid #264143;
    border-radius: 20px;
    box-shadow: 3px 4px 0px 1px #E99F4C;
  }
  
  .title {
    color: #264143;
    font-weight: 900;
    font-size: 1.5em;
    margin-top: 20px;
  }
  
  .sub_title {
    font-weight: 600;
    margin: 5px 0;
  }
  
  .form_group {
    display: flex;
    flex-direction: column;
    align-items: baseline;
    margin: 10px;
  }
  
  .form_style {
    outline: none;
    border: 2px solid #264143;
    box-shadow: 3px 4px 0px 1px #E99F4C;
    width: 290px;
    padding: 12px 10px;
    border-radius: 4px;
    font-size: 15px;
  }
  
  .form_style:focus, .btn:focus {
    transform: translateY(4px);
    box-shadow: 1px 2px 0px 0px #E99F4C;
  }
  
  .btn {
    padding: 15px;
    margin: 25px 0px;
    width: 290px;
    font-size: 15px;
    background: #DE5499;
    border-radius: 10px;
    font-weight: 800;
    box-shadow: 3px 3px 0px 0px #E99F4C;
  }
  
  .btn:hover {
    opacity: .9;
  }
  
  .link {
    font-weight: 800;
    color: #264143;
    padding: 5px;
  }
  .alert,
  .success{
    width: 400px;
    text-align: center;
    position: absolute;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    color: whitesmoke;
    padding: 8px 0;
  }

  .alert{background-color: rgb(252, 59, 59);}
  .success{background-color: rgb(44, 158, 24);}
  </style>
</head>
<body>

  <?php 
  if($invalid){
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Invalid!</strong> Wrong email or password.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
  }

  if($login){
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> You have Logged-In successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';

    // Redirect after 3 seconds using JavaScript
    echo "<script>
        setTimeout(function(){
            window.location.href = 'index.php';
        }, 3000);
    </script>";
  }
  ?>
<!-- From Uiverse.io by mi-series --> 
<div class="flex justify-center content-center items-center">
    <div class="container">
        <div class="form_area">
            <p class="title">Log-IN</p>
            <form action="login.php" method="post"><!-- Connect it to Database using connect-->
                <!-- <div class="form_group">
                    <label class="sub_title" for="name">Name</label>
                    <input placeholder="Enter your full name" class="form_style" type="text" name="name" autocomplete="off" required>
                </div> -->
                <div class="form_group">
                    <label class="sub_title" for="email">Email</label>
                    <input placeholder="Enter your email" id="email" class="form_style" type="email" name="email" autocomplete="off" required>
                </div>
                <div class="form_group">
                    <label class="sub_title" for="password">Password</label>
                    <input placeholder="Enter your password" id="password" class="form_style" type="password" name="password" required>
                </div>
                <div>
                <button class="btn" name="send" type="submit">Log-In</button>
                    <!-- <input class="btn" id="btn" name="send" value="Send" type="submit"> -->
                    <p>don't have an Account? <a class="link" href="signup.php">Sign up here!</a></p><a class="link" href="#">
                </a></div><a class="link" href="">
            
        </a></form></div><a class="link" href="">
    </a>
  </div>
</div>
</body>
</html>
