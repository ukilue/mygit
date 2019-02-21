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
			$stmt = $conn->prepare(
					//" SET @maxGUID = (SELECT IFNULL(Max(GUID), 0)+1 FROM PkgOwner);".
					" INSERT INTO momo_CustomerData (ID, Name, Principal, Email, Phone, CellPhone, Fax, Address, Notes) ".
					" VALUES (:ID, :Name, :Principal, :Email, :Phone, :CellPhone, :Fax, :Address, :Notes);"
					);
			$stmt->bindParam(':ID', $_POST['ID']);
			$stmt->bindParam(':Name', $_POST['Name']);
			$stmt->bindParam(':Principal', $_POST['Principal']);
			$stmt->bindParam(':Email', $_POST['Email']);
			$stmt->bindParam(':Phone', $_POST['Phone']);
			$stmt->bindParam(':CellPhone', $_POST['CellPhone']);
			$stmt->bindParam(':Fax', $_POST['Fax']);
			$stmt->bindParam(':Address', $_POST['Address']);
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
						" UPDATE momo_CustomerData SET Name=:Name, Principal=:Principal, Email=:Email, Phone=:Phone, CellPhone=:CellPhone, Fax=:Fax, ".
						" Address=:Address, Notes=:Notes WHERE ID=:ID "
						
						/*" UPDATE DeliveryData SET Date=:Date, TradeType=:TradeType,CustomerID=:CustomerID,PkgOwner1=:PkgOwner1,PkgOwner2=:PkgOwner2,Terminal=:Terminal, " .
						" PkgCount=:PkgCount,Unit=:Unit,Weight=:Weight,Volume='0',Note='0',ShipName='0',SO='0',CloseDate='0' WHERE GUID='2' "*/
						);
				$stmt->bindParam(':Name', $_POST['Name']);
				$stmt->bindParam(':Principal', $_POST['Principal']);
				$stmt->bindParam(':Email', $_POST['Email']);
				$stmt->bindParam(':Phone', $_POST['Phone']);
				$stmt->bindParam(':CellPhone', $_POST['CellPhone']);
				$stmt->bindParam(':Fax', $_POST['Fax']);
				$stmt->bindParam(':Address', $_POST['Address']);
				$stmt->bindParam(':Notes', $_POST['Notes']);
				$stmt->bindParam(':ID', $_POST['ID']);
				
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
						" DELETE FROM momo_CustomerData WHERE ID=:ID "
						);
				$stmt->bindParam(':ID', $_POST['id']);
				
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
					" SELECT ID, Name, Principal, Email, Phone, CellPhone, Fax, Address, Notes FROM momo_CustomerData " .
					" WHERE (ID like '%'+ :ID +'%') OR (Name like '%'+ :Name +'%') "
					);
			$stmt->bindParam(':ID', $_POST['keyword']);
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
