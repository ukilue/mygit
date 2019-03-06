<?php
	require_once 'db_config.php'; 
	
	$func = $_POST["func"];
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	//$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ($func == 'add')
	{
		try
		{
			$Drivers = $_POST['Driver'];
			$CarType = $_POST['CarType'];
			$Price = $_POST['Price'];
			$esPrice = $_POST['esPrice'];
			$DeliveryGUID = $_POST["DeliveryGUID"];
			if ($DeliveryGUID == "-1")
			{
				$sql= " SELECT IFNULL(Min(DeliveryGUID), 0)-1 as MinID FROM TransportData ";
				foreach ($conn->query($sql) as $row) {
					$DeliveryGUID = $row['MinID'];
				}
			}
			
			for ($i = 0; $i < sizeof($Drivers); $i++)
			{
				//insert 逐筆資料
				$stmt = $conn->prepare(
					//" declare @maxGUID int".
					//" SET @maxGUID = (SELECT max(GUID)+1 FROM TransportData) ".						
					" SET @maxGUID = (SELECT IFNULL(Max(GUID), 0)+1 FROM TransportData); ".
					" INSERT INTO TransportData (GUID, DeliveryGUID, CustGUID, Date, PkgOwner, DriverGUID, StartPlace, SendPlace, PkgCount, Weight, Volume, CarType, Price, Notes)" .
					" VALUES (@maxGUID, :DeliveryGUID,:CustGUID,:Date,:PkgOwner, :DriverGUID, :StartPlace, :SendPlace, :PkgCount, :Weight, :Volume, :CarType, :Price, :Notes) "
					//CURDATE()
					);
				$stmt->bindParam(':DeliveryGUID', $DeliveryGUID);
				$stmt->bindParam(':CustGUID', $_POST["CustomerID"]);
				$stmt->bindParam(':Date', $_POST["DeliveryDate"]);
				$stmt->bindParam(':PkgOwner', $_POST["PkgOwner"]);
				$stmt->bindParam(':DriverGUID', $Drivers[$i]);
				$stmt->bindParam(':Notes', $_POST["Notes"]);
				$Country = $_POST['Country'];
				$stmt->bindParam(':PkgCount', $_POST["PkgCount"]);
				$stmt->bindParam(':Weight', $_POST["Weight"]);
				$stmt->bindParam(':Volume', $_POST["Volume"]);
				$stmt->bindParam(':CarType', $CarType[$i]);
				$stmt->bindParam(':Price', $Price[$i]);
				
				if ($_POST["TradeType"] == "0")
				{
					//進口
					$stmt->bindParam(':StartPlace', $_POST["Terminal"]);
					$stmt->bindParam(':SendPlace', $Country);
				}
				else if ($_POST["TradeType"] == "1")
				{
					//出口
					$stmt->bindParam(':StartPlace', $Country);
					$stmt->bindParam(':SendPlace', $_POST["Terminal"]);
				}
				
				$result = $stmt->execute();
				$stmt->closeCursor();
				
				if($esPrice[$i] == '查無資料')
				{
					$Car = $CarType[$i];
					$column = str_replace(".","_","ton".$Car);
					
					//select 運費表
					$stmt = $conn->prepare(
						" SELECT Country FROM FeeData WHERE Country =:Country "
						);
					$stmt->bindParam(':Country', $Country);
					$result = $stmt->execute();
					$rsp = $stmt->fetchAll();
					if (sizeof($rsp) > 0)
					{
						$stmt->closeCursor();
						//update
						$sql = 
							" UPDATE FeeData SET $column = '$Price[$i]' WHERE Country ='$Country' ";
					}
					else
					{
						$stmt->closeCursor();
						//insert
						$sql = " INSERT INTO FeeData (Country, $column) VALUES ('$Country', '$Price[$i]')";
					}
					$conn->exec($sql);
				}
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
					" SELECT GUID,ID,Date,TradeType,a.CustomerID,(SELECT Name FROM CustomerData WHERE ID = a.CustomerID) as CustomerName, ".
					" PkgOwner,Left(b.Address,2) as Country,Terminal,PkgCount,Unit,Weight,Volume,Note,ShipName,SO,CloseDate ".
					" FROM DeliveryData a ".
					" LEFT JOIN PkgOwner b ".
					" ON a.PkgOwner = b.Name AND a.CustomerID = b.CustomerID ".
					" WHERE ID = ( ".
					"	SELECT ID ". //TOP(1) 
					"	FROM DeliveryData ".
					"	WHERE GUID NOT IN (SELECT DISTINCT DeliveryGUID FROM TransportData) ".
					"	GROUP BY ID ".
					"	ORDER BY ID LIMIT 1 ". //  
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
	else if ($func == 'queryDriver')
	{
		try
		{
			$stmt = $conn->prepare(
					" SELECT Name FROM DriverData WHERE GUID=:GUID"
					);
			$stmt->bindParam(':GUID', $_POST["Driver"]);
			$result = $stmt->execute();
			$str = "";
			if (!empty($result))
			{
				$data = $stmt->fetchAll();
				if(sizeof($data) >0)
					echo $data[0]['Name'];
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
					" SELECT $column FROM FeeData " .
					" WHERE Country=:Country"
					);
			$stmt->bindParam(':Country', $_POST["Country"]);
			$result = $stmt->execute();
			$data = $stmt->fetchAll();
			
			if(sizeof($data) >0)
				echo $data[0]["$column"];
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'queryCustomer')
	{
		try
		{
			$CustomerID = $_POST["ID"];
			$stmt = $conn->prepare(
					" SELECT Name FROM CustomerData WHERE ID=:ID"
					);
			$stmt->bindParam(':ID', $CustomerID);
			$result = $stmt->execute();
			if (!empty($result))
				$data = $stmt->fetchAll();
			echo $data[0]['Name'];
			$stmt->closeCursor();
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'querylastData')
	{
		try
		{
			$PkgOwner = $_POST["PkgOwner"];
			$CarType = $_POST["CarType"];
			
			$stmt = $conn->prepare(
					" SELECT a.Date, b.Name, a.CarType, a.StartPlace, a.SendPlace, a.PkgCount, a.Weight, a.Volume, a.Price, a.Notes " .	//TOP(5) 
					" FROM TransportData as a  " .
					" INNER JOIN DriverData as b ON a.DriverGUID = b.GUID " .
					" WHERE PkgOwner=:PkgOwner and CarType=:CarType ORDER BY a.GUID DESC LIMIT 5" );
			$stmt->bindParam(':PkgOwner', $PkgOwner);
			$stmt->bindParam(':CarType', $CarType);
			$result = $stmt->execute();
			$data = $stmt->fetchAll();
			//print_r($data);
			echo json_encode($data);
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
	else if ($func == 'queryOwnerCountry')
	{
		try
		{
			$OwnerName = $_POST["Name"];
			
			$stmt = $conn->prepare(
					" SELECT left(Address,2) as place FROM PkgOwner WHERE Name=:Name" );
			$stmt->bindParam(':Name', $OwnerName);
			$result = $stmt->execute();
			$data = $stmt->fetchAll();
			if(sizeof($data) >0)
				echo $data[0]["place"];
		}
		catch (PDOException $error) 
		{
			echo  'connect failed:'.$error->getMessage();
		}
	}
?>