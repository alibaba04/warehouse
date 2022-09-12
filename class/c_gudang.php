<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_gudang
{
	var $strResults="";
	
	function delretur($notransaksi)
	{
		global $dbLink;

		$notransaksi  = secureParam($notransaksi,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`npo`=".$notransaksi." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal hapus data notransaksi. ');

			$q = "UPDATE `aki_bretur` SET `aktif`='1' WHERE md5(nobretur)='".$notransaksi."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data notransaksi.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	function delout($notransaksi)
	{
		global $dbLink;

		$notransaksi  = secureParam($notransaksi,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`npo`=".$notransaksi." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal hapus data notransaksi. ');

			$q = "UPDATE `aki_bkeluar` SET `aktif`='1' WHERE md5(nobkeluar)='".$notransaksi."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data notransaksi.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	function delso($notransaksi)
	{
		global $dbLink;

		$notransaksi  = secureParam($notransaksi,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`npo`=".$notransaksi." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal hapus data notransaksi. ');

			$q = "UPDATE `aki_bso` SET `aktif`='1' WHERE md5(nobso)='".$notransaksi."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data notransaksi.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
