<?php
$result  = $this->session->flashdata('result');
$message = $this->session->flashdata('message');


$titles = [
    'true'      => 'สำเร็จ',
    'false'     => 'ไม่สำเร็จ',
    'duplicate' => 'ชื่อผู้ใช้ซ้ำ',
    'userhave'  => 'ชื่อผู้ใช้ซ้ำ',
];

$icons = [
    'true'      => 'success',
    'false'     => 'error',
    'duplicate' => 'warning',
    'userhave'  => 'warning',
];
?>ฟ

<!-- iziToast -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<?php if ($result && isset($icons[$result])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        iziToast.<?= $icons[$result] ?>({
            title: '<?= $titles[$result] ?>',
            message: '<?= addslashes($message) ?>',
            position: 'topRight',
            timeout: 3000,
        });
    }); 
</script>
<?php endif; ?>
