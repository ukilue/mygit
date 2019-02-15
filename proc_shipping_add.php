<?php

	require_once 'db_config.php'; 
	
	$func = $_POST["func"];
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ($func == 'add')
	{
		try
		{
			//判斷是否有兩個司機，若是則insert 2筆貨運單
			$dNum = 1;
			if ($_POST['Dirver2'] != "") $dNum = 2;
			for ($i = 1; $i<= $dNum ; $i++)
			{
				$stmt = $conn->prepare(
					" declare @maxGUID int".
					" SET @maxGUID = (SELECT max(GUID)+1 FROM momo_TransportData) ".						
					//" SET @maxGUID = (SELECT IFNULL(Max(GUID), 0)+1 FROM TransportData); ".
					" INSERT INTO momo_TransportData (GUID, DeliveryGUID, CustGUID, Date, PkgOwner, DriverGUID, StartPlace, SendPlace, PkgCount, Weight, Volume, CarType, Price, Notes)" .
					" VALUES (@maxGUID, :DeliveryGUID,:CustGUID, convert(varchar, getdate(),111), :PkgOwner, :DriverGUID, :StartPlace, :SendPlace, :PkgCount, :Weight, :Volume, :CarType, :Price, :Notes) "
					//CURDATE()
					);
				$stmt->bindParam(':CustGUID', $_POST["CustomerID"]);
				$stmt->bindParam(':DeliveryGUID', $_POST["DeliveryGUID"]);
				$stmt->bindParam(':PkgOwner', $_POST["PkgOwner".$i]);
				
				$Driver = $_POST["Dirver".$i];
				$DriverGUID = substr($Driver, 0, strpos($Driver,"_"));
				$stmt->bindParam(':DriverGUID', $DriverGUID);
				if ($_POST["TradeType"] == "0")
				{
					//進口
					$stmt->bindParam(':StartPlace', $_POST["Terminal"]);
					$stmt->bindParam(':SendPlace', $_POST["Country".$i]);
				}
				else if ($_POST["TradeType"] == "1")
				{
					//出口
					$stmt->bindParam(':StartPlace', $_POST["Country".$i]);
					$stmt->bindParam(':SendPlace', $_POST["Terminal"]);
				}
				$stmt->bindParam(':PkgCount', $_POST["PkgCount"]);
				$stmt->bindParam(':Weight', $_POST["Weight"]);
				$stmt->bindParam(':Volume', $_POST["Volume"]);
				$stmt->bindParam(':CarType', $_POST["CarType".$i]);
				$stmt->bindParam(':Price', $_POST["Price".$i]);
				$stmt->bindParam(':Notes', $_POST["Notes"]);
				
				$result = $stmt->execute();
				$stmt->closeCursor();
			}
			echo "0";
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'query')
	{
		try
		{
			$stmt = $conn->prepare(
					" SELECT GUID,ID,Date,TradeType,a.CustomerID,(SELECT Name FROM momo_CustomerData WHERE ID = a.CustomerID) as CustomerName, ".
					" PkgOwner,Left(b.Address,2) as Country,Terminal,PkgCount,Unit,Weight,Volume,Note,ShipName,SO,CloseDate ".
					" FROM momo_DeliveryData a ".
					" LEFT JOIN momo_PkgOwner b ".
					" ON a.PkgOwner = b.Name ".
					" WHERE ID = ( ".
					"	SELECT TOP(1) ID ".
					"	FROM momo_DeliveryData ".
					"	WHERE GUID NOT IN (SELECT DISTINCT DeliveryGUID FROM momo_TransportData) ".
					"	GROUP BY ID ".
					"	ORDER BY ID ".
					" ) ORDER BY GUID "
					//" WHERE Date=CURRENT_DATE() AND GUID NOT IN (SELECT DISTINCT DeliveryGUID FROM TransportData) ORDER BY GUID LIMIT 1 "
					
					);
			$result = $stmt->execute();
			$data = array();
			
			if (!empty($result))
			{
				$rsp = $stmt->fetchAll();
				echo json_encode($rsp);
			}
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'queryFee')
	{
		try
		{
			$CarType = $_POST["CarType"];
			$column = str_replace(".","_","ton".$CarType);
			//$data = array();
			//echo $column;
			$stmt = $conn->prepare(
					" SELECT $column FROM momo_FeeData " .
					" WHERE Country=:Country"
					);
			$stmt->bindParam(':Country', $_POST["Country"]);
			$result = $stmt->execute();
			if (!empty($result))
				$data = $stmt->fetchAll();
			echo json_encode($data);
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	
?>
