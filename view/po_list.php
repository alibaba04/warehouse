<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/po_list";
require_once( './config.php' );
global $dbLink;
//Periksa hak user pada modul/menu ini
$judulMenu = 'PO';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    
    
    require_once("./class/c_po.php");
    $tmppo = new c_po;

//Jika Mode Tambah/Add
    if (($_POST["txtMode"]) == "Addsupp") {
        $pesan = $tmpsupp->addsupp($_POST);
    }else if (($_POST["txtMode"]) == "Addbrg") {
        $pesan = $tmpbrg->addbrg($_POST);
    }

//Jika Mode Ubah/Edit
    if (($_POST["txtMode"]) == "Editsupp") {
        $pesan = $tmpsupp->edit($_POST);
    }else if(($_POST["txtMode"]) == "Editbrg") {
        $pesan = $tmpbrg->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmppo->delete($_GET["nopo"]);
    }else if($_GET["txtMode"] == "Accop") {
        $pesan = $tmppo->accop($_GET["nopo"],$_GET["kode"]);
    }else if($_GET["txtMode"] == "Cancelop") {
        $pesan = $tmppo->cancelop($_GET["nopo"],$_GET["kode"]);
    }else if($_GET["txtMode"] == "Accfa") {
        $pesan = $tmppo->accfa($_GET["nopo"],$_GET["kode"]);
    }else if($_GET["txtMode"] == "Cancelfa") {
        $pesan = $tmppo->cancelfa($_GET["nopo"],$_GET["kode"]);
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
    function checkkode() {
      var $kode = $("#nokodeb").val();
      $.post("function/ajax_function.php",{ fungsi: "checknkode",nkode:$kode},function(data)
        {
            alert('data');
        },"json");
    }
    $(document).ready(function () {
        $(".select2").select2();
        $("#example1").DataTable({
            responsive: true,
            "scrollX": true,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Purchase Order
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">PO</li>
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
                        <?php
                            if ($hakUser == 90) {
                        ?>
                        <span class="input-group-btn">
                          <?php
                          echo '<button type="button" id="btnpo" class="btn btn-primary"';
                          echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/po_detail&mode=add'>";
                          echo '<i class="fa fa-plus"> </i> PO</button>';
                          ?>
                        </span>
                        <?php
                            }
                        ?>
                    </div></center>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <section class="col-lg-2">
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
            <div class="box box-primary">
                <?php
            //database
                 $q = "SELECT dpo.*,brg.nama,s.supplier,p.tgl_po,p.tgl_beli,p.cust FROM `aki_dpo` dpo left join aki_po p on dpo.nopo=p.nopo left join aki_barang brg on dpo.kode_barang=brg.kode left join aki_supplier s on p.id_supplier=s.kodesupp WHERE p.aktif=0 order by dpo.id desc";
            //Paging
                 
                $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                ?>
                <div class="box-header">
                </div>
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
                            .tdalignr{
                                text-align: right;
                            }
                        </style>

                        <table id="example1" class="table table-bordered display nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>No PO</th>
                                    <th>Pemohon</th>
                                    <th>Supplier</th>
                                    <th>Jenis</th>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga (Rp)</th>
                                    <th>Tanggal PO</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Acc OP</th>
                                    <th>Acc FA</th>
                                    <?php 
                                    if ($hakUser == 90) {
                                        echo "<th>Action</th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td class='psymbol'><a class='btn btn-default btn-sm pull-right' href='pdf/pdf_po.php?&nopo=" . md5($query_data["nopo"])."' style='cursor:pointer;'>" . $query_data["nopo"] . "</a></td>";
                                    echo "<td>" . $query_data["cust"] . "</td>";
                                    echo "<td>" . $query_data["supplier"] . "</td>";
                                    echo "<td>" . $query_data["jbarang"] . "</td>";
                                    if ($query_data["jbarang"] == 'persediaan') {
                                        echo "<td>" . $query_data["nama"] . "</td>";
                                    }else{
                                        echo "<td>" . $query_data["kode_barang"] . "</td>";
                                    }
                                    echo "<td class='psymbol'>" . $query_data["qty"] . "</td>";
                                    echo "<td class='psymbol'>" . $query_data["satuan"] . "</td>";
                                    echo "<td class='tdalignr'>" . number_format($query_data["harga"]) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_po"])) . "</td>";
                                    echo "<td>" ; if ($query_data["tgl_beli"]!='0000-00-00 00:00:00') {
                                        echo date('d/m/Y', strtotime($query_data["tgl_eli"])) . "</td>";
                                    }
                                    
                                    if ($_SESSION["my"]->privilege=='koperational' || $_SESSION["my"]->privilege=='GODMODE' ) {
                                        echo "<td><center>";
                                        if ($query_data["acc_op"]==1) {
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Cancel Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Cancelop&nopo=" . md5($query_data["nopo"]) . "&kode=".md5($query_data["kode_barang"])."'}\" style='cursor:pointer;'><i class='fa fa-fw fa-check'></a>";
                                        }else{
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Approve Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Accop&nopo=" . md5($query_data["nopo"]) . "&kode=".md5($query_data["kode_barang"])."'}\" style='cursor:pointer;'></a>";
                                        }
                                        echo "</center></td>";
                                    }else{
                                        echo "<td><center>";
                                        if ($query_data["acc_op"]==1) {
                                            echo "<i class='fa fa-fw fa-check'>";
                                        }
                                        echo "</center></td>";
                                    }
                                    
                                    if ($_SESSION["my"]->privilege=='kfinance' || $_SESSION["my"]->privilege=='GODMODE' ) {
                                        echo "<td><center>";
                                        if ($query_data["acc_fa"]==1) {
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Cancel Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Cancelfa&nopo=" . md5($query_data["nopo"]) . "&kode=".md5($query_data["kode_barang"])."'}\" style='cursor:pointer;'><i class='fa fa-fw fa-check'></a>";
                                        }else{
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Approve Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Accfa&nopo=" . md5($query_data["nopo"]) . "&kode=".md5($query_data["kode_barang"])."'}\" style='cursor:pointer;'></a>";
                                        }
                                        echo "</center></td>";
                                    }else{
                                        echo "<td><center>";
                                        if ($query_data["acc_fa"]==1) {
                                            echo "<i class='fa fa-fw fa-check'>";
                                        }
                                        echo "</center></td>";
                                    }
                                    echo "<td>";
                                    if ($query_data["acc_fa"]!=1 or $query_data["acc_op"]!=1) {
                                        if ($hakUser == 90) {
                                            echo "<a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/po_detail&mode=edit&nopo=" . md5($query_data["nopo"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Apakah anda yakin akan menghapus data PO ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-trash'></i></a>";
                                        }
                                    }
                                    echo "</td></tr>";
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
        </section>
    </div>
</section>