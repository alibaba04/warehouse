<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/supp_list";
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
  require_once("./class/c_supp.php");
    $tmpsupp = new c_supp;
    if (($_POST["txtMode"]) == "Addsupp") {
        $pesan = $tmpsupp->addsupp($_POST);
    }
    if (($_POST["txtMode"]) == "Editsupp") {
        $pesan = $tmpsupp->edit($_POST);
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
        $("#mylSupp").modal({backdrop: 'static'});
        $("#btnlsupp").click(function(){ 
            $("#mylSupp").modal({backdrop: 'static'});
        });
        $('#btnClose').click(function(){
            location.href='index.php';
        });
        $("#example3").DataTable({
           responsive: true,
           "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#btnsupp").click(function(){ 
            $.post("function/ajax_function.php",{ fungsi: "getkodesupp"},function(data)
            {
                document.getElementById("txtksupp").value = 'supp'+data;
            },"json");
            $("#txtMode").val('Addsupp');
            $("#mySupp").modal({backdrop: 'static'});
        });
    });
    function editsupp($kode){
        $.post("function/ajax_function.php",{ fungsi: "editsupp",kode:$kode},function(data)
        {
            $("#txtksupp").val(data.kodesupp);
            $("#txtsupp").val(data.supplier);
            $("#txtalamat").val(data.alamat);
            $("#txtsjenis").val(data.jenis);
            $("#txtnohp").val(data.nomor);
            $("#txtnameb").val(data.kontak);
            $("#txtnorek").val(data.norek);
            $("#txtMode").val('Editsupp');
            $("#cbobank option:first").remove();
            var x = document.getElementById("cbobank");
            $('#cbobank').prepend($('<option val="'+data.nrek+'"selected>'+data.nrek+'</option>')); 
        },"json");
        $("#mySupp").modal({backdrop: 'static'});
    }
</script>
<form action="index2.php?page=view/supp_list" method="post" name="frmpo" onSubmit="return validasiForm(this);">
  <input type='hidden' name='txtMode' id='txtMode' value=''>
  <div class="modal fade" id="mylSupp" role="dialog">
      <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">List Supplier <label id="labelclr"></label></h4>
              </div>
              <div class="modal-body">
                <table id="example3" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                  <thead>
                    <tr>
                      <th>id</th>
                      <th>Kode</th>
                      <th>Supplier</th>
                      <th>Jenis</th>
                      <th>Alamat</th>
                      <th>NoHP</th>
                      <th>A.n</th>
                      <th>NoRekening</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $q = "SELECT * FROM `aki_supplier` ";
                    $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                    $rowCounter=1;
                    while ($query_data = $rs->fetchArray()) {
                      echo "<tr>";
                      echo "<td>" . $query_data["id"] ."</td>";
                      echo '<td onclick=editsupp("'.$query_data["kodesupp"].'") style="cursor:pointer;">'. $query_data["kodesupp"] .'</td>';
                      echo "<td>" . $query_data["supplier"] ."</td>";
                      echo "<td>" . $query_data["jenis"] . "</td>";
                      echo "<td>" . $query_data["alamat"] . "</td>";
                      echo "<td>" . $query_data["nomor"] ."</td>";
                      echo "<td>" . $query_data["nrek"] ."</td>";
                      echo "<td>" . $query_data["norek"] ."</td>";
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
                <button type="button" id="btnsupp" class="btn btn-primary"><i class="fa fa-plus"> </i> Supplier</button>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade" id="mySupp" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Supplier <label id="labelclr"></label></h4>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group row">
              <label for="nopo" class="col-sm-2 col-form-label">Kode Supplier</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="txtksupp" name="txtksupp" readonly >
              </div>
            </div>
            <div class="form-group row">
              <label for="txtsupp" class="col-sm-2 col-form-label">Supplier</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="txtsupp" name="txtsupp">
              </div>
            </div>
            <div class="form-group row">
              <label for="txtalamat" class="col-sm-2 col-form-label">Alamat</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="txtalamat" name="txtalamat" ></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="txtsjenis" class="col-sm-2 col-form-label">Jenis</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="txtsjenis" name="txtsjenis" ></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="txtnohp" class="col-sm-2 col-form-label">No HP</label>
              <div class="col-sm-10">
                <input type="phone" class="form-control" id="txtnohp" name="txtnohp" >
              </div>
            </div>
            <div class="form-group row">
              <label for="txtbank" class="col-sm-2 col-form-label">Bank</label>
              <div class="col-sm-10">
                <select class="form-control select2" id="cbobank" name="cbobank" style="width: 100%;">
                  <option value="">--Nama Bank--</option>
                  <option value="Bank Mandiri">Bank Mandiri</option>
                  <option value="Bank Bukopin">Bank Bukopin</option>
                  <option value="Bank Danamon">Bank Danamon</option>
                  <option value="Bank Mega">Bank Mega</option>
                  <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
                  <option value="Bank Permata">Bank Permata</option>
                  <option value="Bank Sinarmas">Bank Sinarmas</option>
                  <option value="Bank QNB">Bank QNB</option>
                  <option value="Bank Lippo">Bank Lippo</option>
                  <option value="Bank UOB">Bank UOB</option>
                  <option value="Panin Bank">Panin Bank</option>
                  <option value="Citibank">Citibank</option>
                  <option value="Bank ANZ">Bank ANZ</option>
                  <option value="Bank Commonwealth">Bank Commonwealth</option>
                  <option value="Bank Maybank">Bank Maybank</option>
                  <option value="Bank Maspion">Bank Maspion</option>
                  <option value="Bank J Trust">Bank J Trust</option>
                  <option value="Bank QNB">Bank QNB</option>
                  <option value="Bank KEB Hana">Bank KEB Hana</option>
                  <option value="Bank Artha Graha">Bank Artha Graha</option>
                  <option value="Bank OCBC NISP">Bank OCBC NISP</option>
                  <option value="Bank MNC">Bank MNC</option>
                  <option value="Bank DBS">Bank DBS</option>
                  <option value="BCA">BCA</option>
                  <option value="BNI">BNI</option>
                  <option value="BRI">BRI</option>
                  <option value="BTN">BTN</option>
                  <option value="Bank DKI">Bank DKI</option>
                  <option value="Bank BJB">Bank BJB</option>
                  <option value="Bank BPD DIY">Bank BPD DIY</option>
                  <option value="Bank Jateng">Bank Jateng</option>
                  <option value="Bank Jatim">Bank Jatim</option>
                  <option value="Bank BPD Bali">Bank BPD Bali</option>
                  <option value="Bank Sumut">Bank Sumut</option>
                  <option value="Bank Nagari">Bank Nagari</option>
                  <option value="Bank Riau Kepri">Bank Riau Kepri</option>
                  <option value="Bank Sumsel Babel">Bank Sumsel Babel</option>
                  <option value="Bank Lampung">Bank Lampung</option>
                  <option value="Bank Jambi">Bank Jambi</option>
                  <option value="Bank Kalbar">Bank Kalbar</option>
                  <option value="Bank Kalteng">Bank Kalteng</option>
                  <option value="Bank Kalsel">Bank Kalsel</option>
                  <option value="Bank Kaltim">Bank Kaltim</option>
                  <option value="Bank Sulsel">Bank Sulsel</option>
                  <option value="Bank Sultra">Bank Sultra</option>
                  <option value="Bank BPD Sulteng">Bank BPD Sulteng</option>
                  <option value="Bank Sulut">Bank Sulut</option>
                  <option value="Bank NTB">Bank NTB</option>
                  <option value="Bank NTT">Bank NTT</option>
                  <option value="Bank Maluku">Bank Maluku</option>
                  <option value="Bank Papua">Bank Papua</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="txtnohp" class="col-sm-2 col-form-label">A.n</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="txtnameb" name="txtnameb" >
              </div>
            </div>
            <div class="form-group row">
              <label for="txtnorek" class="col-sm-2 col-form-label">No Rekening</label>
              <div class="col-sm-10">
                <input type="number" class="form-control" id="txtnorek" name="txtnorek">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Add"  id="btnaddSupp" name="btnaddSupp">
        </div>
      </div>
    </div>
  </div>
</form>