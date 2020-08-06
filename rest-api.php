<?php

    $connect = new mysqli('localhost', 'root', '');

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['id'])) { /* ..meme/id/[0-9]+ */
            // Check if data exists
            $id = $connect->real_escape_string($_GET['id']);
            $sql = "SELECT * FROM agsmartit.images WHERE id='$id'";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }

            $array = array();
            while($rows = mysqli_fetch_assoc($result)) {
                $array[] = $rows;
            }

            if(empty($array)) {
                die("This data is not found in the database.");
            }

            // Display data
            header('Content-type: text/javascript');
            echo json_encode($array, JSON_PRETTY_PRINT);

            // Update request count for requested data
            $sql = "UPDATE agsmartit.images SET requestCount = requestCount + 1 WHERE id = '$id'";
            $result = mysqli_query($connect, $sql);
            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }
        }

        if(isset($_GET['all'])) { /* ..meme/all */
            // Check if data exists
            $sql = "SELECT * FROM agsmartit.images";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }

            $array = array();
            while($rows = mysqli_fetch_assoc($result)) {
                $array[] = $rows;
            }
            
            // Display data
            header('Content-type: text/javascript');
            echo json_encode($array, JSON_PRETTY_PRINT);

            // Update request count for requested data
            $sql = "UPDATE agsmartit.images SET requestCount = requestCount + 1";
            $result = mysqli_query($connect, $sql);
            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }
        }

        if(isset($_GET['page'])) { /* ..meme/page/[0-9]+ */
            // Check if data exists
            $page = $connect->real_escape_string($_GET['page']);
            $sql = "SELECT * FROM agsmartit.images WHERE page='$page'";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }

            $array = array();
            while($rows = mysqli_fetch_assoc($result)) {
                $array[] = $rows;
            }

            if(empty($array)) {
                die("This data is not found in the database.");
            }

            // Display data
            header('Content-type: text/javascript');
            echo json_encode($array, JSON_PRETTY_PRINT);

            // Update request count for requested data
            $sql = "UPDATE agsmartit.images SET requestCount = requestCount + 1 WHERE page = '$page'";
            $result = mysqli_query($connect, $sql);
            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }
        }

        if(isset($_GET['popular'])) { /* ..meme/popular */
            // Find out the highest request count
            $sql = "SELECT MAX(requestCount) FROM agsmartit.images";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }

            $rows = mysqli_fetch_array($result);
            if(!$rows) {
                die("Retrieving highest request count failed: ". mysqli_connect_error());
            }

            $highestCount = $rows[0];

            // Retrieve data with highest request count
            $sql = "SELECT * FROM agsmartit.images WHERE requestCount = '$highestCount'";
            $result = mysqli_query($connect, $sql);

            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }

            $array = array();
            while($rows = mysqli_fetch_assoc($result)) {
                $array[] = $rows;
            }

            if(empty($array)) {
                die("This data is not found in the database.");
            }

            // Display data
            header('Content-type: text/javascript');
            echo json_encode($array, JSON_PRETTY_PRINT);

            // Update request count for requested data
            $sql = "UPDATE agsmartit.images SET requestCount = requestCount + 1 WHERE requestCount = '$highestCount'";
            $result = mysqli_query($connect, $sql);
            if(!$result) {
                die("Query failed: " . mysqli_connect_error());
            }
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['submit'])) {
            $name = $_POST['name'];
            $url = $_POST['url'];

            if(empty($name) || empty($url)) {
                header("Location: ./meme/create?error=emptyfields");
                exit();
            }

            else if(!preg_match('/\.(jpg|png|jpeg)$/', $url)) {
                header("Location: ./meme/create?error=invalidurl");
                exit();
            }

            else {
                // Find highest id
                $sql = "SELECT MAX(id) FROM agsmartit.images";
                $result = mysqli_query($connect, $sql);

                if(!$result) {
                    die("Query failed: " . mysqli_connect_error());
                }

                $rows = mysqli_fetch_array($result);
                if(!$rows) {
                    die("Retrieving highest ID failed: ". mysqli_connect_error());
                }

                $id = $rows[0] + 1;

                // Find highest page
                $sql = "SELECT MAX(page) FROM agsmartit.images";
                $result = mysqli_query($connect, $sql);

                if(!$result) {
                    die("Query failed: " . mysqli_connect_error());
                }

                $rows = mysqli_fetch_array($result);
                if(!$rows) {
                    die("Retrieving highest page failed: ". mysqli_connect_error());
                }

                $potentialPage = $rows[0];

                // Check if page is full
                $sql = "SELECT COUNT(*) FROM agsmartit.images WHERE page = '$potentialPage'";
                $result = mysqli_query($connect, $sql);

                if(!$result) {
                    die("Query failed: " . mysqli_connect_error());
                }

                $rows = mysqli_fetch_array($result);
                if(!$rows) {
                    die("Retrieving page content total failed: ". mysqli_connect_error());
                }

                $pageContent = $rows[0];
               
                if($pageContent >= 9) {
                    $page = $potentialPage + 1;
                }
                
                else {
                    $page = $potentialPage;
                }

                $sql = "INSERT INTO agsmartit.images (id, name, url, page, requestCount) VALUES ('$id', '$name', '$url', $page, 0)";
                $result = mysqli_query($connect, $sql);

                if(!$result) {
                    die("Query failed: " . mysqli_connect_error());
                }

                else {
                    header("Location: ./meme/create?newdata=success");
                    exit();
                }
            }
        }
    }

    mysqli_close($connect);
?>