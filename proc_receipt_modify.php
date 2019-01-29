<?php

	require_once 'db_config.php'; 
	
	//$func = $_POST["func"];
	$func = $_REQUEST["func"];
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ($func == 'modify')
	{
		$oper = $_POST["oper"];
		if ($oper=='edit')
		{
			try
			{
				//jqgrid 編輯時
				$stmt = $conn->prepare(
						" UPDATE momo_DeliveryData SET Date=:Date,TradeType=:TradeType,CustomerID=:CustomerID,PkgOwner1=:PkgOwner1,PkgOwner2=:PkgOwner2,Terminal=:Terminal, " .
						" PkgCount=:PkgCount,Unit=:Unit,Weight=:Weight,Volume=:Volume,Note=:Note,ShipName=:ShipName,SO=:SO,CloseDate=:CloseDate WHERE GUID=:GUID ".
						" DELETE FROM momo_TransportData WHERE DeliveryGUID=:GUID1 "
						
						/*" UPDATE DeliveryData SET Date=:Date, TradeType=:TradeType,CustomerID=:CustomerID,PkgOwner1=:PkgOwner1,PkgOwner2=:PkgOwner2,Terminal=:Terminal, " .
						" PkgCount=:PkgCount,Unit=:Unit,Weight=:Weight,Volume='0',Note='0',ShipName='0',SO='0',CloseDate='0' WHERE GUID='2' "*/
						);
				$stmt->bindParam(':Date', $_POST['Date']);
				$stmt->bindParam(':TradeType', $_POST['TradeType']);
				$stmt->bindParam(':CustomerID', $_POST['CustomerID']);
				$stmt->bindParam(':PkgOwner1', $_POST['PkgOwner1']);
				$stmt->bindParam(':PkgOwner2', $_POST['PkgOwner2']);
				$stmt->bindParam(':Terminal', $_POST['Terminal']);
				$stmt->bindParam(':PkgCount', $_POST['PkgCount']);
				$stmt->bindParam(':Unit', $_POST['Unit']);
				$stmt->bindParam(':Weight', $_POST['Weight']);
				$stmt->bindParam(':Volume', $_POST['Volume']);
				$stmt->bindParam(':Note', $_POST['Note']);
				$stmt->bindParam(':ShipName', $_POST['ShipName']);
				$stmt->bindParam(':SO', $_POST['SO']);
				$stmt->bindParam(':CloseDate', $_POST['CloseDate']);
				$stmt->bindParam(':GUID', $_POST['GUID']);
				$stmt->bindParam(':GUID1', $_POST['GUID']);
				
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
						" DELETE FROM momo_DeliveryData WHERE GUID=:GUID ; ".
						" DELETE FROM momo_TransportData WHERE DeliveryGUID=:GUID ;"
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
					" SELECT GUID,Date,(CASE WHEN TradeType = 0 THEN '進口' ELSE '出口' END) as TradeType,CustomerID,PkgOwner1,PkgOwner2, ".
					" Terminal,PkgCount,Unit,Weight,Volume,Note,ShipName,SO,CloseDate FROM momo_DeliveryData ".
					" WHERE Date= :date ORDER BY GUID "
					);
			$date = $_POST['date'];
			if ($date == "") $date = date('Y/m/d');
			$stmt->bindParam(':date', $date);
			$result = $stmt->execute();
			$data = array();
			
			if (!empty($result))
			{
				$rsp = $stmt->fetchAll();
				$i = 0;
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
