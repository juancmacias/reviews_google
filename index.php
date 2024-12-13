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

                echo $data['result']['rating'];
                echo '<div class="estrellas">';
                for ($i = 0; $i < $data['result']['rating']; $i++) {
                    echo '<span class="gral" ><i class="fa-solid fa-star"></i></span>';
                }
                $total = count($data['result']['reviews']);
                echo '</div> de ' . $total . ($total < 1 ? ' reseñas' : ' reseña') . '.</div>';

                $telefono = "";
                if(isset($data['result']['formatted_phone_number'])){
                    $telefono = '<a href="tel:+34'.$data['result']['formatted_phone_number'] .'" title="Llamar al '. $data['result']['name'] .'">'.$data['result']['formatted_phone_number'] .'</a> ';
                }
                echo '<div class="contacto">'. $telefono .' <a href="'.$data['result']['website'] .'" target="_top" title="Visitar la web de '.  $data['result']['name'] .'">'.$data['result']['website'] .'</a>'.'</div>';
                echo '<div class="reviews">';
                // Iterar sobre las reseñas
                // Ordenar las reseñas por el campo 'time'

                usort($data['result']['reviews'], function($a, $b) {
                    return $b['time'] >= $a['time']; // Orden ascendente
                });
                // Filtrar reseñas con rating superior a 3
                $filteredReviews = array_filter($data['result']['reviews'], function($review) {
                    return $review['rating'] > 3;
                });

                // Ordenar las reseñas filtradas por rating en orden descendente
                usort($filteredReviews, function($a, $b) {
                    return $b['rating'] <=> $a['rating'];
                });
                // Tomar solo los primeros 10 elementos
                $topReviews = array_slice($filteredReviews, 0, 10);
                foreach ($topReviews as $review) {
                    echo '
                 <div class="review">
                <img src="' . $review['profile_photo_url'] . '" alt="Foto de ' . $review['author_name'] . '" class="profile-photo">
                <h2>' . $review['author_name'] . '</h2>';
            ?>
                    <div class="rating">
                        <i class="fa-regular fa-star"></i>
                        <span class="kvMYJc" role="img" aria-label="5 estrellas">
                            <?php
                            $res = 5 - $review['rating'];
                            for ($i = 1; $i < $review['rating']; $i++) {
                            ?>
                                <span class="hCCjke  NhBTye elGi1d"><i class="fa-solid fa-star"></i></span>
                            <?php
                            }
                            for ($i = 1; $i < $res; $i++) {
                            ?>
                                <span class="neutro"><i class="fa-regular fa-star"></i></span>
                            <?php
                            }
                            ?>

                        </span>
                        <span class="rsqaWe"><?php echo $review['relative_time_description'] ?></span>
                        <div class="Tuisuc"></div>
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
