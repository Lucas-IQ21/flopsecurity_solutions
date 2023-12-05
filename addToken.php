<?php
    session_start();

    // Connexion à la base de données
    $servername = "adress serveur";
    $username = "user";
    $password = "password";
    $dbname = "name_bdd";
    $conn = new mysqli($servername, $username, $password, $dbname);


    // génère un jeton aléatoire
    function generAleatoireToken() {
        return bin2hex(random_bytes(32)); // 32 bytes = 256 bits
    }

    // Fonction pour définir un cookie sécurisé
    function setSecureCookie($name, $value, $expiration) {
        setcookie($name, $value, $expiration, '/', null, true, true); // secure and httpOnly flags
    }

    // Hash le password
    function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        echo "Bonjour, " . $_SESSION['username'] . "! <br>";
    } else {
        // Sinon est-ce que le formulaire de connexion est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // ici je recherche l'utilisateur dans la base de données
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            // Si l'utilisateur est trouvé
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc(); //récupère la prochaine ligne de résultat de la requête SQL sous la forme d'un tableau associatif
                // vérif mot de passe
                if (password_verify($password, $row['password'])) {
                    // Mot de passe correct, connecter l'utilisateur
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];

                    // Générer et stocker un jeton aléatoire
                    $token = generAleatoireToken();
                    setSecureCookie('token', $token, time() + (60 * 60 * 24 * 7)); // 1 semaine

                    // Enregistrer le jeton dans la base de données
                    $updateTokenStmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
                    $updateTokenStmt->bind_param("si", $token, $row['id']);
                    $updateTokenStmt->execute();
                    $updateTokenStmt->close();

                    echo "Connexion réussie!";
                } else {
                    echo "Mot de passe incorrect.";
                }
            } else {
                echo "Nom d'utilisateur introuvable.";
            }

            $stmt->close();
        }
    }

    // Fermer la connexion à la base de données
    $conn->close();
?>
