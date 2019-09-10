<?php
    setcookie("user", null, time() - (3600/4), "/");
    header("Location: login.html"); 
	exit();
?>