<?php
	require_once 'db_config.php'; 
	
	$func = $_REQUEST["func"];
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ($func == 'add')
	{
	//新增資料
		try
		{
			//新增資料至資料庫，ID自動取資料庫最大的值+1
			$stmt = $conn->prepare(
					//" SET @maxGUID = (SELECT IFNULL(Max(GUID), 0)+1 FROM PkgOwner);".
					" INSERT INTO momo_DriverData (GUID, Name, ID, Phone1, Phone2, Address) ".
					" VALUES (:GUID, :Name, :ID, :Phone1, :Phone2, :Address);"
					);
			$stmt->bindParam(':GUID', $_POST['GUID']);
			$stmt->bindParam(':Name', $_POST['Name']);
			$stmt->bindParam(':ID', $_POST['ID']);
			$stmt->bindParam(':Phone1', $_POST['Phone1']);
			$stmt->bindParam(':Phone2', $_POST['Phone2']);
			$stmt->bindParam(':Address', $_POST['Address']);
			
			$result = $stmt->execute();
			//$conn->exec("INSERT INTO `PkgOwner` (`GUID`, `Name`, `Phone`, `CellPhone`, `Country`, `Address`, `CompanyID`, `Notes`) VALUES ('2','1','1','1','1','1','1','1')");
			$stmt->closeCursor();
			echo 0;
			//print("新增成功！");
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'modify')
	{
		$oper = $_POST["oper"];
		if ($oper=='edit')
		{
			try
			{
				//jqgrid 編輯時
				$stmt = $conn->prepare(
						" UPDATE momo_DriverData SET Name=:Name, ID=:ID, Phone1=:Phone1, Phone2=:Phone2, Address=:Address WHERE GUID=:GUID "
						
						/*" UPDATE DeliveryData SET Date=:Date, TradeType=:TradeType,CustomerID=:CustomerID,PkgOwner1=:PkgOwner1,PkgOwner2=:PkgOwner2,Terminal=:Terminal, " .
						" PkgCount=:PkgCount,Unit=:Unit,Weight=:Weight,Volume='0',Note='0',ShipName='0',SO='0',CloseDate='0' WHERE GUID='2' "*/
						);
				$stmt->bindParam(':Name', $_POST['Name']);
				$stmt->bindParam(':ID', $_POST['ID']);
				$stmt->bindParam(':Phone1', $_POST['Phone1']);
				$stmt->bindParam(':Phone2', $_POST['Phone2']);
				$stmt->bindParam(':Address', $_POST['Address']);
				$stmt->bindParam(':GUID', $_POST['GUID']);
				
				$result = $stmt->execute();
				//$stmt->closeCursor();
			
			}
			catch (PDOException $error) 
			{
				echo 'connect failed:'.$error->getMessage();
			}
		}
		else if ($oper == 'del')
		{
			//jqgrid 刪除時
			try
			{
				$stmt = $conn->prepare(
						" DELETE FROM momo_DriverData WHERE GUID=:GUID "
						);
				$stmt->bindParam(':GUID', $_POST['id']);
				
				$result = $stmt->execute();
			}
			catch (PDOException $error) 
			{
				echo 'connect failed:'.$error->getMessage();
			}
		}
	}
	else if ($func == 'query')
	{
		//查詢資料
		try
		{
			$stmt = $conn->prepare(
					" SELECT GUID, Name, ID, Phone1, Phone2, Address FROM momo_DriverData " .
					" WHERE Name like '%'+ :Name +'%' "
					);
			$stmt->bindParam(':Name', $_POST['keyword']);
			
			$result = $stmt->execute();
			$data = array();
			
			if (!empty($result))
			{
				$rsp = $stmt->fetchAll();
				echo json_encode($rsp);
			}
			$stmt->closeCursor();
			//echo $date;
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	
	
?>
