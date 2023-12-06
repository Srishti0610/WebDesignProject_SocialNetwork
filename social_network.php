<?php 
    session_start(); 
    if(!isset($_SESSION['username'])) 
    { 
        header("Location: login.php"); 
    } 
    else{
        try {
            $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=SocialNetwork",
            "root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $dbh->beginTransaction();
            $stmt = $dbh->prepare('select * from users where username!="' .$_SESSION['username'] . '"');
            $stmt->execute();
            $rowList = array();

            // Get all the entries from the users table other than logged user
            while ($row = $stmt->fetch()) {
                $rowList[$row['username']]=$row;
            }
            
            
            // Get all the entries from the friends table for logged user
            $stmt2 = $dbh->prepare('select * from friends where user="' .$_SESSION['username'] . '"');
            $stmt2->execute();
            $rowList2 = array();
            while ($row2 = $stmt2->fetch()) {
                $rowList2[$row2['friend']]=$rowList[$row2['friend']];
            }
            $_SESSION['rows2'] = $rowList2;

            // Get all the entries from the friends table for logged user
            $rowList3 = $rowList;

            
            foreach ($rowList as $data){
                foreach ($rowList2 as $friend){
                    $fullname = $friend['username'];
                    if ($data['username'] == $fullname) {
                        unset($rowList3[$data['username']]);
                    }
                }
            }
            
            $_SESSION['rows'] = $rowList3;  
            
            
        } 
        catch (PDOException $e) {
            die();
        }

    }
    if (isset($_GET['remove'])) {
        $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=SocialNetwork",
               "root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $dbh->beginTransaction();
        $dbh->exec('delete from friends where user="' .$_SESSION['username'] . '" and friend="' .$_GET['remove'] . '"');
        $dbh->commit();
        header("Location: social_network.php");
	}
    if (isset($_GET['add'])) {
        $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=SocialNetwork",
               "root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $dbh->beginTransaction();
        $dbh->exec('insert into friends values("' .$_SESSION['username'] . '","' . $_GET['add'] . '")')
            or die(print_r($dbh->errorInfo(), true));
        $dbh->commit();
        header("Location: social_network.php");
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network Grid Layout</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .mainContent {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        .titleDiv {
            text-align: center;
            padding-bottom: 70px;
        }

        .userDetails {
            float: left;
            width: 20%;
            padding: 10px;
        }

        .friendList {
            display: flex;
            float: right;
            width: 70%;
            padding: 10px;
        }

        .grid {
            width: 48%;
        }

        .friend, .other {
            padding: 10px;
        }

        .friend .username, .other .username {
            font-size: 16px;
        }

        .friend .fullname, .other .fullname {
            font-size: 14px;
        }

        .friend .email, .other .email {
            font-size: 14px;
        }

        .friend a, .other a {
            text-decoration: none;
        }
        .sociallogoutbtn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF0000;
            color: #FFF;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
        }

        .sociallogoutbtn:hover {
            background-color: #CC0000; 
        }
        .buttonAddRemove {
            display: inline-block;
            padding: 4px 10px;
            background-color: #0074cc; 
            color: #FFF; 
            text-decoration: none;
            border: 1px solid #0074cc; 
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .buttonAddRemove:hover {
            background-color: #FFF; 
            color: #0074cc; 
        }

    </style>
</head>
<body>
<div class="mainTitle">
    <div class="titleDiv">
        <h1>Social Network</h1>
    </div>
    <div class="userDetails">
        <h2>Hello, <?php echo $_SESSION['username'];?></h2>
        <h2>Your Details:</h2>
        <h3 class="username">Username: <?php echo $_SESSION['username'];?></h3>
        <h3 class="fullname">Full Name: <?php echo $_SESSION['fullname'];?></h3>
        <p class="email">Email: <?php echo $_SESSION['email'];?></p>
        <br/>
        <a class="sociallogoutbtn" href="login.php?logout=true">Log Out</a> 
    </div>
    <div class="friendList">
        <div class="grid">
            <h2>Friends List</h2>
            <?php 
                foreach ($_SESSION['rows2'] as $username => $data) {
                    echo '<form class="friend" method="GET" action="social_network.php">';
                    echo '<h3 class="username">' . $data['username'] . '</h3>';
                    echo '<p class="fullname">' . $data['fullname'] . '</p>';
                    echo '<p class="email">Email: ' . $data['email'] . '</p>';
                    echo '<input type="hidden" name="remove" value="' . $data['username'] . '">';
                    echo '<button class="buttonAddRemove" type="submit">Remove</button>';
                    echo '</form>';
                }         
            ?>
        </div>
        <div class="grid">
            <h2>Other Users List</h2>
            <?php 
                foreach ($_SESSION['rows'] as $username => $data) {
                    echo '<form class="other" method="GET" action="social_network.php">';
                    echo '<h3 class="username">' . $data['username'] . '</h3>';
                    echo '<p class="fullname">' . $data['fullname'] . '</p>';
                    echo '<p class="email">Email: ' . $data['email'] . '</p>';
                    echo '<input type="hidden" name="add" value="' . $data['username'] . '">';
                    echo '<button class="buttonAddRemove" type="submit">Add</button>';
                    echo '</form>';
                }      
            ?>
        </div>
    </div>
</div>
</body>
</html>



