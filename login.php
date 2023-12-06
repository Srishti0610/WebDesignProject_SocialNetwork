<?php
	session_start();
	if (isset($_GET['logout'])) {
		session_unset();
        session_destroy();
        header("Location: login.php"); 
	}
	else if (isset($_SESSION['username'])) {
		header("Location: social_network.php");
	}
	else if($_SERVER["REQUEST_METHOD"] === 'POST')
	{
		$username = $_POST['username'];
		$password = $_POST['password'];

        error_reporting(E_ALL);
        ini_set('display_errors','On');

        try {
            $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=SocialNetwork",
            "root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $dbh->beginTransaction();
            $stmt = $dbh->prepare('select * from users where username="' .$username . '" and password="' . md5($password).'"');
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() == 1) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                header("Location: social_network.php");
            }
            else
            {
                $_SESSION['errorMessage'] = "Incorrect username or password! Try again!";
            }
        } catch (PDOException $e) {
            die();
        }
		
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <style>
        .loginContainer {
            width: 500px;
            padding: 16px;
            background-color: white;
            margin: 0 auto;
            margin-top: 150px;
            border: 1px solid black;
            border-radius: 4px;
        }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .loginContainer button {
            background-color: #8A0028;
            font-weight: bold;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .loginContainer button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <form class="loginContainer" action="login.php" method="POST">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>

        <button type="submit">Login</button>
    </form>

</body>
<script>
    function showAlert() {
        let message = "<?php
            if (isset($_SESSION['errorMessage']))
            {
                echo $_SESSION['errorMessage'];
                session_unset();
                session_destroy();
            }
        ?>";
        if (message !== "") {
            alert(message);
        }
    }
    document.addEventListener("DOMContentLoaded", showAlert);
</script>
</html>