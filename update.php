<?php 
	
	$btnSearchClicked = isset($_POST['btnSearch']);
	$statuses = [];
	$car = null;

	if ($btnSearchClicked == true) {
		require_once 'db.php';
		$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

		if ($conn -> connect_error) {
			die ("Connection failed: ". $conn -> connect_error);
		}

		$carId = $_POST['patrolCarId'];

		$sql = "SELECT * FROM patrolcar WHERE patrolcar_id = '".$carId."'";
		$result = $conn -> query($sql);

		if ($row = $result -> fetch_assoc()) {
			$id = $row['patrolcar_id'];
			$statusId = $row['patrolcar_status_id'];
			$car = ["id" => $id, "statusId" => $statusId];
		}

		$sql = "SELECT * FROM patrolcar_status";
		$result = $conn -> query($sql);

		while ($row = $result -> fetch_assoc()) {
			$id = $row['patrolcar_status_id'];
			$desc = $row['patrolcar_status_desc'];
			$status = ["id" => $id, "desc" => $desc];

			array_push($statuses,$status);
		}
		$conn -> close();

	}

	$btnUpdateClicked = isset($_POST['btnUpdate']);

	if ($btnUpdateClicked == true) {
		require_once 'db.php';
		$updateSuccess = false;

		$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

		if ($conn -> connect_error) {
			die("Connection failed: ". $conn -> connect_error);
		}

		$newStatusId = $_POST['carStatus'];
		$status = $row['patrolcar_status_desc'];
		$carId = $_POST['patrolCarId'];

		$sql = "UPDATE patrolcar SET patrolcar_status_id = '". $newStatusId ."' WHERE patrolcar_id = '". $carId ."'";
		$updateSuccess = $conn -> query($sql);
		if ($updateSuccess === false) {
			echo "Error: " . $sql . "<br>" . $conn -> error;
		}

	else if ($newStatusId == '3') {
		$sql = "SELECT incident_id FROM dispatch WHERE time_completed is NULL AND patrolcar_id = '". $carId ."'";
		$result = $conn -> query($sql);

		$incidentId = 0;
		if ($result -> num_rows > 0) {
			if ($row = $result -> fetch_assoc()) {
				$incidentId = $row['incidentId'];
			}
		}

		$sql = "UPDATE dispatch SET time_completed = NOW() WHERE time_completed is NULL AND patrolcar_id = '". $carId ."'";
		$updateSuccess = $conn -> query($sql);
		if ($updateSuccess === false) {
			echo "Error: " . $sql . "<br>" . $conn -> error;
		}

		$sql = "UPDATE incident SET incident_status_id = '3' WHERE incident_id = '". $incidentId ."'";
		$updateSuccess = $conn -> query($sql);
		if ($updateSuccess === false) {
			echo "Error: " . $sql . "<br>" . $conn -> error;
		}
	}

	$conn -> close();

	

		if ($updateSuccess === TRUE && $newStatusId == 1) 
	{
		echo "<script> window.location.href = 'search.php?message=success?carID=$carId&newStatusId=1';alert('Patrol car $carId status has been updated to Dispatched.');</script>";
	}
	else if ($updateSuccess === TRUE && $newStatusId == 2) 
	{
		echo "<script> window.location.href = 'search.php?message=success?carID=$carId&newStatusId=2';alert('Patrol car $carId status has been updated to Patrol.');</script>";
	}
	else if ($updateSuccess === TRUE && $newStatusId == 3) 
	{
		echo "<script> window.location.href = 'search.php?message=success?carID=$carId&newStatusId=3';alert('Patrol car $carId status has been updated to Free.');</script>";
	}
	else if ($updateSuccess === TRUE && $newStatusId == 4) 
	{
		echo "<script> window.location.href = 'search.php?message=success?carID=$carId&newStatusId=4';alert('Patrol car $carId status has been updated to Arrived.');</script>";
	}





	
	
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Update Patrol Car Status</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body>
	<div class="container" style="width: 80%;">
		<?php require_once 'nav.php'; ?>
		<aside>
			<h2>BONUS POINTS</h2>
			<ul>
				<li>To create a notification e.g. alert or text, when status has been updated</li>
			</ul>
			<p></p>
		</aside>
		<section style="margin-top: 20px">
			<form action="update.php" method="POST">
				<?php
				if ($car != null) {
					echo '<div class="form-group row">';
					echo ' <label for="patrolCarId" class="col-sm-4 col-form-label">Car Number</label>';
					echo ' <div class="col-sm-8">';
					echo $car['id'];
					echo '      <input type="hidden" name="patrolCarId" id="patrolCarId" value="'.$car['id'].'">';
					echo '  </div>';
					echo '</div>';
					echo '<div class="form-group row">';
					echo '   <label for="carNo" class="col-sm-4 col-form-label">Patrol Car Status</label>';
					echo '  <div class="col-sm-8">';
					echo '     <select id="carStatus" class="form-control" name="carStatus">';
								$totalStatus = count($statuses);
								for ($i=0; $i<$totalStatus; $i++) 
								{ 
									$status = $statuses[$i];
									$selected = "";
									if ($status['id'] == $car['statusId']) 
									{
										$selected = ' selected="selected"';
									}
									echo '<option value="'.$status['id'].'"'.$selected.">".$status['desc'].'</option>';
											$selected = "";
								}

					echo '         </select>';
					echo '   </div>';
					echo ' </div>';
				} else { 
					echo '<div class="form-group row">';
					echo '  <div class="col-sm-12">No records found.</div>';
					echo '</div>';
				 
				}
				?>
					<div class="form-group row">
						<div class="col-sm-4"></div>
						<div class="col-sm-8">
							<input type="submit" name="btnUpdate" value="Update" class="btn btn-primary">
						</div>
					</div>
			</form>
		</section>
		<footer class="page-footer font-small blue pt-4 footer-copyright text-center py-3">&copy; 2020 Copyright</footer>
	</div>
	<script type="text/javascript" src="js/jquery-3.5.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/popper.min.js"></script>
</body>
</html>