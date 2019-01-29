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
				//CustGUID, PkgOwner, DriverGUID, StartPlace, SendPlace, PkgCount, Weight, Volume, CarType, Price, Notes
						" UPDATE momo_TransportData SET CustGUID=:CustGUID,PkgOwner=:PkgOwner,DriverGUID=:DriverGUID,StartPlace=:StartPlace,SendPlace=:SendPlace,PkgCount=:PkgCount, " .
						" Weight=:Weight,Volume=:Volume,CarType=:CarType,Price=:Price,Notes=:Notes WHERE GUID=:GUID "
						);
				$stmt->bindParam(':CustGUID', $_POST['CustGUID']);
				$stmt->bindParam(':PkgOwner', $_POST['PkgOwner']);
				$stmt->bindParam(':DriverGUID', $_POST['DriverGUID']);
				$stmt->bindParam(':StartPlace', $_POST['StartPlace']);
				$stmt->bindParam(':SendPlace', $_POST['SendPlace']);
				$stmt->bindParam(':PkgCount', $_POST['PkgCount']);
				$stmt->bindParam(':Weight', $_POST['Weight']);
				$stmt->bindParam(':Volume', $_POST['Volume']);
				$stmt->bindParam(':CarType', $_POST['CarType']);
				$stmt->bindParam(':Price', $_POST['Price']);
				$stmt->bindParam(':Notes', $_POST['Notes']);
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
						" DELETE FROM momo_TransportData WHERE GUID=:GUID ; "
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
					" SELECT GUID, Date, DeliveryGUID, CustGUID, PkgOwner, DriverGUID, StartPlace, SendPlace, PkgCount, Weight, Volume, CarType, Price, Notes ".
					"  FROM momo_TransportData ".
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
