<?php


session_start();

$email = $_SESSION["email"];
$verified = $_SESSION["verified"];

if ($email == null) {
    header("location: login.php");
    exit;
} else {
    $connection = new PDO("mysql:host=localhost;dbname=stage;", "root", "");

    $stmt = $connection->prepare("SELECT ver_code FROM stage WHERE email = ?");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        $stmt->execute([$email]);
        $verification_code = $stmt->fetch(PDO::FETCH_ASSOC);
        $ver_code_value = $verification_code['ver_code'];
        $code_entrer = $_POST["code_entrer"];

        if ($code_entrer == $ver_code_value) {
            header("location: change_password.php");
            exit;
        } else {

            $error_message = "Le code que vous avez saisi est incorrect. Veuillez réessayer.";

        }

    }


}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>connection - workify</title>
    <link rel="stylesheet" href="styles/verify.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="#"><span id="logo">workify</span></a>
    <div class="account">
        <a href="signup.php"><button>registre</button></a>
    </div>
</header>

<div class="container">
    <span id="form-title">Verification 2FA</span>
    <form action="" method="POST">

        <span>Veuillez saisir le code envoyé à votre adresse e-mail :</span>
        <br>
        <input type="text" name="code_entrer">
        <br>
        <button type="submit" id="btn">Verifier</button>
        <br>
        <span>Merci de vérifier votre boîte de réception.</span>

        <?php if (!empty($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

    </form>
</div>

</container>


</body>
</html>