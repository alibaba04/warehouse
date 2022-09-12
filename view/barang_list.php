<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/barang_list";
error_reporting( error_reporting() & ~E_NOTICE );
error_reporting(E_ERROR | E_PARSE);
//Periksa hak user pada modul/menu ini
$judulMenu = 'Report';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
    require_once("./class/c_barang.php");
    $tmpbrg = new c_brg;
    if (($_POST["txtMode"]) == "Addbrg") {
        $pesan = $tmpbrg->addbrg($_POST);
    }
    if(($_POST["txtMode"]) == "Editbrg") {
        $pesan = $tmpbrg->edit($_POST);
    }
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $("#mylBarang").modal({backdrop: 'static'});
        $("#btnlbrg").click(function(){ 
            $("#mylBarang").modal({backdrop: 'static'});
        });
        $("#btnbrg").click(function(){ 
            document.getElementById("nokodeb").readOnly = false; 
            $("#txtMode").val('Addbrg');
            $("#myBarang").modal({backdrop: 'static'});
        });
        $('#btnClose').click(function(){
            location.href='index.php';
        });
        $("#example2").DataTable({
          "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
    function editbrg($kode){
        $.post("function/ajax_function.php",{ fungsi: "editbrg",kode:$kode},function(data)
        {
            document.getElementById("nokodeb").setAttribute("readonly", ""); 
            $("#nokodeb").val(data.kode);
            $("#txtnamab").val(data.nama);
            $("#txtsatuan").val(data.satuan);
            $("#txtbjenis").val(data.jenis);
            $("#txtlok").val(data.lokasi);
            $("#txtrack").val(data.rack);
            $("#txtMode").val('Editbrg');
        },"json");
        document.getElementById("txtastok").disabled = true;
        $("#myBarang").modal({backdrop: 'static'});
    }
</script>
<form action="index2.php?page=view/barang_list" method="post" name="frmpo" onSubmit="return validasiForm(this);">
    <input type='hidden' name='txtMode' id='txtMode' value=''>
    <div class="modal fade" id="mylBarang" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">List Barang <label id="labelclr"></label></h4>
                </div>
                <div class="modal-body">
                  <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"width="100%" >
                    <thead>
                      <tr>
                        <th>id</th>
                        <th>Kode</th>
                        <th width="100px;">Nama</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Rak</th>
                        <th>TglBeli</th>
                        <th>HargaBeli</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $q = "SELECT b.*,masuk, retur,keluar,so,harga,tgl_po FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk,db.nobeli FROM aki_dbeli db left join aki_beli b on db.nobeli=b.nobeli where aktif=0 group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar dk left join aki_bkeluar bk on dk.nobkeluar=bk.nobkeluar where aktif=0 group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur dr left join aki_bretur br on dr.nobretur=br.nobretur group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso dso left join aki_bso so on dso.nobso=so.nobso group by dso.kode_barang ) as dso on b.kode=dso.kode_barang left join (SELECT a1.* FROM (SELECT dpo.*,tgl_po,RANK() OVER (PARTITION BY dpo.kode_barang ORDER BY tgl_po DESC) rank FROM `aki_dpo` dpo left join aki_po po on dpo.nopo=po.nopo) as a1 where a1.rank=1 group by a1.kode_barang) as a2 on b.kode=a2.kode_barang group by b.kode ORDER BY `harga` DESC";
                      $rs = new MySQLPagedResultSet($q,500, $dbLink);
                      $rowCounter=1;
                      while ($query_data = $rs->fetchArray()) {
                        echo "<tr>";
                        echo "<td>" . $query_data["id"] ."</td>";
                        echo '<td onclick=editbrg("'.$query_data["kode"].'") style="cursor:pointer;">'. $query_data["kode"] .'</td>';
                        echo "<td>" . $query_data["nama"] ."</td>";
                        echo "<td>" . strtoupper($query_data["astok"]+$query_data["masuk"]-$query_data["keluar"]+$query_data["retur"]+($query_data["so"])) . "</td>";
                        echo "<td>" . $query_data["satuan"] ."</td>";
                        echo "<td>" . $query_data["jenis"] ."</td>";
                        echo "<td>" . $query_data["lokasi"] ."</td>";
                        echo "<td>" . $query_data["rack"] ."</td>";
                        if ($query_data["tgl_po"] == null) {
                            echo "<td></td>";
                        }else{
                            echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_po"])) ."</td>";
                        }
                        echo "<td>Rp " . number_format($query_data["harga"]) ."</td>";
                        echo("</tr>");
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
                <div class="modal-footer">
                  <button type="button" id="btnbrg" class="btn btn-primary"><i class="fa fa-plus"> </i> Barang</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myBarang" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Barang <label id="labelclr"></label></h4>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                      <div class="form-group row">
                        <label for="nokodeb" class="col-sm-2 col-form-label">Kode Barang</label>
                        <div class="col-sm-6">
                          <input type="text" class="form-control" id="nokodeb" name="nokodeb" >
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtnamab" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="txtnamab" name="txtnamab">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtsatuan" class="col-sm-2 col-form-label">Satuan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtsatuan" name="txtsatuan">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtbjenis" class="col-sm-2 col-form-label">Jenis</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="txtbjenis" name="txtbjenis">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtlok" class="col-sm-2 col-form-label">Lokasi</label>
                        <div class="col-sm-10">
                            <input type="phone" class="form-control" id="txtlok" name="txtlok">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtrack" class="col-sm-2 col-form-label">No Rak</label>
                        <div class="col-sm-10">
                            <input type="phone" class="form-control" id="txtrack" name="txtrack">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="txtastok" class="col-sm-2 col-form-label">Stok Awal</label>
                        <div class="col-sm-10">
                          <input type="number"  class="form-control" id="txtastok" name="txtastok">
                        </div>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Add"  id="btnaddbarang">
                </div>
            </div>
        </div>
    </div>
</form>