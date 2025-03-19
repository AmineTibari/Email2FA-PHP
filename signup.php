
<?php

// Paramètres de connexion à la base de données
$host = "localhost";
$dbname = "stage";
$username = "root";
$password = "";

// Vérifier si la requête est bien de type POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    try {
        // Connexion sécurisée à la base de données avec PDO
        $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupération et sécurisation des entrées utilisateur
        $username = trim($_POST["username"]);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];

        // Validation des entrées
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error_message = "Veuillez remplir tous les champs.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Adresse e-mail invalide.";
        } elseif ($password !== $confirmPassword) {
            $error_message = "Les mots de passe ne correspondent pas.";
        } else {
            // Vérifier si l'utilisateur existe déjà
            $stmt = $connection->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error_message = "Un compte avec cet e-mail existe déjà.";
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insertion de l'utilisateur
                $stmt = $connection->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $hashedPassword);
                if ($stmt->execute()) {

                    // Génération du code de vérification
                    require_once 'mail.php';
                    $verification_code = random_int(1000, 9999);

                    // Envoi du code de vérification par e-mail
                    global $mail;
                    $mail->setFrom('aminetibari2005@gmail.com', 'Amine Tibari');
                    $mail->addAddress($email);
                    $mail->Subject = 'Code de vérification';
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

                    // Suppression des anciens codes et insertion du nouveau code
                    $delete_stmt = $connection->prepare("DELETE FROM stage WHERE email = ?");
                    $delete_stmt->execute([$email]);
                    $stmt = $connection->prepare("INSERT INTO stage (ver_code, email) VALUES (:ver_code, :email)");
                    $stmt->bindParam(':ver_code', $verification_code);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();

                    // Création de la session utilisateur
                    $_SESSION["email"] = $email;
                    $_SESSION["verified"] = false;

                    // Redirection vers la page de vérification
                    header("Location: verify.php");
                    exit();
                } else {
                    $error_message = "Erreur lors de la création du compte. Veuillez réessayer.";
                }
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>connection - workify</title>
    <link rel="stylesheet" href="styles/signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="#"><span id="logo">workify</span></a>
    <div class="account">
        <a href="login.php"><button>connection</button></a>
    </div>
</header>

<div class="container">
    <span id="form-title">Créer un compte</span>
    <form action="" method="POST">

        <label>nom d'utilisateur:</label>
        <input type="text" name="username"><br>

        <label>email:</label>
        <input type="email" name="email"><br>

        <label>mot de passe:</label>
        <input type="password" name="password"><br>

        <label>confirme mot de passe:</label>
        <input type="password" name="confirmPassword"><br>

        <div class="form-links">
            <?php if (!empty($error_message)) : ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <input type="submit" value="connection">
        </div><br>



    </form>


</div>


</body>
</html>