<?php

    session_start();
    error_reporting(-1);
    ini_set("display_errors", 1);

    require_once('../config.inc.php');

    $dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

    #create query
    $quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
    $qposts = $dbh->prepare('SELECT * FROM posts');
    $qposter_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');

    #if someone is logged in, retrieve his/her user-information
    if (isset($_SESSION['user_id'])){
        $quser_info->execute(array($_SESSION['user_id']));
        $user_info = $quser_info->fetch();
    }

?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width"/>
        <link rel="stylesheet" href="../css/reset.css" />
        <link rel="stylesheet" href="../css/main.css">
        <script type='text/javascript' src='../js/jquery.particleground.js'></script>
        <script type='text/javascript' src='../js/background.js'></script>
        <title>Cryptus</title>
    </head>
    <body id="body">
        <?php
            echo '<div id="header">';
                
                echo '<div class="navbar">';
                    echo '<img class="left" id="logo-navbar" src="../images/logo.png" href="../index.php">';
                    echo '<a class="left" href="../index.php">Cryptus</a>';
                    if (isset($_SESSION['user_id'])){
                        echo '<div class="dropdown right">';
                            echo '<button class="dropbtn right">' . $user_info["user_name"] . '<div class="arrow-down"></div></button>';
                            echo '<div class="dropdown-content">';
                                echo '<a href="' . 'profile.php?user_id=' . $_SESSION['user_id'] . '">My profile</a>';
                                echo '<a href="settings.php">Settings</a>';
                                echo '<a href="logout.php">Logout</a>';
                            echo '</div>';
                        echo '</div>';
                    }else{
                        echo '<a class="right" href="index.php">Login</a>';
                    }
                    echo '<form action="search.php" method="POST"><input id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input"></form>';
                echo '</div>';

                echo '<div class="navbar">';
                    echo '<a class="nav-item left" href="timeline.php">Timeline</a>';
                    echo '<a class="nav-item left" id="current" href="connections.php">Connections</a>';
                    echo '<a class="nav-item right" href="../threads/index.php">Discussions</a>';
                    echo '<a class="nav-item right" href="portfolio.php">Portfolio</a>';
                echo '</div>';

            echo '</div>';

            if(isset($_SESSION['user_id'])){
                echo '<div class="profile-picture-header-div">';
                    $jpgimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpg';
                    $jpegimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpeg';
                    $pngimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'png';
                    if (file_exists ($jpgimg)){
                        echo '<img id="profile-picture-header" src="' . $jpgimg . '"/>';
                    } elseif (file_exists ($jpegimg)){
                        echo '<img id="profile-picture-header" src="' . $jpegimg . '"/>';
                    } elseif (file_exists ($pngimg)){
                        echo '<img id="profile-picture-header" src="' . $pngimg . '"/>';
                    } else {
                        echo '<img id="profile-picture-header" src="../profile-pictures/default.jpg"/>';
                    }
                echo '</div>';
            }
        ?>

        <div class="container-timeline">
            <?php
                if(isset($_SESSION['user_id'])){

                    echo '<a style="color:black;">Friends:<br><br></a>';                        

                    $qh = $dbh->prepare('SELECT * FROM Users JOIN relationships ON user_one_id = Users.user_id OR user_two_id = Users.user_id WHERE status = 1 AND (user_one_id = ? OR user_two_id = ?) GROUP BY user_name'); 
                    $qh->execute(array($_SESSION['user_id'], $_SESSION['user_id']));
                    $friends = $qh->fetchAll();
                    foreach ($friends as $key => $friend) {
                        if ($friend['user_id'] == $_SESSION['user_id']) continue;
                        echo '<div class="content-div">';

                            echo '<div class="content-meta-div">';
                                $jpgimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'jpg';
                                $jpegimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'jpeg';
                                $pngimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'png';
                                if (file_exists ($jpgimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
                                } elseif (file_exists ($jpegimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
                                } elseif (file_exists ($pngimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
                                } else {
                                    echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
                                }
                                echo '<a class="content-poster-name" href="' . 'profile.php?user_id=' . $friend['user_id'] . '">' . $friend['user_name'] . '</a></br>';
                 

                        echo '</div>';
                    }
                }else{
                    echo '<p class="error-message"><a href="../index.php">Login</a> to view or create your connections.</p>';
                }

                if(isset($_SESSION['user_id'])){

                    echo '<a style="color:black;">Friend requests:<br><br></a>';

                    $qh = $dbh->prepare('SELECT * FROM Users JOIN relationships ON user_one_id = Users.user_id OR user_two_id = Users.user_id WHERE status = 0 AND (user_one_id = ? OR user_two_id = ?) AND action_user_id != ? GROUP BY user_name'); 
                    $qh->execute(array($_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'] ));
                    $friends = $qh->fetchAll();
                    foreach ($friends as $key => $friend) {
                        if ($friend['user_id'] == $_SESSION['user_id']) continue;
                        echo '<div class="content-div">';

                            echo '<div class="content-meta-div">';
                                $jpgimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'jpg';
                                $jpegimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'jpeg';
                                $pngimg = '../profile-pictures/' . $friend['user_id'] . '.' . 'png';
                                if (file_exists ($jpgimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
                                } elseif (file_exists ($jpegimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
                                } elseif (file_exists ($pngimg)){
                                    echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
                                } else {
                                    echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
                                }
                                echo '<a class="content-poster-name" href="' . 'profile.php?user_id=' . $friend['user_id'] . '">' . $friend['user_name'] . '</a></br>';
                 

                        echo '</div>';
                    }
                }
            ?>
        </div>
        
    </body>
</html>