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
			$stmt = $conn->query(" SELECT max(ID)+1 as ID FROM momo_DeliveryData ");	//(SELECT IFNULL(Max(GUID), 0)+1 FROM DeliveryData);
			$Delivery = $stmt->fetch();
			$stmt->closeCursor();
			
			$PkgOwner = $_POST['PkgOwner'];
			$Notes = $_POST['Notes'];
			for ($i=0; $i<sizeof($PkgOwner) ;$i++)
			{
				
				$stmt = $conn->prepare(
						" declare @GUID int ".
						" SET @GUID = (SELECT max(GUID)+1 FROM momo_DeliveryData) ".
						" INSERT INTO momo_DeliveryData (GUID,ID,Date,TradeType,CustomerID,PkgOwner,Terminal,PkgCount, ".
						" Unit,Weight,Volume,Note,ShipName,SO,CloseDate) " .
						" VALUES (@GUID,:ID,:Date,:TradeType,:CustomerID,:PkgOwner, :Terminal,:PkgCount, ".
						" :Unit,:Weight,:Volume,:Note,:ShipName,:SO,:CloseDate); "
						);
				$stmt->bindParam(':ID', $Delivery['ID']);
				$stmt->bindParam(':Date', $_POST['Date']);
				$stmt->bindParam(':TradeType', $_POST['TradeType']);
				$stmt->bindParam(':CustomerID', $_POST['CustomerID']);
				$stmt->bindParam(':PkgOwner', $PkgOwner[$i]);
				$stmt->bindParam(':Terminal', $_POST['Terminal']);
				$stmt->bindParam(':PkgCount', $_POST['PkgCount']);
				$stmt->bindParam(':Unit', $_POST['Unit']);
				$stmt->bindParam(':Weight', $_POST['Weight']);
				$stmt->bindParam(':Volume', $_POST['Volume']);
				$stmt->bindParam(':Note', $Notes[$i]);
				$stmt->bindParam(':ShipName', $_POST['ShipName']);
				$stmt->bindParam(':SO', $_POST['SO']);
				$stmt->bindParam(':CloseDate', $_POST['CloseDate']);
				//alert($stmt->$queryString);
				$result = $stmt->execute();
				$stmt->closeCursor();
				
			}
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
					" SELECT Phone, CellPhone, Address, Notes FROM momo_PkgOwner WHERE Name=:Name"
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
					$rsp[3] = $row['Notes'];
				}
				echo json_encode($rsp);
			}
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'QueryCustomer')
	{
		
		//查詢資料
		try
		{
			$stmt = $conn->prepare(
					" SELECT Name FROM momo_CustomerData WHERE ID=:ID"
					);
			$stmt->bindParam(':ID', $_POST['ID']);
			$result = $stmt->execute();
			if (!empty($result))
			{
				$data = $stmt->fetchAll();
				if (sizeof($data) > 0)
				{
					echo $data[0]['Name'];
				}
				else 
				{
					echo '查無資料';
				}
			}
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'AJAXOwner')
	{
		
		//查詢資料
		try
		{
			$stmt = $conn->prepare(
					" SELECT Name FROM momo_PkgOwner WHERE CustomerID=:ID"
					);
			$stmt->bindParam(':ID', $_POST['ID']);
			$result = $stmt->execute();
			$str = "";
			if (!empty($result))
			{
				$data = $stmt->fetchAll();
				foreach ($data as $row)
				{
					$str = $str. "<option>" . $row['Name'] . "</option>";
				}
				echo $str;
			}
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo 'connect failed:'.$error->getMessage();
		}
	}
	
?>
