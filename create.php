<!DOCTYPE html>
<html lang="en">
    <head>
       <title>Image Retrieve</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">
        <link rel="stylesheet" href="../styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    </head>

    <body>
        <div class="position container d-flex align-items-center justify-content-center">
            <div class="form">
                <form method="post" action="../rest-api.php" class="mb-2">
                    <p>Submit the new image data</p>
                    <?php
                        $check_url = $_SERVER['REQUEST_URI'];
                        if(preg_match('/(newdata=success)/', $check_url)) {
                            echo "<p>Submission successful!</p>";
                        }
                    ?>
                    <input type="text" name="name" placeholder="Image name"></input>
                    <input type="text" name="url" placeholder="Image URL"></input>
                    <input type="submit" name="submit">
                </form>
                
                <p></p><a href="..">Back</a></p>
            </div>
        </div>
    </body>
</html>