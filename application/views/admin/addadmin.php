<div class="container-fluid">
    <h3 class="mb-4">เพิ่มแอดมิน</h3>
    <div class="page-card">
        <form action="<?= base_url('Auth/register') ?>" method="post">
            <div class="mb-3">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>บทบาท (Role)</label>
                <select name="role" class="form-select">
                    <option value="admin">Admin</option>
                    <option value="player">ผู้เล่น (Player)</option>
                </select>
            </div>
            <div class="mb-3">
                <label>รหัสผ่าน</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="<?= base_url('') ?>" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
