<?php




$connection = new PDO("mysql:host=localhost;dbname=stage;charset=utf8", "root", "");
session_start();
$email = $_SESSION["email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if (empty($password) || empty($confirmPassword)) {
        die("Veuillez remplir tous les champs.");
    }

    if ($password !== $confirmPassword) {
        die("Les mots de passe ne correspondent pas.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);
    header("location: dashboard.php");
    exit;

}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>connection - workify</title>
    <link rel="stylesheet" href="styles/change_password.css">
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

        <label>mot de passe:</label>
        <input type="password" name="password"><br>

        <label>confirme mot de passe:</label>
        <input type="password" name="confirmPassword"><br>

        <div class="form-links">
            <input type="submit" value="connection">
        </div><br>

    </form>
</div>

</container>


</body>
</html>
