<?php

$connection = new PDO("mysql:host=localhost;dbname=stage;", "root", "");
$stmt = $connection->prepare("SELECT ver_code FROM stage WHERE email = ?");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (isset($_POST["verifier-email"])) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // verification code
        global $mail;
        require_once 'mail.php';
        $verification_code = mt_rand(1000, 10000);
        $mail->setFrom('aminetibari2005@gmail.com', 'Amine Tibari');
        $mail->addAddress($email);
        $mail->Subject = 'Code de verification';
        $mail->Body = "
                        <html>
                        <head>
                            <meta charset='UTF-8'>
                            <title>Vérification de Code</title>
                        </head>
                        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; text-align: center;'>
                           <div style='max-width: 600px; background: #ffffff; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                                <h2 style='color: #333;'>Votre code de vérification</h2>
                                <p style='font-size: 16px; color: #666;'>Utilisez le code ci-dessous pour vérifier votre compte :</p>
                                <div style='font-size: 24px; font-weight: bold; color: #2d89ef; margin: 20px 0; padding: 10px; background: #f0f8ff; border-radius: 5px; display: inline-block;'>
                                    $verification_code
                                </div>
                                <p style='font-size: 14px; color: #999;'>Ce code est valable pour une durée limitée.</p>
                            </div>
                        </body>
                        </html>
                        ";
        $mail->send();
        $pdo = new PDO("mysql:host=localhost;dbname=stage;", "root", "");
        $delete_stmt = $pdo->prepare("DELETE FROM stage WHERE email = ?");
        $delete_stmt->execute([$email]);
        $stmt = $pdo->prepare("INSERT INTO stage (ver_code, email) VALUES (:ver_code, :email)");
        $stmt->bindParam(':ver_code', $verification_code);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $_SESSION["email"] = $email;

        if (!$user) {
            $error_message =  "aucun compte n'a trouver avec ce email";
        } else {
           header('location: check_verification.php');
           exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>connection - workify</title>
    <link rel="stylesheet" href="styles/forget_password.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="#"><span id="logo">workify</span></a>
    <div class="account">
        <a href="signup.html"><button>registre</button></a>
    </div>
</header>

<div class="container">
    <span id="form-title">Mot de passe oublier</span>
    <form action="" method="POST">

        <div class="email-section">
            <span>Veuillez saisir votre adresse e-mail :</span>
            <br>
            <input class="email-input" type="text" name="email">
            <br>
            <button type="submit" name="verifier-email" id="btn">Verifier</button>
        </div>



        <?php if (!empty($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

    </form>
</div>

</container>


</body>
</html>