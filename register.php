<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "users";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hashage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si l'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Nom d'utilisateur déjà pris.";
    } else {
        // Insérer l'utilisateur dans la base de données
        $stmt = $conn->prepare("INSERT INTO users (user, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Erreur lors de l'inscription.";
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>
<form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" required><br>
    <label for="password">Password:</label>
    <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
<p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</body>
</html>
