<?php
defined( 'validSession' ) or die( 'Restricted access' ); 
?>
<section class="content-header">
  <h1>
    Dashboard
    <small></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>
<br>
<div class="box-body">
  <input type="hidden" name="kodeuser" id="kodeuser" value="<?php echo $_SESSION["my"]->id ?>">
  <div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-info"></i> Alert!</h4>
    Welcome to PT AKI &#x1F609;
  </div>
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-4 col-6">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <?php 
              $q = "SELECT sum(dpo.subtotal) as total,year(po.tgl_po) as year FROM `aki_dpo` dpo left join aki_po po on dpo.nopo=po.nopo where dpo.acc_op='1' and dpo.acc_fa='1'";
              $rsTemp = mysql_query($q, $dbLink);
              if ($data = mysql_fetch_array($rsTemp)) {
                echo "<h3>Rp. ".number_format($data['total'])."</h3>";
                echo "<p>Total Pengeluaran ".$data['year']."</p>";
              }
              ?>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="./plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="./plugins/chartjs/Chart.min.js"></script>
