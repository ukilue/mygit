<?php

	require_once 'db_config.php'; 
	
	$func = $_POST["func"];
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ($func == 'add')
	{
	//新增資料
		try
		{
			$stmt = $conn->prepare(
					" declare @maxGUID int".
					" SET @maxGUID = (SELECT max(GUID)+1 FROM momo_DeliveryData) ".	
					" declare @getdate varchar(10) ".
					" SET @getdate = (SELECT convert(varchar, getdate(),111)) ".
					//" SET @maxGUID = (SELECT IFNULL(Max(GUID), 0)+1 FROM DeliveryData); ".
					//" SET @getdate = (SELECT DATE_FORMAT(CURDATE(),'%Y/%m/%d')); ". 
					" INSERT INTO momo_DeliveryData (GUID,Date,TradeType,CustomerID,PkgOwner1,PkgOwner2, ".
					" Terminal,PkgCount,Unit,Weight,Volume,Note,ShipName,SO,CloseDate) " .
					" VALUES (@maxGUID,@getdate,:TradeType,:CustomerID,:PkgOwner1,:PkgOwner2, ".
					" :Terminal,:PkgCount,:Unit,:Weight,:Volume,:Note,:ShipName,:SO,:CloseDate); "
					);
			$stmt->bindParam(':TradeType', $_POST['TradeType']);
			$cid = $_POST['CustomerID'];
			$CustomerID = substr($cid, 0, strpos($cid,"_"));
			$stmt->bindParam(':CustomerID', $CustomerID);
			$stmt->bindParam(':PkgOwner1', $_POST['PkgOwner1']);
			$stmt->bindParam(':PkgOwner2', $_POST['PkgOwner2']);
			$stmt->bindParam(':Terminal', $_POST['Terminal']);
			$stmt->bindParam(':PkgCount', $_POST['PkgCount']);
			$stmt->bindParam(':Unit', $_POST['Unit']);
			$stmt->bindParam(':Weight', $_POST['Weight']);
			$stmt->bindParam(':Volume', $_POST['Volume']);
			$stmt->bindParam(':Note', $_POST['Notes']);
			$stmt->bindParam(':ShipName', $_POST['ShipName']);
			$stmt->bindParam(':SO', $_POST['SO']);
			$stmt->bindParam(':CloseDate', $_POST['CloseDate']);
			//alert($stmt->$queryString);
			$result = $stmt->execute();
			$stmt->closeCursor();
			echo 0;
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'QueryOwner')
	{
	//查詢資料
		try
		{
			$stmt = $conn->prepare(
					" SELECT Name, Phone, CellPhone, Address FROM momo_PkgOwner WHERE Name=:Name"
					);
			$stmt->bindParam(':Name', $_POST['Name']);
			$result = $stmt->execute();
			$rowCount= $stmt->rowCount();
			$rsp = array();
			
			if (!empty($result))
			{
				$result = $stmt->fetchAll();
				foreach ($result as $row)
				{
					$rsp[0] = $row['Phone'];
					$rsp[1] = $row['CellPhone'];
					$rsp[2] = $row['Address'];
					echo json_encode($rsp);
				}
			}
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	
?>
