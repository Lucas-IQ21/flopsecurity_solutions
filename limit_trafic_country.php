<?php
    require 'path/to/vendor/autoload.php'; // Chemin vers le fichier autoload.php de la bibliothèque GeoIP2

    // Importation des classes GeoIP2\Database\Reader
    use GeoIp2\Database\Reader;

    // Chemin vers le fichier GeoLite2-City.mmdb qui contient : le pays, la ville, le code postal, la latitude, la longitude, etc.
    $databasePath = 'var/www/GeoLite2-City.mmdb';

    // Clé API
    $apiKey = 'NDKJAU9813Ndàç&ènàç_&"&90841ND97';

    // Ici j'initialise le lecteur GeoIP2
    $reader = new Reader($databasePath, ['locales' => ['fr'], 'apiKey' => $apiKey]);

    // fonction qui récupère l'ip de l'utilisateur
    function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    // $userIp = l'ip de l'utilisateur
    $userIp = getIpAddress();

    try {
        // Recherche la localisation de l'adresse IP
        $record = $reader->city($userIp);

        // Prend le code du pays localisé
        $countryCode = $record->country->isoCode;

        // Tableau des codes pays des pays francophones
        $francophoneCountries = ['FR', 'CA', 'BE'];

        // Check si le pays de l'utilisateur est dans la liste des pays francophones
        if (in_array($countryCode, $francophoneCountries)) {
            // L'utilisateur est autorisé à accéder au site
            echo "Bienvenue sur notre site !";
        } else {
            // L'utilisateur n'est pas autorisé à accéder au site
            echo "Désolé, l'accès à ce site est restreint dans votre région.";
        }
    } catch (Exception $e) {
        // Gestion des erreurs, par exemple enregistrer dans un fichier journal
        echo "Une erreur s'est produite : " . $e->getMessage();
    }
?>
