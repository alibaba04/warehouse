<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/gudang_list";
require_once( './config.php' );
global $dbLink;
//Periksa hak user pada modul/menu ini
$judulMenu = 'Oder';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_gudang.php");
    $tmpgudang = new c_gudang;

//Jika Mode Ubah/Edit
    if (isset($_POST["txtMode"]) == "Edit") {
        $pesan = $tmpgudang->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        if ($_GET["ket"] == "retur") {
            $pesan = $tmpgudang->delretur($_GET["no"]);
        }elseif ($_GET["ket"] == "out") {
            $pesan = $tmpgudang->delout($_GET["no"]);
        }else{
            $pesan = $tmpgudang->delso($_GET["no"]);
        }
    }

//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="dist/js/jquery-ui.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" charset="utf-8">
    $(function () {
        $("#example3").DataTable({
          "autoWidth": false,
          "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
        $(".select2").select2();
        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } 
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
        $("#example4").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
        $("#example2").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        $("#example1").DataTable({
            responsive: true,
            "autoWidth": false,
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        
    });
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Gudang
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Gudang</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-sm-4">
            <!-- TO DO List -->
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body"><center>
                    <div class="input-group input-group-sm">
                        <span class="input-group-btn">
                            <button type="button" id="btnpengajuan" class="btn btn-primary btn-flat" 
                            <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/pengajuan_detail&mode=add'";
                            ?>
                            ><i class="fa fa-minus"> </i> Keluar</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnso" class="btn btn-primary btn-flat"  <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/so_detail&mode=add'";
                            ?>
                            ><i class="fa fa-plus"> </i> SO</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnpengajuan" class="btn btn-primary btn-flat"  <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/retur_detail&mode=add'";
                            ?>
                            ><i class="fa fa-plus"> </i> Retur</button>
                        </span>
                    </div></center>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <section class="col-lg-6">
            <?php
            //informasi hasil input/update Sukses atau Gagal
            if (isset($_GET["pesan"]) != "") {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-warning"></i>
                        <h3 class="box-title">Pesan</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if (substr($_GET["pesan"],0,5) == "Gagal") { 
                            echo '<div class="callout callout-danger">';
                        }else{
                            echo '<div class="callout callout-success">';
                        }
                        if ($_GET["pesan"] != "") {

                            echo $_GET["pesan"];
                        }
                        echo '</div>';
                        ?>
                    </div>
                </div>
            <?php } ?>
        </section>
        <section class="col-lg-12 connectedSortable">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#pengajuan" data-toggle="tab">Keluar</a></li>
                    <!-- <li><a href="#retur" data-toggle="tab">Retur</a></li>
                    <li><a href="#so" data-toggle="tab">SO</a></li> -->
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="pengajuan">
                        <div class="box box-primary">
                            <?php
                            $q = "SELECT bk.nobkeluar as no,cust,tgl_bkeluar as tgl,kodeproyek,brg.nama,dbk.qty,dbk.satuan,'ke' as ket FROM `aki_bkeluar` bk left join aki_dbkeluar dbk on bk.nobkeluar=dbk.nobkeluar left join aki_barang brg on dbk.kode_barang=brg.kode WHERE aktif=0 group by bk.nobkeluar union all SELECT bk.nobretur as no,cust,tgl_bretur as tgl,kodeproyek,brg.nama,dbk.qty,dbk.satuan,'re' as ket FROM `aki_bretur` bk left join aki_dbretur dbk on bk.nobretur=dbk.nobretur left join aki_barang brg on dbk.kode_barang=brg.kode WHERE aktif=0 group by bk.nobretur union all SELECT bk.nobso as no,'-' as cust,tgl_bso as tgl,'-' as kodeproyek,brg.nama,dbk.qty,dbk.satuan,'so' as ket FROM `aki_bso` bk left join aki_dbso dbk on bk.nobso=dbk.nobso left join aki_barang brg on dbk.kode_barang=brg.kode WHERE aktif=0 group by bk.nobso";
                            $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                            ?>
                            <div class="box-body">
                                <div class="wrappert">
                                    <style type="text/css">
                                        .psymbol{
                                            text-align: center;
                                        }
                                        .pdone{
                                            background-color: #ff0000;
                                        }
                                        .pprogress{
                                            background-color: #a6a6a6;
                                        }
                                    </style>
                                    <table id="example1" class="table table-bordered display nowrap" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Transaksi</th>
                                                <th>Pemohon</th>
                                                <th>Kode/Proyek</th>
                                                <th>Nama</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                                <th>Tanggal</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rowCounter=1;
                                            while ($query_data = $rs->fetchArray()) {
                                                echo "<tr>";
                                                echo "<td>" . $rowCounter . "</td>";
                                                echo '<td onclick=editbrg("'.$query_data["no"].'")>'. $query_data["no"] .'</td>';
                                                $pmhon = '';
                                                $kdproyek = '';
                                                if ($query_data["ket"] == 'ke') {
                                                    $pmhon = $query_data["cust"];
                                                    $kdproyek = $query_data["kodeproyek"];
                                                }else if ($query_data["ket"] == 're') {
                                                    $pmhon = '-Retur-';
                                                    $kdproyek = '-Retur-';
                                                }else{
                                                    $pmhon = '-SO-';
                                                    $kdproyek = '-SO-';
                                                }
                                                echo "<td>".$pmhon."</td>";
                                                
                                                echo "<td>".$kdproyek."</td>";
                                                echo "<td>".$query_data["nama"]."</td>";
                                                echo "<td>".$query_data["qty"]."</td>";
                                                echo "<td>".$query_data["satuan"]."</td>";
                                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl"])) . "</td>";
                                                $linkedit='';$lket='';
                                                if ($query_data["ket"] == 're') {
                                                    $linkedit = "retur_detail&mode=edit&nobretur=" . md5($query_data["no"]);
                                                    $lket='retur';
                                                }else if($query_data["ket"] == 'ke'){
                                                    $linkedit = "pengajuan_detail&mode=edit&nobkeluar=" . md5($query_data["no"]);
                                                    $lket='out';
                                                }else{
                                                    $linkedit = "so_detail&mode=edit&nobso=" . md5($query_data["no"]);
                                                    $lket='so';
                                                }
                                                echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/" . $linkedit."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Order ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&ket=" . $lket . "&no=" . md5($query_data["no"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-trash'></i></a>";
                                                echo "</tr>";
                                                $rowCounter++;
                                            }
                                            if (!$rs->getNumPages()) {
                                                echo("<tr class='even'>");
                                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                                echo("</tr>");
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
