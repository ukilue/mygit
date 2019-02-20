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
					" INSERT INTO momo_PkgOwner (Name, Phone, CellPhone, Country, Address, CompanyID, CustomerID, Notes) ".
					" VALUES (:Name,:Phone,:CellPhone,:Country,:Address,:CompanyID,:CustomerID,:Notes);"
					);
			$stmt->bindParam(':Name', $_POST['Name']);
			$stmt->bindParam(':Phone', $_POST['Phone']);
			$stmt->bindParam(':CellPhone', $_POST['CellPhone']);
			$stmt->bindParam(':Country', $_POST['Country']);
			$stmt->bindParam(':Address', $_POST['Address']);
			$stmt->bindParam(':CompanyID', $_POST['CompanyID']);
			//$Customer = $_POST['CustomerID'];
			//$CustomerID = substr($Customer, 0, strpos($Customer,"_"));
			$stmt->bindParam(':CustomerID', $_POST['CustomerID']);
			$stmt->bindParam(':Notes', $_POST['Notes']);
			
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
						" UPDATE momo_PkgOwner SET Phone=:Phone,CellPhone=:CellPhone,Country=:Country,Address=:Address,CompanyID=:CompanyID,CustomerID=:CustomerID ".
						" ,Notes=:Notes WHERE Name=:Name "
						
						/*" UPDATE DeliveryData SET Date=:Date, TradeType=:TradeType,CustomerID=:CustomerID,PkgOwner1=:PkgOwner1,PkgOwner2=:PkgOwner2,Terminal=:Terminal, " .
						" PkgCount=:PkgCount,Unit=:Unit,Weight=:Weight,Volume='0',Note='0',ShipName='0',SO='0',CloseDate='0' WHERE GUID='2' "*/
						);
				$stmt->bindParam(':Name', $_POST['Name']);
				$stmt->bindParam(':Phone', $_POST['Phone']);
				$stmt->bindParam(':CellPhone', $_POST['CellPhone']);
				$stmt->bindParam(':Country', $_POST['Country']);
				$stmt->bindParam(':Address', $_POST['Address']);
				$stmt->bindParam(':CompanyID', $_POST['CompanyID']);
				$stmt->bindParam(':CustomerID', $_POST['CustomerID']);
				$stmt->bindParam(':Notes', $_POST['Notes']);
				
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
						" DELETE FROM momo_PkgOwner WHERE Name=:Name "
						);
				$stmt->bindParam(':Name', $_POST['id']);
				
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
					" SELECT Name, Phone, CellPhone, Country, Address, CompanyID, CustomerID, Notes FROM momo_PkgOwner " .
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
