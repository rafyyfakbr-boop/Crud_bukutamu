  <?php
require_once('function.php');
include_once('templates/header.php');
?>
  <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Data User</h1>
                    <?php
                    // jika ada tombol simpan
                    if (isset($_POST['simpan'])) {
                        if (tambah_user($_POST) > 0) {
                    ?>
                            <div class="alert alert-success" role="alert">
                                Data berhasil disimpan!
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                Data gagal disimpan!
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <button type="button" class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#tambahModal">
                                <span class="icon text-white-50">
                                    <i class="fas fa-plus"></i>
                            </span>
                            <span class="text">Data User</span>
                        </button>
                    </div>
                    <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                <tr>
                                    <th>No</th>                         
                                    <th>Username</th>
                                    <th>User Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // penomoran auto-increment
                                $no = 1;
                                // Query untuk memanggil semua data dari tabel users
                                $users = query("SELECT * FROM users");
                                foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $user['username'] ?></td>
                                    <td><?= $user['user_role'] ?></td>
                                    <td>
                                        <a class="btn btn-success" href="edit-user.php?id=<?= $user['id_user'] ?>">Ubah</a>
                                        <a onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')" class="btn btn-danger" href="hapus-user.php?id=<?= $user['id_user'] ?>">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
                 <?php
    // mengambil data barang dari tabel dengan kode terbesar
    $query = mysqli_query($koneksi, "SELECT max(id_user) as kodeTerbesar FROM users");
    $data = mysqli_fetch_array($query);
    $kodeUser = $data['kodeTerbesar'];
    // mengambil angka dari kode barang terbesar
    $urutan = (int) substr($kodeUser, 2, 3);
    // nomor yang diambil akan ditambah 1 untuk menentukan nomor urut berikutnya
    $urutan++;
    // membuat kode barang baru
    $huruf = "usr";
    $kodeUser = $huruf . sprintf("%02s", $urutan);
?>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahModalLabel">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <div class="modal-body">
    <form method="post" action="">
        <input type="hidden" name="id_user" id="id_user" value="<?= $kodeUser ?>">
        <div class="form-group row">
            <label for="username" class="col-sm-3 col-form-label">Username</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="username" name="username">
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-sm-3 col-form-label">Password</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="password" name="password">
            </div>
        </div>
        <div class="form-group row">
            <label for="user_role" class="col-sm-3 col-form-label">User Role</label>
            <div class="col-sm-8">
                <select class="form-control" id="user_role" name="user_role">
                    <option value="admin">Administrator</option>
                    <option value="operator">Operator</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
        </div>
    </div>
</div>
                 
<?php
include_once('templates/footer.php');
?>