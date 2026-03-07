<?php
session_start();

if(!isset($_SESSION['email']) && isset($_COOKIE['email'])){
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['name'] = $_COOKIE['name'];
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$success = 0;
$user = 0;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

include 'SignupDatabase.php';

$name = mysqli_real_escape_string($connect,$_POST['name']);
$email = mysqli_real_escape_string($connect,$_POST['email']);

$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* check if user already exists */

$sql = "SELECT * FROM signup WHERE email='$email'";
$result = mysqli_query($connect,$sql);

if(mysqli_num_rows($result) > 0){

    $user = 1;

}
else{

    $sql = "INSERT INTO signup(name,email,password)
    VALUES('$name','$email','$password')";

    $result = mysqli_query($connect,$sql);

    if($result){

        $success = 1;

        session_regenerate_id(true);

        $_SESSION['name']=$name;
        $_SESSION['email']=$email;

        setcookie("name",$name,time()+(86400*30),"/");
        setcookie("email",$email,time()+(86400*30),"/");

        /* PHPMailer */

        require 'PHPMailer/Exception.php';
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);

        try{

        $mail->isSMTP();
        $mail->Host='smtp.gmail.com';
        $mail->SMTPAuth=true;
        $mail->Username='harv280905@gmail.com';
        $mail->Password='caat ygoi uiyo mvqm';
        $mail->SMTPSecure='ssl';
        $mail->Port=465;

        $mail->setFrom('harv280905@gmail.com','Plant-Hub');
        $mail->addAddress($email,$name);

        $mail->isHTML(true);
        $mail->Subject='Welcome to Plant-Hub';

        $mail->Body="Dear $name,<br><br>
        Welcome to Plant-Hub 🌱<br>
        Thank you for joining Plant-Hub! We welcome you to our online gardening community, where you can explore and connect with fellow gardening enthusiasts.<br>
        Your Name: $name<br>
        Your Email: $email<br>
        Your Password: [Hidden for security]<br>
        Please keep your details safe and do not share them with anyone.<br><br>
        Happy Gardening!<br>
        Plant-Hub";

        $mail->send();

        }catch(Exception $e){
            error_log($mail->ErrorInfo);
        }

    }

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
    <title>Sign-up Form</title>
    <style>
        body {
            background-color: whitesmoke;    
        }
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
        .alert {
            width: 400px;
            text-align: center;
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <?php
    if ($user) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oops!</strong> User already exists.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }

    if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> You have signed up successfully.<br> Redirecting...
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';

        // Redirect after 3 seconds using JavaScript
        echo "<script>
            setTimeout(function(){
                window.location.href = 'index.php';
            }, 3000);
        </script>";
    }
    ?>
    <div class="container">
        <div class="form_area">
            <p class="title">SIGN UP</p>
            <form action="signup.php" method="post">
                <div class="form_group">
                    <label class="sub_title" for="name">Name</label>
                    <input placeholder="Enter your full name" class="form_style" type="text" name="name" autocomplete="off" required>
                </div>
                <div class="form_group">
                    <label class="sub_title" for="email">Email</label>
                    <input placeholder="Enter your email" id="email" class="form_style" type="email" name="email" autocomplete="off" required>
                </div>
                <div class="form_group">
                    <label class="sub_title" for="password">Password</label>
                    <input placeholder="Enter your password" id="password" class="form_style" type="password" name="password" autocomplete="off" required>
                </div>
                <div>
                    <button class="btn" type="submit">SIGN UP</button>
                    <p>Have an Account? <a class="link" href="login.php">Login Here!</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>