<?php
    require_once('../config.php');
    require('../function/fpdf/html_table2.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF('P','mm',array(215,330));
    $pdf->AddPage();
    $pdf->SetMargins(5, 10, 15, true);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 16);
    $tgl = explode(" - ",$_GET['tgl']);
    
    $tgl1 = date('Y-m-d', strtotime($tgl[0]));
    $tgl2 = date('Y-m-d', strtotime($tgl[1]));
    $brg = $_GET['brg'];
    $pdf->Cell(0, 5, "Laporan Transaksi : ".$tgl[0].' sampai '.$tgl[1], 0, 1, 'C');
    
    //ISI
    $pdf->SetFont('Calibri', '', 12);
    $totDebet = $totKredit = 0; $selisih = 0;
    $pdf->Ln(5);
    $pdf->Cell(10,7,'No',1,0,'C',0);
    $pdf->Cell(75,7,'Nama Barang',1,0,'C',0);
    $pdf->Cell(22,7,'in',1,0,'C',0);
    $pdf->Cell(22,7,'out',1,0,'C',0);
    $pdf->Cell(22,7,'retur',1,0,'C',0);
    $pdf->Cell(22,7,'so',1,0,'C',0);
    $pdf->Cell(22,7,'Stok',1,1,'C',0);
    $filter = '';
    if ($brg != '?') {
        $filter = "BETWEEN '".$tgl1."' and '".$tgl2."' and kode='".$brg."'";
    }else{
        $filter = "BETWEEN '".$tgl1."' and '".$tgl2."'";
    }
    //database
    $q2 = "SELECT b.*,IFNULL(masuk, 0) as masuk, IFNULL(retur, 0) as retur, IFNULL(keluar, 0) as keluar, IFNULL(so, 0) as so,harga,tgl_po FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk,db.nobeli FROM aki_dbeli db left join aki_beli b on db.nobeli=b.nobeli where aktif=0 and tgl_beli ".$filter." group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar dk left join aki_bkeluar bk on dk.nobkeluar=bk.nobkeluar where aktif=0 and tgl_bkeluar ".$filter." group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur dr left join aki_bretur br on dr.nobretur=br.nobretur where aktif=0 and tgl_bretur ".$filter." group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso dso left join aki_bso so on dso.nobso=so.nobso where aktif=0 and tgl_bso ".$filter." group by dso.kode_barang ) as dso on b.kode=dso.kode_barang left join (SELECT a1.* FROM (SELECT dpo.*,tgl_po,RANK() OVER (PARTITION BY dpo.kode_barang ORDER BY tgl_po DESC) rank FROM `aki_dpo` dpo left join aki_po po on dpo.nopo=po.nopo) as a1 where a1.rank=1 and tgl_po ".$filter." group by a1.kode_barang) as a2 on b.kode=a2.kode_barang group by b.kode ORDER BY `kode` ASC";
    $rs2 = mysql_query($q2, $dbLink);
    $total=1;
    while ($query_data = mysql_fetch_array($rs2)) {
        if ($total % 2 == 0) {
            $pdf->SetFillColor(172, 203, 252);
        }else{
            $pdf->SetFillColor(252, 252, 250);
        }
        $stok = ($query_data["astok"]+$query_data["masuk"]-$query_data["keluar"]+$query_data["retur"]+($query_data["so"]));
        $pdf->Cell(10,5,$total,1,0,'C',1);
        $pdf->Cell(75,5,$query_data["nama"],1,0,'L',1);
        $pdf->Cell(22,5,$query_data["masuk"],1,0,'C',1);
        $pdf->Cell(22,5,$query_data["keluar"],1,0,'C',1);
        $pdf->Cell(22,5,$query_data["retur"],1,0,'C',1);
        $pdf->Cell(22,5,$query_data["so"],1,0,'C',1);
        $pdf->Cell(22,5,$stok,1,1,'C',1);
        $total++;
    }
    if ($total==0){
        $pdf->Cell(0, 15, "Tidak Ada Data", 0, 1, 'C');
    }
    //output file PDF
    $pdf->Output('TransaksiWH.pdf', 'I'); //download file pdf
?>