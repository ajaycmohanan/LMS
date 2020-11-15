<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    $userid = $_SESSION['userid'];
    $sql = 'SELECT * FROM members WHERE mid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];
    $adminid = $row['adminid'];
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
								<h4>Books</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Books</li>
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
									<th>Book Id</th>
									<th>Book Name</th>
									<th>Author(s)</th>
									<th>Language</th>
									<th>Publisher</th>
									<th>Isbn</th>
									<th>Classification</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql = 'SELECT * FROM books WHERE adminid = :userid AND status = :status';
									$stmt = $pdo -> prepare($sql);
									$stmt -> execute(['userid' => $adminid, 'status' => 1]);
									while ( $rows = $stmt -> fetch(PDO::FETCH_ASSOC)) {
										echo '
										    <tr>
										        <td>'.$rows['bookid'].'</td>
										        <td>'.$rows['bookname'].'</td>
										        <td>'.$rows['author'].'</td>
										        <td>'.$rows['lang'].'</td>
										        <td>'.$rows['publisher'].'</td>
										        <td>'.$rows['isbn'].'</td>
										        <td>'.$rows['classification'].'</td>
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
