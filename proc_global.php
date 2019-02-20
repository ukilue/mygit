<?php

	require_once 'db_config.php'; 
	
	//$func = $_POST["func"];
	$func = $_REQUEST["func"];
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if ($func == 'QueryCustomer')
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
	else if ($func == 'QueryOwnerByCustomerID')
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
