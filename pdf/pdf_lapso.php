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
    $pdf->Cell(0, 5, "Laporan Transaksi Stok Opname : ".$tgl[0].' sampai '.$tgl[1], 0, 1, 'C');
    
    //ISI
    $pdf->SetFont('Calibri', '', 12);
    $totDebet = $totKredit = 0; $selisih = 0;
    $pdf->Ln(5);
    $pdf->Cell(40,6,'No Transaksi',1,0,'C',0);
    $pdf->Cell(25,6,'Tanggal',1,0,'C',0);
    $pdf->Cell(80,6,'Barang',1,0,'C',0);
    $pdf->Cell(20,6,'Qty',1,0,'C',0);
    $pdf->Cell(40,6,'ket',1,1,'C',0);
    //database
    $q2 = "SELECT b.kode,b.nama,ket,bs.nobso as no,qty,tgl_bso as tgl,'so' as ket FROM `aki_barang` b RIGHT join aki_dbso dbs on b.kode=dbs.kode_barang RIGHT join aki_bso bs on bs.nobso=dbs.nobso where tgl_bso BETWEEN '".$tgl1."' and '".$tgl2."'";
    $rs2 = mysql_query($q2, $dbLink);
    $total=0;
    while ($query_data = mysql_fetch_array($rs2)) {
        if ($query_data["ket"] == 'in') {
            $pdf->SetFillColor(172, 203, 252);
        }elseif($query_data["ket"] == 'out'){
            $pdf->SetFillColor(230, 147, 153);
        }elseif($query_data["ket"] == 're'){
            $pdf->SetFillColor(234, 247, 193);
        }else{
            $pdf->SetFillColor(252, 252, 250);
        }
        $pdf->Cell(40,6,$query_data["no"],1,0,'L',1);
        $pdf->Cell(25,6,$query_data["tgl"],1,0,'C',1);
        $pdf->Cell(80,6,$query_data["nama"],1,0,'L',1);
        if ($query_data["ket"] == 'out') {
            $pdf->Cell(20,6,'-'.$query_data["qty"],1,0,'R',1);
        }else{
            $pdf->Cell(20,6,$query_data["qty"],1,0,'R',1);
        }
        $pdf->Cell(40,6,$query_data["ket"],1,1,'C',1);

        $total++;
    }
    if ($total==0){
        $pdf->Cell(0, 15, "Tidak Ada Data", 0, 1, 'C');
    }
    //output file PDF
    $pdf->Output('TransaksiSo.pdf', 'I'); //download file pdf
?>