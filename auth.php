
<?php
	if(count($_POST)>0) {
		$conn = mysqli_connect("127.0.0.1","root");
		$result = mysqli_query($conn,"SELECT * FROM test.users WHERE usr='" . $_POST["uname"] . "' and psw = '". $_POST["psw"]."'");
		$count  = mysqli_num_rows($result);
		if($count==0) {
			$message = "Invalid Username or Password!\n";
			header("Location: login.html?psw=false"); 
			exit();
		} else {
			$message = "You are successfully authenticated!<br>";
			$row = mysqli_fetch_array($result);
			setcookie("user", $row["usr"], time() + (3600/4), "/"); // 3600/4 = 15 minutes
			if ($row["role"] == "Admin"){
				header("Location: main_admin.php"); 
			} else {
				header("Location: main.php"); 
			}
			exit();
		}
	} else {
		header("Location: login.html"); 
		exit();
	}
?>

