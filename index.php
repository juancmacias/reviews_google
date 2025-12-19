<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Reviews on Google, example php</title>
</head>
<?php
require_once('key.php');
?>
<body>
    <div class="container">
        <div class="cliente">
            <?php
            $data = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?place_id=ChIJxQQ8oG0nQg0Rg-0sKaNugIc&fields=name,rating,review,website,formatted_phone_number&reviews_no_translations=true&translated=false&key=".$YOUR_API_KEY."&reviews_sort=newest"), true);
            if ($data && isset($data['result']['reviews'])) {
                echo '
                        <h1>Reseñas para: ' . $data['result']['name'] . '</h1>
                        <div class="def">';

                echo '<span style="font-size: 56px; font-weight: 300;">' . number_format($data['result']['rating'], 1) . '</span>';
                echo '<div class="estrellas">';
                $rating = $data['result']['rating'];
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($rating)) {
                        echo '<span class="gral"><i class="fa-solid fa-star"></i></span>';
                    } else if ($i == ceil($rating) && $rating != floor($rating)) {
                        echo '<span class="gral"><i class="fa-solid fa-star-half-stroke"></i></span>';
                    } else {
                        echo '<span style="color: #e8eaed;"><i class="fa-solid fa-star"></i></span>';
                    }
                }
                $total = count($data['result']['reviews']);
                echo '</div><span style="font-size: 14px; color: #70757a; margin-left: 8px;">(' . $total . ($total != 1 ? ' reseñas' : ' reseña') . ')</span></div>';

                $telefono = "";
                if(isset($data['result']['formatted_phone_number'])){
                    $telefono = '<a href="tel:+34'.$data['result']['formatted_phone_number'] .'" title="Llamar al '. $data['result']['name'] .'">'.$data['result']['formatted_phone_number'] .'</a> ';
                }
                
                // Separar las URLs si hay múltiples separadas por punto y coma
                $websites = "";
                if(isset($data['result']['website'])){
                    $urls = explode(';', $data['result']['website']);
                    $websiteLinks = array();
                    foreach($urls as $individualUrl){
                        $individualUrl = trim($individualUrl);
                        if(!empty($individualUrl)){
                            // Decodificar URL si está codificada
                            $individualUrl = urldecode($individualUrl);
                            // Limpiar espacios
                            $individualUrl = trim($individualUrl);
                            // Si la URL ya tiene protocolo duplicado (ej: " https://..."), remover el espacio
                            $individualUrl = preg_replace('/^\s*(https?:\/\/)/', '$1', $individualUrl);
                            // Asegurar que la URL tenga el protocolo http:// o https://
                            if(!preg_match('/^https?:\/\//', $individualUrl)){
                                $individualUrl = 'https://' . $individualUrl;
                            }
                            $websiteLinks[] = '<a href="'.$individualUrl.'" target="_top" title="Visitar la web de '.$data['result']['name'].'">'.$individualUrl.'</a>';
                        }
                    }
                    $websites = implode(' | ', $websiteLinks);
                }
                
                echo '<div class="contacto">'. $telefono . $websites .'</div>';
                echo '<div class="reviews">';
                // Iterar sobre las reseñas
                // Ordenar las reseñas por el campo 'time' (más recientes primero)

                usort($data['result']['reviews'], function($a, $b) {
                    return $b['time'] - $a['time'];
                });
                
                // Mostrar todas las reseñas que devuelve la API (máximo 5 según limitación de Google)
                foreach ($data['result']['reviews'] as $review) {
                    echo '
                 <div class="review">
                <img src="' . $review['profile_photo_url'] . '" alt="Foto de ' . $review['author_name'] . '" class="profile-photo">
                <h2>' . $review['author_name'] . '</h2>';
            ?>
                    <div class="rating">
                        <span class="kvMYJc" role="img" aria-label="<?php echo $review['rating']; ?> estrellas">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $review['rating']) {
                                    echo '<span class="hCCjke"><i class="fa-solid fa-star"></i></span>';
                                } else {
                                    echo '<span class="neutro"><i class="fa-solid fa-star"></i></span>';
                                }
                            }
                            ?>
                        </span>
                        <span class="rsqaWe"><?php echo $review['relative_time_description'] ?></span>
                    </div>
            <?php
                    echo '
                <p class="text">' . $review['text'] . '</p>
                <a href="' . $review['author_url'] . '" target="_blank">Ver más</a>
                </div>
                    ';
                }
            } else {
                echo "No se pudieron obtener las reseñas.";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
