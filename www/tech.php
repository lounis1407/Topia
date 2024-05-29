<?php
$des = "Projet Dev-Web";
$title = "Page tech";
$num = "Dev web";
$h1 = "Projet Dev Web";
require("include/header.inc.php");
if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
    $_COOKIE["Langue"] = "fr-FR";
}
?>

<main>
    <article>
        <h3>L'image du jour depuis l'API de la Nasa</h3>
        <?php
        $url = "https://api.nasa.gov/planetary/apod?api_key=WwF7x9ZzWL1enGMGfSODGCcQXawaPU8PvRim8HBD";
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: My PHP script\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            echo "Impossible de récupérer les données de l'API de la NASA. Veuillez réactualiser la page.";
        } else {
            $obj = json_decode($response);

            if ($obj && $obj->media_type === 'image') {
                echo "<figure class=\"nasa\">";
                echo "<img alt=\"$obj->title\" title=\"$obj->title\" src=\"$obj->url\" class=\"image\"/>";
                echo "<figcaption>$obj->title</figcaption>";
                echo "</figure>";
            } elseif ($obj && $obj->media_type === 'video') {
                echo "<figure>";
                echo "<iframe width=\"560\" height=\"315\" src=\"$obj->url\" frameborder=\"0\" allowfullscreen></iframe>";
                echo "<figcaption>$obj->title</figcaption>";
            } else {
                echo "Erreur : Type de média non pris en charge ou réponse invalide.";
            }
        }
        ?>
    </article>

    <article id="g">
        <h3>Votre localisation avec geoplugin</h3>
        <?php
        $url = "http://www.geoplugin.net/xml.gp?ip=" . $_SERVER['REMOTE_ADDR'];
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            )
        );
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            echo "<p>Impossible de récupérer les données de geoplugin. Veuillez réactualiser la page.</p>";
        } else {
            $xml = @simplexml_load_string($response);

            if ($xml === false) {
                echo "<p>Erreur : la réponse de geoplugin est invalide.</p>";
            } else {
                echo '<ul>';
                if (!empty($xml->geoplugin_countryName)) {
                    echo '<li>Pays : ' . $xml->geoplugin_countryName . '</li>';
                }
                if (!empty($xml->geoplugin_region)) {
                    echo '<li>Région : ' . $xml->geoplugin_region . '</li>';
                }
                if (!empty($xml->geoplugin_regionName)) {
                    echo '<li>Département : ' . $xml->geoplugin_regionName . '</li>';
                }
                if (!empty($xml->geoplugin_city)) {
                    echo '<li>Ville : ' . $xml->geoplugin_city . '</li>';
                }
                if (!empty($xml->geoplugin_latitude)) {
                    echo '<li>Latitude : ' . $xml->geoplugin_latitude . '</li>';
                }
                if (!empty($xml->geoplugin_longitude)) {
                    echo '<li>Longitude : ' . $xml->geoplugin_longitude . '</li>';
                }
                echo '</ul>';
            }
        }
        ?>
    </article>

    <article id="ip">
        <h3>Votre IP avec ipinfo</h3>
        <?php
        $url = "http://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "?token=12f1becc3c953f";
        $response = @file_get_contents($url);

        if ($response === false) {
            echo "<p>Impossible de récupérer les données d'ipinfo. Veuillez réactualiser la page.</p>";
        } else {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<p>Erreur : la réponse d'ipinfo est invalide.</p>";
            } else {
                echo '<ul>';
                if (!empty($data['ip'])) {
                    echo '<li>Votre Adresse IP : ' . $data['ip'] . '</li>';
                }
                if (!empty($data['hostname'])) {
                    echo '<li>Votre Hostname : ' . $data['hostname'] . '</li>';
                }
                if (!empty($data['city'])) {
                    echo '<li>Votre ville : ' . $data['city'] . '</li>';
                }
                if (!empty($data['region'])) {
                    echo '<li>Votre région : ' . $data['region'] . '</li>';
                }
                if (!empty($data['country'])) {
                    echo '<li>Votre pays : ' . $data['country'] . '</li>';
                }
                if (!empty($data['loc'])) {
                    echo '<li>Votre localisation : ' . $data['loc'] . '</li>';
                }
                if (!empty($data['postal'])) {
                    echo '<li>Votre code postale : ' . $data['postal'] . '</li>';
                }
                if (!empty($data['timezone'])) {
                    echo '<li>Votre fuseau horaire : ' . $data['timezone'] . '</li>';
                }
                echo '</ul>';
            }
        }
        ?>
    </article>

    <article id="what">
        <h3>Votre IP avec whatismyip</h3>
        <?php
        define('API_ENDPOINT', 'https://api.whatismyip.com/ip.php');
        define('API_KEY', 'd0bbe1831b6812599703f955c13933de');
        $params = [
            'key' => API_KEY,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'output' => 'xml'
        ];
        $url = API_ENDPOINT . '?' . http_build_query($params);

        $response = @file_get_contents($url);

        if ($response === false) {
            echo "<p>L'adresse IP du host : Indisponible</p>";
        } else {
            $xml = @simplexml_load_string($response);

            if ($xml === false || !isset($xml->ip_address)) {
                echo "<p>Erreur : la réponse de l'API est invalide.</p>";
            } else {
                echo "<p>L'adresse IP du host : $xml->ip_address</p>";
            }
        }

        echo '<p>Votre adresse IP: ' . $_SERVER['REMOTE_ADDR'] . '</p>';
        ?>
    </article>

    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>

<?php
require("include/footer.inc.php");
?>
