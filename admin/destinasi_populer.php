<?php include '../../koneksi.php'; ?>

<h3>Destinasi Populer (Homepage)</h3>
<a href="tambah.php">+ Tambah Destinasi</a>

<table border="1" cellpadding="10">
<tr>
  <th>Nama</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>

<?php
$q=mysqli_query($conn,"SELECT * FROM destinasi_populer");
while($d=mysqli_fetch_assoc($q)):
?>
<tr>
  <td><?= $d['nama'] ?></td>
  <td><?= $d['aktif']?'Aktif':'Nonaktif' ?></td>
  <td>
    <a href="edit.php?id=<?= $d['id'] ?>">Edit</a> |
    <a href="hapus.php?id=<?= $d['id'] ?>"
       onclick="return confirm('Hapus?')">Hapus</a>
  </td>
</tr>
<?php endwhile; ?>
</table>
