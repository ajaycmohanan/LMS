<?php
	include('../dBConfig.php');
	if (isset($_GET['userid'])) {
		$id = $_GET['userid'];
		$sql = 'UPDATE members SET status = :status WHERE mid = :id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(['status' => 0, 'id' => $id]);
		$count = $stmt -> rowCount();
		if ($count > 0) {
		    header('location:manage-members.php');
		} else {
		    //DataBase Deletion Failed
		}
	}
?>
