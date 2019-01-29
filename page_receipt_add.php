<?php 
	require_once 'db_config.php'; 
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<script src="./assets/js/jquery-2.1.4.min.js"></script>

<script>
	function func_TypeChange(e)
	{
		var div = $(e);
		var divType0 = $("#div_Type0");
		var divType1 = $("#div_Type1");
		if(div.val()=="0")	//進口
		{
			divType0.show();
			divType1.hide();
		}
		else if(div.val()=="1") //出口
		{
			divType0.hide();
			divType1.show();
		}	
	}
	function func_ownerchanged(e, type, num)
	{
		var txt = $(e).val();
		var ownerPhone = $("#"+type+"_ownerPhone"+num);
		var ownerCellphone = $("#"+type+"_ownerCellphone"+num);
		var ownerPlace = $("#"+type+"_ownerPlace"+num);

		$.ajax({
			type : "POST", cache : false, dataType : "json",
			url: "/proc_receipt_add.php",
			data: {	func:'QueryOwner',Name:txt},
			success: function (data) {
				ownerPhone.val(data[0]);
				ownerCellphone.val(data[1]);
				ownerPlace.val(data[2]);
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});		
	}
	function func_addOwner()
	{
		var div = $("#div_pkgowner2");
		div.show();
	}	
	function func_hideOwner()
	{
		var div = $("#div_pkgowner2");
		div.hide();
	}
	function submit_add()
	{
		var tradeType0 = $("#TradeType0");
		var tradeType1 = $("#TradeType1");
		var Type = "";
		if (tradeType0.is(":checked"))Type = "0";		//進口
		 else if (tradeType1.is(":checked")) Type = "1";	//出口
		//alert("Type="+Type);
		
		var CustomerID = $.trim($("#Type"+Type+"_CustomerID").val());	//客戶
		var Terminal = $.trim($("#Type"+Type+"_Terminal").val());		//貨櫃場
		var PkgOwner1 = $.trim($("#Type"+Type+"_PkgOwner1").val());		//貨主1
		var PkgOwner2 = $.trim($("#Type"+Type+"_PkgOwner2").val());		//貨主2
		var PkgCount = $.trim($("#Type"+Type+"_PkgCount").val());		//件數
		var Unit = $.trim($("#Type"+Type+"_Unit").val());				//單位
		var Weight = $.trim($("#Type"+Type+"_Weight").val());			//重量
		var Volume = $.trim($("#Type"+Type+"_Volume").val());				//大小
		var Notes = $.trim($("#Type"+Type+"_Notes").val());				//備註
		var ShipName = $.trim($("#Type"+Type+"_ShipName").val());		//船名/航次
		var SO = $.trim($("#Type"+Type+"_SO").val());					//S/O
		var CompanyID = $.trim($("#Type"+Type+"_CompanyID").val());		//統一編號
		var CloseDate = $.trim($("#Type"+Type+"_CloseDate").val());		//結關日
		
		//alert("CustomerID="+CustomerID+",Terminal="+Terminal+",PkgOwner1="+PkgOwner1+",PkgOwner2="+PkgOwner2+",PkgCount="+PkgCount+",Unit="+Unit+",Weight="+Weight+",Size="+Size);
		
		$.ajax({
			type : "POST", cache : false, dataType : "text",
			url: "/proc_receipt_add.php",
			data: {	func:'add',TradeType:Type,CustomerID:CustomerID,Terminal:Terminal,PkgOwner1:PkgOwner1,
					PkgOwner2:PkgOwner2,PkgCount:PkgCount,Unit:Unit,Weight:Weight,
					Volume:Volume,Notes:Notes,ShipName:ShipName,SO:SO,CompanyID:CompanyID,CloseDate:CloseDate},
			success: function (data) {						
				if(data=='0') {
					window.location = "/index.php?action=receipt&type=add";
				}else  {
					alert(data);
				}	
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});		
	}
</script>
<body>
	<!--貨主資料-->
	<datalist id="owners">
<?php
	$sql= " SELECT Name FROM momo_PkgOwner ORDER BY Name";
	$result = $conn->query($sql);
	if (!empty($result))
	{
		foreach ($result as $row)
		{
			print "<option>".$row['Name']."</option>";
			
		}
	}
?>
	</datalist>
	<!--客戶資料-->
	<datalist id="customers">
<?php
	$sql= " SELECT ID, Name FROM momo_CustomerData ORDER BY Name";
	$result = $conn->query($sql);
	if (!empty($result))
	{
		foreach ($result as $row)
		{
			print "<option>".$row['ID']."_".$row['Name']."</option>";
			
		}
	}
?>
	</datalist>
	
	<p>
		<input type="radio" id="TradeType0" name="TradeType" onclick="func_TypeChange(this)" value="0" checked>進口&nbsp;
		<input type="radio" id="TradeType1" name="TradeType" onclick="func_TypeChange(this)" value="1">出口
	</p>
	<!--進口-->
	<div id="div_Type0" style="display:inline">
		<p>客戶：<input type="text" list="customers" id="Type0_CustomerID"><p>
		<p>起運(貨櫃場)：<input type="text" id="Type0_Terminal"></p>
		<p>
			到達(貨主1)：<input type="text" list="owners" id="Type0_PkgOwner1" onchange="func_ownerchanged(this, 'Type0','1')">&nbsp;
			<button style="width:30px" onclick="func_addOwner()">+</button>&nbsp;
			貨主電話：<input type="text" id="Type0_ownerPhone1" value="02xxx" disabled>&nbsp;
			行動電話：<input type="text" id="Type0_ownerCellphone1" value="09xxx" disabled>&nbsp;
			送貨地點：<input type="text" id="Type0_ownerPlace1" value="台北市" disabled>
		</p>
		<div id="div_pkgowner2" style="display:none">
			<p>
				到達(貨主2)：<input type="text" list="owners" id="Type0_PkgOwner2" onchange="func_ownerchanged(this, 'Type0','2')">&nbsp;
				<button style="width:30px" onclick="func_hideOwner()">-</button>&nbsp;
				貨主電話：<input type="text" id="Type0_ownerPhone2" value="02xxx" disabled>&nbsp;
				行動電話：<input type="text" id="Type0_ownerCellphone2" value="09xxx" disabled>&nbsp;
				送貨地點：<input type="text" id="Type0_ownerPlace2" value="台北市" disabled>
			</p>
		</div>
		<p>件數：<input type="text" id="Type0_PkgCount"></p>
		<p>
			單位：
			<select id="Type0_Unit">
				<option value="C/T">C/T</option>
				<option value="C/S">C/S</option>
				<option value="D/M">D/M</option>
				<option value="P/L">P/L</option>
				<option value="P/L">P/L</option>
				<option value="BOX">BOX</option>
			</select>
		</p>
		<p>重量(kg)：<input type="text" id="Type0_Weight"></p>
		<p>材積：<input type="text" id="Type0_Volume"></p>
		<p>備註：<input type="text" id="Type0_Notes"></p>
	</div>
	<!--出口-->
	<div id="div_Type1" style="display:none">
		<p>客戶：<input type="text" list="customers" id="Type1_CustomerID"></p>
		<p>
			起運(貨主)：<input type="text" list="owners" id="Type1_PkgOwner1" onchange="func_ownerchanged(this, 'Type1','1')">&nbsp;
			客戶電話：<input type="text" id="Type1_ownerPhone1" value="02xxx" disabled>&nbsp;
			行動電話：<input type="text" id="Type1_ownerCellphone1" value="09xxx" disabled>&nbsp;
			地點：<input type="text" id="Type1_ownerPlace1" value="台北市" disabled>
		</p>
		<p>到達(貨櫃場)<input type="text" id="Type1_Terminal"></p>
		<p>件數：<input type="text" id="Type1_PkgCount"></p>
		<p>
			單位：
			<select id="Type1_Unit">
				<option value="C/T">C/T</option>
				<option value="C/S">C/S</option>
				<option value="D/M">D/M</option>
				<option value="P/L">P/L</option>
				<option value="P/L">P/L</option>
				<option value="BOX">BOX</option>
			</select>
		</p>
		<p>重量(kg)：<input type="text" id="Type1_Weight"></p>	
		<p>材積：<input type="text" id="Type1_Volume"></p>
		<p>備註：<input type="text" id="Type1_Notes"></p>
		<p>船名/航次：<input type="text" id="Type1_ShipName"></p>
		<p>S/O：<input type="text" id="Type1_SO"></p>
		<p>統一編號：<input type="text" id="Type1_CompanyID"></p>
		<p>結關日：<input type="text" id="Type1_CloseDate"></p>
	</div>
	<button type="button" onclick="submit_add();">新增資料</button>
</body>
</html>
