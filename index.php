<?php
    include("classes/Seo.php");
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>SEO</title>
</head>
<body class="bg-light">

<main role="main" class="container">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="my-3 p-3 bg-white rounded shadow-sm">

                <h6 class="border-bottom border-gray pb-2 mb-0">Enter The Of The Site You Would Like A Report On.</h6>

                <form action="/" method="post">

                    <div class="input-group">

                        <input type="text" name="url" class="form-control" placeholder="http or https://www." aria-label="Recipient's username" aria-describedby="basic-addon2">

                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-info" type="submit">Search</button>
                        </div>

                    </div>

                </form>
                <hr>
                <?php

                if($_SERVER['REQUEST_METHOD'] == "POST") {

                    $url = $_POST['url'];

                    $seo = new Seo($url);
                    $speed = $seo->run();
                    $tags      = $seo->getTags();
                    $meta_tags = $seo->getMetaTags();
                    $words     = $seo->getWordCount();

                    $speed = json_decode($speed, 1);
                    echo "Speed: " . $speed['time'] . '<br>';

                    $words = json_decode($words, 1);
                    echo "Word Count: " . $words . '<br>';

                   $tags = json_decode($tags, 1);

                    echo "<h4>Tags Searched For</h4>";

                    foreach ($tags as $key => $tag) {

                        $title_length = null;

                        if($key == 'title') {
                            $title_length = "The Number Of Characters In The Title Is " . $tag['titleLength'];
                        }

                        echo 'Number of &lt;' . $key . '&gt; Tags' . ' ' . $tag['numberOfTags'] . ' ' . $title_length . "<br>";
                    }


                    echo "<h4>Meta Tags Found</h4>";

                    $meta = json_decode($meta_tags, 1);

                    foreach ($meta as $key => $tag) {

                        echo "<strong>" . $key . "</strong>" . ' ' . $tag . "<br>";
                    }

                }
                ?>


            </div>
        </div>
    </div>
</main>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>


</body>
</html>