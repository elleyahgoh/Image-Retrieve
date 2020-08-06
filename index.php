<?php

    $home_url = "https://interview.agsmartit.com/index.php";

    require ('simple_html_dom.php');

    // Establish connection to database
    $connect = new mysqli('localhost', 'root', '');
    if(!$connect) {
        die("Connection to server failed: " . mysqli_connect_error());
    }

    // Create database if it does not exist yet
    $sql = "CREATE DATABASE IF NOT EXISTS agsmartit";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        die("Failed to create database: " . mysqli_connect_error());
    } 

    // Create images table if it does not exist yet 
    $sql = "SELECT * FROM agsmartit.images";
    $result = mysqli_query($connect, $sql);

    if (!$result) {
        $sql = "CREATE TABLE agsmartit.images ( id INT NOT NULL PRIMARY KEY, name TEXT NOT NULL , url TEXT NOT NULL , page INT NOT NULL , requestCount INT NOT NULL )";
        $result = mysqli_query($connect, $sql);
        if(!$result) {
            die("Query failed: " . mysqli_connect_error());
        } 

        $url = $home_url;
        $url_html = file_get_html($url);
        $id = 0;
        $page = 1;

        // Inspect page for images
        while($url_html->find('img')) {

            $array_add = 0;
            
            // Extract image URL
            foreach($url_html->find('.meme-frame') as $parent_class) {
                foreach($parent_class->find('img') as $target_name) {
                    $result = $target_name->attr['src'];
                    $url_list[] = $result;
                    $array_add++;
                }
            }

            // Extract image name
            foreach($url_html->find('.meme-name') as $parent_class) {
                foreach($parent_class->find('h6') as $target_name) {
                    $result = $target_name->plaintext;
                    $name_list[] = $result;
                }
            }

            // Store results into database
            for($i = 0; $i < $array_add; $i++) {
                $id = $id + 1;
                $sql = "INSERT INTO agsmartit.images (id, name, url, page, requestCount) VALUES ('$id', '$name_list[$i]', '$url_list[$i]', $page, 0)";
                $result = mysqli_query($connect, $sql);
                if (!$result) {
                    die("Query failed: " . mysqli_connect_error());
                }
            }

            // Empty collecting arrays
            unset($url_list);
            unset($name_list);

            // Get URL of new page
            $page++;
            $url = $home_url . "?page=" . $page;
            $url_html = file_get_html($url);
        }

        // Create updates table if it does not exist yet 
        $sql = "SELECT * FROM agsmartit.updates";
        $result = mysqli_query($connect, $sql);

        if(!$result) {
            $sql = "CREATE TABLE agsmartit.updates ( id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, updateDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)";
            $result = mysqli_query($connect, $sql); 

            if(!$result) {
                die("Query failed: ". mysqli_connect_error());
            }

            $sql = "INSERT INTO agsmartit.updates VALUES (NULL, NULL)";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: ". mysqli_connect_error());
            }
        }
    }
    
    if(isset($_GET['id-submit'])) {
        $id = $_GET['id'];
        
        if(empty($id)) {
            header("Location: meme?error=noid") ;
            exit();
        }
        header("Location: meme/id/" . $id);
        exit();
    }
    
    if(isset($_GET['page-submit'])) {
        $page = $_GET['page'];
        
        if(empty($page)) {
            header("Location: meme?error=nopage") ;
            exit();
        }
        header("Location: meme/page/" . $page);
        exit();
    }
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Image Retrieve</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">
        <link rel="stylesheet" href="styles.css">
    </head>

    <body>
        <div class="position container d-flex align-items-center justify-content-center">
            <div class="form">
                <p><span class="font-weight-bold">Data retrieved from: </span><a href="https://interview.agsmartit.com/index.php">https://interview.agsmartit.com/index.php</a></p>
                
                <?php
                    $sql = "SELECT updateDate from agsmartit.updates ORDER BY id DESC LIMIT 1 OFFSET 0";
                    $result = mysqli_query($connect, $sql);

                    if(!$result) {
                        die("Retrieving update date failed: ". mysqli_connect_error());
                    }

                    else {
                        $rows = mysqli_fetch_array($result);
                        if(!$rows) {
                            die("Retrieving update date failed: ". mysqli_connect_error());
                        }

                        echo "<p><span class='font-weight-bold'>Last updated: </span>" . $rows['updateDate'] . "</p>";
                    }
                ?>

                <button id="allButton" class="d-block mb-2">Get all image data</button>
                
                <form method="get" class="mb-2">
                    <input type="text" name="id" placeholder="Enter image ID"></input>
                    <input type="submit" name="id-submit">
                </form>
                
                <form method="get" class="mb-2">
                    <input type="text" name="page" placeholder="Enter page"></input>
                    <input type="submit" name="page-submit">
                </form>
                
                <button id="popularButton" class="d-block mb-2">Get popular image data</button>

                <button id="createButton" class="d-block mb-2">Add new image data</button>
            </div>
        </div>
    </body>

    <script type="text/javascript">
        document.getElementById("allButton").onclick = function () {
            location.href = "./meme/all";
        };
    </script>
    
    <script type="text/javascript">
        document.getElementById("popularButton").onclick = function () {
            location.href = "./meme/popular";
        };
    </script>

    <script type="text/javascript">
        document.getElementById("createButton").onclick = function () {
            location.href = "./meme/create";
        };
    </script>
</html>