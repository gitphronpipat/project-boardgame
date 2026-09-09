<!DOCTYPE html>
<html lang="th">

<head>
	<!--- font-ตัวหนังสือ --->
	<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<!--- font-awesome --->
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<!--- bootstrap-icons --->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<!--- bootstrap --->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<!-- DataTables + Bootstrap5 CSS เพื่อให้มันประสานกัน --> 
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<!-- jQuery เพื่อทำให้คำสั่ง js ง่านขึ้นและเพื่อให้สามารถใช้งาน datatabel ได้ -->
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<!-- DataTables + Bootstrap5 JS เพื่อให้มันประสานกัน --> 
	<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
	<!-- เรียกใช้ script table --> 
	<script src="<?= base_url('assets/js/tables.js') ?>"></script>

<style>
	body {
		font-family: 'Sarabun', sans-serif;
		color: #e0e0e0;
		background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
		min-height: 100vh;
		font-size: 16px;
		line-height: 1.7;
		letter-spacing: 0.3px;
	}
	h1, h2, h3, h4, h5, h6 {
		color: #fff;
		font-weight: 600;
	}
	h1 { font-size: 2rem;    font-weight: 700; }
	h2 { font-size: 1.75rem; font-weight: 600; }	
	h3 { font-size: 1.5rem;  font-weight: 600; }
	h4 { font-size: 1.25rem; font-weight: 500; }
	h5 { font-size: 1rem;    font-weight: 500; }
	h6 { font-size: 0.875rem;font-weight: 500; }

    #main {
      margin-left: 220px;
      padding: 1.8rem;
      min-height: calc(100vh - 60px);
    }
	
	@media (max-width: 768px) {
    #main {
        margin-left: 0;
        padding: 1rem;
    }
    body {
        font-size: 15px;
    }
	}

	/* Card ครอบหน้า */
	.page-card {
		background: rgba(255, 255, 255, 0.06);
		backdrop-filter: blur(14px);
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 16px;
		box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
		padding: 1.8rem 2rem;
		color: #e0e0e0;
	}

	/* Form Controls */
	label {
		color: rgba(255, 255, 255, 0.85);
		font-weight: 500;
		margin-bottom: 6px;
	}
	.form-control, .form-select {
		background-color: rgba(255, 255, 255, 0.06) !important;
		border: 1px solid rgba(255, 255, 255, 0.15) !important;
		color: #fff !important;
		border-radius: 10px;
		padding: 10px 14px;
	}
	.form-control:focus, .form-select:focus {
		background-color: rgba(255, 255, 255, 0.1) !important;
		border-color: #818cf8 !important;
		box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
		color: #fff !important;
	}
	.form-control::placeholder {
		color: rgba(255, 255, 255, 0.35);
	}
	.form-select option {
		background-color: #1a1a2e;
		color: #fff;
	}
	.input-group-text {
		background-color: rgba(255, 255, 255, 0.08) !important;
		border: 1px solid rgba(255, 255, 255, 0.15) !important;
		color: #a5b4fc;
	}

	/* Tables */
	.table {
		color: #e0e0e0 !important;
		border-color: rgba(255, 255, 255, 0.08) !important;
		margin-bottom: 0;
	}
	.table thead th {
		background-color: rgba(129, 140, 248, 0.12) !important;
		color: #c7d2fe !important;
		border-color: rgba(255, 255, 255, 0.1) !important;
		font-weight: 600;
		padding: 12px;
	}
	.table tbody td {
		background-color: transparent !important;
		color: #e0e0e0 !important;
		border-color: rgba(255, 255, 255, 0.06) !important;
		vertical-align: middle;
		padding: 12px;
	}
	.table-hover tbody tr:hover td {
		background-color: rgba(255, 255, 255, 0.05) !important;
	}
	.dataTables_wrapper {
		color: rgba(255, 255, 255, 0.7);
	}
	.dataTables_wrapper .dataTables_length,
	.dataTables_wrapper .dataTables_filter,
	.dataTables_wrapper .dataTables_info,
	.dataTables_wrapper .dataTables_paginate {
		color: rgba(255, 255, 255, 0.7) !important;
		margin-bottom: 10px;
	}
	.dataTables_wrapper .dataTables_filter input {
		background: rgba(255, 255, 255, 0.06);
		border: 1px solid rgba(255, 255, 255, 0.15);
		color: #fff;
		border-radius: 8px;
		padding: 4px 10px;
	}
	.dataTables_wrapper .dataTables_length select {
		background: #1a1a2e;
		border: 1px solid rgba(255, 255, 255, 0.15);
		color: #fff;
		border-radius: 8px;
	}

	/* Buttons */
	.btn-primary {
		background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
		border: none;
		color: #fff;
		border-radius: 8px;
		font-weight: 500;
		transition: all 0.2s ease;
	}
	.btn-primary:hover {
		background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
		color: #fff;
		transform: translateY(-1px);
		box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
	}
	.btn-warning {
		background-color: rgba(251, 191, 36, 0.18);
		border: 1px solid rgba(251, 191, 36, 0.4);
		color: #fbbf24;
		border-radius: 8px;	
		font-weight: 500;
		transition: all 0.2s;
	}
	.btn-warning:hover {
		background-color: rgba(251, 191, 36, 0.3);
		border-color: rgba(251, 191, 36, 0.6);
		color: #fef3c7;
	}
	.btn-danger {
		background-color: rgba(248, 113, 113, 0.15);
		border: 1px solid rgba(248, 113, 113, 0.35);
		color: #f87171;
		border-radius: 8px;
		font-weight: 500;
		transition: all 0.2s;
	}
	.btn-danger:hover {
		background-color: rgba(248, 113, 113, 0.25);
		border-color: rgba(248, 113, 113, 0.5);
		color: #fca5a5;
	}
	.btn-secondary {
		background-color: rgba(211, 26, 26, 0.1);
		border: 1px solid rgba(255, 255, 255, 0.2);
		color: #e0e0e0;
		border-radius: 8px;
	}
	.btn-secondary:hover {
		background-color: rgba(255, 255, 255, 0.18);
		color: #fff;
	}
</style>

   <title><?php echo $title; ?></title>
</head>

<body>
<?php $this->load->view('theme/navbar'); ?>
<?php $this->load->view('theme/menu'); ?>
<?php $this->load->view('theme/notify'); ?>

<div id="main">
    <?php $this->load->view($content); ?>
</div>

</body>
</html>
