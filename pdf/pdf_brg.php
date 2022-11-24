<?php
    require_once('../config.php');
    require('../function/fpdf/html_table2.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF('P','mm',array(215,330));
    $pdf->AddPage();
    $pdf->SetMargins(7, 10, 15, true);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 16);
    $pdf->Cell(0, 5, "Laporan Stok Barang sampai tanggal: ".$_GET['tgl'], 0, 1, 'C');
    $tgl = date("Y-m-d", strtotime($_GET['tgl']));
    //ISI
    $pdf->SetFont('Calibri', '', 12);
    $totDebet = $totKredit = 0; $selisih = 0;
    $pdf->Ln(5);
    $pdf->Cell(10,6,'No',1,0,'C',0);
    $pdf->Cell(30,6,'Kode',1,0,'C',0);
    $pdf->Cell(85,6,'Nama',1,0,'C',0);
    $pdf->Cell(20,6,'Stok',1,0,'C',0);
    if ($_GET['chk'] == 2) {
        $pdf->Cell(20,6,'Satuan',1,0,'C',0);
        $pdf->Cell(35,6,'Harga',1,1,'C',0);
    }else if($_GET['chk'] == 3){
        $pdf->Cell(20,6,'min Stok',1,0,'C',0);
        $pdf->Cell(20,6,'Satuan',1,1,'C',0);
    }else{
        $pdf->Cell(20,6,'Satuan',1,0,'C',0);
        $pdf->Cell(35,6,'Harga',1,1,'C',0);
    }
    
    //database
    $q2 = "SELECT b.*,IFNULL(masuk, 0) as masuk, IFNULL(retur, 0) as retur, IFNULL(keluar, 0) as keluar, IFNULL(so, 0) as so,harga,tgl_po FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk,db.nobeli FROM aki_dbeli db left join aki_beli b on db.nobeli=b.nobeli where aktif=0 and tgl_beli<='".$tgl."' group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar dk left join aki_bkeluar bk on dk.nobkeluar=bk.nobkeluar where aktif=0 and tgl_bkeluar<='".$tgl."' group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur dr left join aki_bretur br on dr.nobretur=br.nobretur where aktif=0 and tgl_bretur<='".$tgl."' group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso dso left join aki_bso so on dso.nobso=so.nobso where aktif=0 and tgl_bso<='".$tgl."' group by dso.kode_barang ) as dso on b.kode=dso.kode_barang left join (SELECT a1.* FROM (SELECT dpo.*,tgl_po,RANK() OVER (PARTITION BY dpo.kode_barang ORDER BY tgl_po DESC) rank FROM `aki_dpo` dpo left join aki_po po on dpo.nopo=po.nopo) as a1 where a1.rank=1 and tgl_po<='".$tgl."' group by a1.kode_barang) as a2 on b.kode=a2.kode_barang group by b.kode ORDER BY `kode` ASC";
    $rs2 = mysql_query($q2, $dbLink);
    $total=0;
    while ($query_data = mysql_fetch_array($rs2)) {
        $stok = ($query_data["astok"]+$query_data["masuk"]-$query_data["keluar"]+$query_data["retur"]+($query_data["so"]));
        if ($_GET['chk'] == 3) {
            if ($stok <= $query_data["minstok"]) {
                $total++;
                $pdf->Cell(10,6,$total,1,0,'C',0);
                $pdf->Cell(30,6,$query_data["kode"],1,0,'C',0);
                $pdf->Cell(85,6,$query_data["nama"],1,0,'L',0);
                $pdf->Cell(20,6,$stok,1,0,'R',0);
                $pdf->Cell(20,6,$query_data["minstok"],1,0,'R',0);
                $pdf->Cell(20,6,$query_data["satuan"],1,1,'C',0);
            }
        }elseif ($_GET['chk'] == 1) {
            if ($stok <= 0) {
                $total++;
                $pdf->Cell(10,6,$total,1,0,'C',0);
                $pdf->Cell(30,6,$query_data["kode"],1,0,'C',0);
                $pdf->Cell(85,6,$query_data["nama"],1,0,'L',0);
                $pdf->Cell(20,6,$stok,1,0,'R',0);
                $pdf->Cell(20,6,$query_data["satuan"],1,0,'C',0);
                $pdf->Cell(35,6,$query_data["harga"],1,1,'R',0);
            }
        }elseif ($_GET['chk'] == 2){
            if ($stok > 0) {
                $total++;
                $pdf->Cell(10,6,$total,1,0,'C',0);
                $pdf->Cell(30,6,$query_data["kode"],1,0,'C',0);
                $pdf->Cell(85,6,$query_data["nama"],1,0,'L',0);
                $pdf->Cell(20,6,$stok,1,0,'R',0);
                $pdf->Cell(20,6,$query_data["satuan"],1,0,'C',0);
                $pdf->Cell(35,6,number_format($query_data["harga"]),1,1,'R',0);
            }
        }else{
            $total++;
            $pdf->Cell(10,6,$total,1,0,'C',0);
            $pdf->Cell(30,6,$query_data["kode"],1,0,'C',0);
            $pdf->Cell(85,6,$query_data["nama"],1,0,'L',0);
            $pdf->Cell(20,6,$stok,1,0,'R',0);
            $pdf->Cell(20,6,$query_data["satuan"],1,0,'C',0);
            $pdf->Cell(35,6,number_format($query_data["harga"]),1,1,'R',0);
        }
    }
    if ($total==0){
        $pdf->Cell(0, 15, "Tidak Ada Data", 0, 1, 'C');
    }
    //output file PDF
    $pdf->Output('TransaksiSo.pdf', 'I'); //download file pdf
?>