<?php

$subject = $_SESSION['site_name']." Registration";

$message = "<html><body style=\"background-color: #151515; color: #ffffff;\"><br/>

<div style=\"margin:30px;\">
<center><h2>Welcome to ".$_SESSION['site_name']."</h2></center>

<p>Thank you for registering an account for <a target=\"_blank\" style=\"color: #03fc52;\" href=\"http://www.ionetwork3.com\">".$_SESSION['site_name']."</a>, ".$_SESSION['site_description']."</p>

<p><a target=\"_blank\" style=\"color: #03fc52;\" href=\"http://www.ionetwork3.com/account/activation.php?activate=".$activation_code."\">CLICK HERE</a> to verify your email address and activate your account!</p>

<p><u>Your registration details are:</u>
<ul>
<li>Registration Email Address: <a style=\"color: #03fc52;\">".$email."</a></li>
<li>Registration IP Address: ".$_SERVER['REMOTE_ADDR']."</li>
<li>Registration Time: ".date("d-M-Y h:i:s")." GMT-6</li>
<li>Activation Code: ".$activation_code."</li>
</ul></p>

<p>".$_SESSION['site_name']." Admin</p><br/>

</div>

</body></html>";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From: no-reply <no-reply@ionetwork3.com>\r\n";

try {
    mail($email,$subject,$message,$headers);
} catch (Exception $e) {
    $alert = "Error: ". $e;
}

?>