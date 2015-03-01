<?php 
if(isset($_POST['submit'])){
    $to = "groszewa@gmail.com"; // this is your Email address
    $from = $_POST['input_email']; // this is the sender's Email address
    $name = $_POST['$input_name'];
    $subject = "A message from user " . $name;
    $subject2 = "Copy of " . $name . "'s message";
    $message = $name . " wrote the following:" . "\n\n" . $_POST['message'];
    $message2 = "Here is a copy of your message " . $name . "\n\n" . $_POST['message'];

    $headers = "From:" . $from;
    $headers2 = "From:" . $to;
    mail($to,$subject,$message,$headers);
    mail($from,$subject2,$message2,$headers2); // sends a copy of the message to the sender
    echo "Mail Sent. Thank you " . $name . ", we will contact you shortly.";
    // You can also use header('Location: thank_you.php'); to redirect to another page.
    }
?>