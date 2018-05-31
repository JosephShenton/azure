<?php 
	session_start();
	if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
		header('Location: timetable.php');
	} else {
		header('Location: login.php');
	}
?>