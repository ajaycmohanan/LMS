<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    $userid = $_SESSION['userid'];
    $sql = 'SELECT name FROM librarian WHERE adminid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];
?>
<!DOCTYPE html>
<html>
<head>
	<?php include('include/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/dataTables.bootstrap4.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/responsive.dataTables.css">
</head>
<body>
	<?php include('include/header.php'); ?>
	<?php include('include/sidebar.php'); ?>
	<div class="main-container">
		<div class="pd-ltr-20 customscroll customscroll-10-p height-100-p xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Manage Members</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Home</a></li>
									<li class="breadcrumb-item"><a href="index.php">Members</a></li>
									<li class="breadcrumb-item active" aria-current="page">Manage</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<!-- Export Datatable start -->
				<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">
					<div class="row">
						<table class="stripe hover multiple-select-row data-table-export nowrap" id="user_data">
							<thead>
								<tr>
									<th>User Id</th>
									<th>Name</th>
									<th class="table-plus datatable-nosort">Email</th>
									<th class="table-plus datatable-nosort">Phone No.</th>
									<th>Aadhar</th>
									<th class="table-plus datatable-nosort">Address</th>
									<th class="table-plus datatable-nosort">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql = 'SELECT * FROM members WHERE adminid = :userid AND status = :status';
									$stmt = $pdo -> prepare($sql);
									$stmt -> execute(['userid' => $userid, 'status' => 1]);
									while ( $rows = $stmt -> fetch(PDO::FETCH_ASSOC)) {
										echo '
										    <tr>
										        <td>'.$rows['mid'].'</td>
										        <td>'.$rows['name'].'</td>
										        <td>'.$rows['email'].'</td>
										        <td>'.$rows['phno'].'</td>
										        <td>'.$rows['aadhar'].'</td>
										        <td>'.$rows['hname'].",".$rows['place'].'</td>
										        <td>
										        	<div class="dropdown">
										        		<a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
										        					<i class="fa fa-ellipsis-h"></i>
										        		</a>
										        		<div class="dropdown-menu dropdown-menu-right">
										        			<a class="dropdown-item" href="view-member.php?userid='.$rows['mid'].'"><i class="fa fa-eye"></i> View</a>
										        			<a class="dropdown-item" href="edit-member.php?userid='.$rows['mid'].'"><i class="fa fa-pencil"></i> Edit</a>
										        			<a class="dropdown-item" href="delete-member.php?userid='.$rows['mid'].'"><i class="fa fa-trash"></i> Delete</a>
										        		</div>
										        	</div>
										        </td>
										    </tr>
										';
									};
                        		?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- Export Datatable End -->
			</div>
			<?php //include('include/footer.php'); ?>
		</div>
	</div>
	<?php include('include/script.php'); ?>
	<script src="src/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="src/plugins/datatables/media/js/dataTables.bootstrap4.js"></script>
	<script src="src/plugins/datatables/media/js/dataTables.responsive.js"></script>
	<script src="src/plugins/datatables/media/js/responsive.bootstrap4.js"></script>
	<!-- buttons for Export datatable -->
	<script src="src/plugins/datatables/media/js/button/dataTables.buttons.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.bootstrap4.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.print.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.html5.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.flash.js"></script>
	<script src="src/plugins/datatables/media/js/button/pdfmake.min.js"></script>
	<script src="src/plugins/datatables/media/js/button/vfs_fonts.js"></script>
	<script>
		$('document').ready(function(){
			$('.data-table').DataTable({
				scrollCollapse: true,
				autoWidth: false,
				responsive: true,
				columnDefs: [{
					targets: "datatable-nosort",
					orderable: false,
				}],
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"language": {
					"info": "_START_-_END_ of _TOTAL_ entries",
					searchPlaceholder: "Search"
				},
			});
			$('.data-table-export').DataTable({
				scrollCollapse: true,
				autoWidth: false,
				responsive: true,
				columnDefs: [{
					targets: "datatable-nosort",
					orderable: false,
				}],
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"language": {
					"info": "_START_-_END_ of _TOTAL_ entries",
					searchPlaceholder: "Search"
				},
				dom: 'Bfrtip',
				buttons: [
				'copy', 'csv', 'pdf', 'print'
				]
			});
			var table = $('.select-row').DataTable();
			$('.select-row tbody').on('click', 'tr', function () {
				if ($(this).hasClass('selected')) {
					$(this).removeClass('selected');
				}
				else {
					table.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}
			});
			var multipletable = $('.multiple-select-row').DataTable();
			$('.multiple-select-row tbody').on('click', 'tr', function () {
				$(this).toggleClass('selected');
			});
		});
	</script>
</body>
</html>
