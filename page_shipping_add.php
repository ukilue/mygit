<?php 
	require_once 'db_config.php';
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<script type="text/javascript">
$.ajax({
	type : "POST", cache : false, dataType : "json",
	url: "/proc_shipping_add.php",
	data: {	func:'query'},
	success: DataLoaded,
	error: function (e) {
		alert("系統異常, 請稍候再試");
	}		
});
function DataLoaded(data)
{
	if (data=='')
	{
		alert("無下一筆資料需編輯");
		/*$("#btn_save").attr('disabled', true);
		$("#Type0_Dirver1").attr('disabled', true);
		$("#Type0_Dirver2").attr('disabled', true);
		$("#Type1_Dirver1").attr('disabled', true);
		$("#Type0_Price1").attr('disabled', true);
		$("#Type0_Price2").attr('disabled', true);
		$("#Type1_Price1").attr('disabled', true);
		*/
		return;
	}
	var TradeType = data[0]['TradeType'];	//同一編號送貨單應為同一類型(出口或進口)
	var divType0 = $("#div_Type0");			//進口
	var divType1 = $("#div_Type1");			//出口
	//共通資料
	$("#DeliveryGUID").val(data[0]['GUID']);
	$("#DeliveryDate").val(data[0]['Date']);
	$("#CustomerID").val(data[0]['CustomerID']);
	$("#CustomerName").val(data[0]['CustomerName']);
	$("#PkgCount").val(data[0]['PkgCount']);
	$("#Unit").val(data[0]['Unit']);
	$("#Weight").val(data[0]['Weight']);
	$("#Volume").val(data[0]['Volume']);
	var input_TradeType = $("#TradeType");
	
	
	//outerDIV
	var outerdiv = $("#div_Owner");
	
	var i;
	for (i = 0; i < data.length; i++)
	{
		outerdiv.append(
			"<p>" +
			"	<label name=\"OwnerLabel[]\">貨主(到達"+(i+1)+")：</label><input type=\"text\" id=\"Type0_PkgOwner\""+(i+1)+" name=\"Owner[]\" size=\"5px\" value=\""+data[i]['PkgOwner']+"\" disabled>&nbsp; " +
			"	送貨地點：<input type=\"text\" id=\"Type0_Country\""+(i+1)+" name=\"OwnerPlace[]\" size=\"5px\" value=\""+data[i]['Country']+"\" disabled>&nbsp; "+
			"	備註：<input type=\"text\" id=\"Type0_Notes\""+(i+1)+" name=\"OwnerNotes[]\" size=\"5px\" value=\""+data[i]['Note']+"\" disabled>&nbsp; "+
			"	<button type=\"button\" name=\"BtnAddDriver[]\" onclick=\"AddDriver('"+(i+1)+"')\" >新增司機</button> "+
			"</p>"+
			"<div id=\"div_Driver"+(i+1)+"\"></div>"
		);
	}
	/*
	var input_PkgOwner1 = $("#Type"+TradeType+"_PkgOwner1");
	var input_Country1 = $("#Type"+TradeType+"_Country1");
	var input_esPrice1 = $("#Type"+TradeType+"_esPrice1");
	var input_PkgOwner2 = $("#Type"+TradeType+"_PkgOwner2");
	var input_Country2 = $("#Type"+TradeType+"_Country2");
	var input_esPrice2 = $("#Type"+TradeType+"_esPrice2");
	*/
	if (TradeType=='0')
	{
		input_TradeType.val('進口');
		$("#Type0_Terminal").val(data[0]['Terminal']);
		divType0.show();
		divType1.hide();
	}
	else if (TradeType=='1')
	{
		input_TradeType.val('出口');
		$("#Type1_Terminal").val(data[0]['Terminal']);
		divType0.hide();
		divType1.show();
	}
	/*
	if (input_PkgOwner2.val()=="")	//沒有第二個owner
	{
		var divOwner2 = $("#div_Owner2");
		var divDriver2 = $("#div_Driver2");
		divOwner2.hide();
		divDriver2.hide();
	}
	input_DeliveryGUID.val(data[0]['GUID']);
	input_CustomerID.val(data[0]['CustomerID']);
	input_CustomerName.val(data[0]['CustomerName']);
	input_PkgCount.val(data[0]['PkgCount']);
	input_Unit.val(data[0]['Unit']);
	input_Weight.val(data[0]['Weight']);
	input_Volume.val(data[0]['Volume']);
	input_Notes.val(data[0]['Notes']);
	input_Terminal.val(data[0]['Terminal']);
	input_PkgOwner1.val(data[0]['PkgOwner1']);
	input_Country1.val(data[0]['Country1']);
	input_PkgOwner2.val(data[0]['PkgOwner2']);
	input_Country2.val(data[0]['Country2']);
	*/
	//alert(TradeType);
	//alert(data[0]['GUID']);
	
	//alert("Country1="+data[0]['Country1']+"Country2="+data[0]['Country2']+"Volume="+data[0]['Volume']);
}
function AddDriver(pkgnum)
{
	var outerdiv = $("#div_Driver"+pkgnum);
	var Drivers = document.getElementsByName("Dirver[]");
	var DriverNum = Drivers.length + 1;
	outerdiv.append(
		"<div id=\"Owner"+pkgnum+"_Driver"+DriverNum+"\">
		"	<p>" +
		"		<button type=\"button\" name=\"BtnDelDriver"+pkgnum+"[]\" onclick=\"DelDriver('"+pkgnum+"','"+DriverNum+"')\" >刪除</button> " +
		"		司機：<input type=\"text\" list=\"Driver\" id=\"Type0_Dirver1\" name=\"Dirver"+pkgnum+"[]\" >&nbsp; "+
		"		車種：<input type=\"text\" id=\"Type0_CarType1\" name=\"CarType"+pkgnum+"[]\" onchange=\"CarTypeChanged('Type0_Country1','Type0_CarType1','Type0_esPrice1');\">&nbsp; "+
		"		金額：<input type=\"text\" id=\"Type0_Price1\" name=\"Price"+pkgnum+"[]\" >&nbsp; "+
		"		預估金額：<input type=\"text\" id=\"Type0_esPrice1\" name=\"esPrice"+pkgnum+"[]\" disabled> "+
		"	</p>"+
		"</div>"
	);
}
function DelDriver(pkgnum,DriverNum)
{
	var outerdiv = $("#div_Driver"+pkgnum);
	var driverDiv = $("#Owner"+pkgnum+"_Driver"+DriverNum);
	driverDiv.remove();
	//重新設定html
	var Drivers = document.getElementsByName("Dirver[]");
	var i;
	for (i = 0; i < Drivers.length; i++)
	{
		var OwnerDiv = document.getElementsByName("OwnerDiv[]");
		var OwnerLabel = document.getElementsByName("OwnerLabel[]");
		var Owners = document.getElementsByName("Owner[]");
		var OwnerBtn = document.getElementsByName("OwnerBtn[]");
		var OwnerPhone = document.getElementsByName("OwnerPhone[]");
		var OwnerCellphone = document.getElementsByName("OwnerCellphone[]");
		var OwnerPlace = document.getElementsByName("OwnerPlace[]");
		var OwnerNotes = document.getElementsByName("OwnerNotes[]");
		OwnerDiv[i].setAttribute("id", "div_Pkgowner" + (i+1));
		OwnerLabel[i].innerHTML = "貨主(到達"+(i+1)+")：";
		Owners[i].setAttribute("id", "Type0_PkgOwner" + (i+1));
		Owners[i].setAttribute("onchange", "func_ownerchanged(this, '0','"+(i+1)+"')");
		OwnerBtn[i].setAttribute("id", "Type0_OwnerBtn" + (i+1));
		OwnerBtn[i].setAttribute("onchange", "func_removeOwner('"+(i+1)+"')");
		OwnerPhone[i].setAttribute("id", "Type0_OwnerPhone" + (i+1));
		OwnerCellphone[i].setAttribute("id", "Type0_OwnerCellphone" + (i+1));
		OwnerPlace[i].setAttribute("id", "Type0_OwnerPlace" + (i+1));
		OwnerNotes[i].setAttribute("id", "Type0_OwnerNotes" + (i+1));
	}
}
function CarTypeChanged(txt_Country, txt_CarType, txt_esPrice)
{
	var input_Country = $("#"+txt_Country);
	var input_CarType = $("#"+txt_CarType);
	var input_esPrice = $("#"+txt_esPrice);
	//alert(input_Country.val()+" " +input_CarType.val());
	$.ajax({
		type : "POST", cache : false, dataType : "json",
		url: "/proc_shipping_add.php",
		data: {	func:'queryFee',Country:input_Country.val(),CarType:input_CarType.val()},
		success: function (fee) {
			if (fee=="") input_esPrice.val("查無資料或車種錯誤");
			 else input_esPrice.val(fee[0][0]);
		},
		error: function (e) {
			input_esPrice.val("查無資料或車種錯誤");
		}		
	});
}
function submit_save()
{
	var TradeType = $("#TradeType");
	var Type = "";
	if (TradeType.val() == "進口") Type="0";
	 else if (TradeType.val() == "出口") Type = "1";
	
	//alert("Type="+Type);
	
	var DeliveryGUID = $.trim($("#DeliveryGUID").val());	//送貨單編號
	var DeliveryDate = $.trim($("#DeliveryDate").val());	//客戶
	var CustomerID = $.trim($("#CustomerID").val());	//客戶
	var PkgCount = $.trim($("#PkgCount").val());		//件數
	var Unit = $.trim($("#Unit").val());				//單位
	var Weight = $.trim($("#Weight").val());			//重量
	var Volume = $.trim($("#Volume").val());			//材積
	var Terminal = $.trim($("#Type"+Type+"_Terminal").val());		//貨櫃場
	var PkgOwner1 = $.trim($("#Type"+Type+"_PkgOwner1").val());		//貨主1
	var PkgOwner2 = $.trim($("#Type"+Type+"_PkgOwner2").val());		//貨主2
	var PkgOwner3 = $.trim($("#Type"+Type+"_PkgOwner3").val());		//貨主2
	var Country1 = $.trim($("#Type"+Type+"_Country1").val());		//貨主地點1
	var Country2 = $.trim($("#Type"+Type+"_Country2").val());		//貨主地點2
	var Dirver1 = $.trim($("#Type"+Type+"_Dirver1").val());			//司機1
	var Dirver2 = $.trim($("#Type"+Type+"_Dirver2").val());			//司機2
	var CarType1 = $.trim($("#Type"+Type+"_CarType1").val());		//車種2
	var CarType2 = $.trim($("#Type"+Type+"_CarType2").val());		//車種2
	var Price1 = $.trim($("#Type"+Type+"_Price1").val());			//金額1
	var Price2 = $.trim($("#Type"+Type+"_Price2").val());			//金額2
	var Notes = $.trim($("#Notes").val());				//備註
	
	//alert("CustomerID="+CustomerID+",Terminal="+Terminal+",PkgOwner1="+PkgOwner1+",PkgOwner2="+PkgOwner2+",PkgCount="+PkgCount+",Unit="+Unit+",Weight="+Weight+",Size="+Size);
	
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_shipping_add.php",
		data: {	func:'add',TradeType:Type,DeliveryDate:DeliveryDate,CustomerID:CustomerID,DeliveryGUID:DeliveryGUID,PkgCount:PkgCount,Unit:Unit,Weight:Weight,
				Volume:Volume,Terminal:Terminal,PkgOwner1:PkgOwner1,PkgOwner2:PkgOwner2,Country1:Country1,Country2:Country2,Notes:Notes,
				Dirver1:Dirver1,Dirver2:Dirver2,CarType1:CarType1,CarType2:CarType2,Price1:Price1,Price2:Price2},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=shipping&type=add";
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
	<!--司機資料-->
	<datalist id="Driver">
<?php
	$sql= " SELECT GUID, Name FROM momo_DriverData ORDER BY Name";
	$result = $conn->query($sql);
	if (!empty($result))
	{
		foreach ($result as $row)
		{
			print "<option>".$row['GUID']."_".$row['Name']."</option>";
			
		}
	}
?>
	</datalist>
	<p>
		送貨單編號：<input type="text" id="DeliveryGUID" size="5px" disabled>&nbsp;
		載運類型：<input type="text" id="TradeType" size="5px" disabled>
	</p>
	<p>
		日期：<input type="text" id="DeliveryDate" size="10px" disabled>&nbsp;
		客戶：<input type="text" id="CustomerID" size="5px" disabled>&nbsp;
		客戶名稱：<input type="text" id="CustomerName" size="8px" disabled>
	</p>
	<fieldset>
		<legend>貨物資料</legend>
		<p>
			件數：<input type="text" id="PkgCount" size="3px" disabled>&nbsp;
			單位：<input type="text" id="Unit" size="3px" disabled>&nbsp;
			重量(kg)：<input type="text" id="Weight" size="3px" disabled>&nbsp;
			材積：<input type="text" id="Volume" size="3px" disabled>
		</p>
		<p>起運(貨櫃場)：<input type="text" id="Type0_Terminal" size="5px"  disabled></p>
	</fieldset>
	
	<fieldset>
		<legend>運送資料設定</legend>
		<!--進口-->
		<div id="div_Type0" style="display:inline">
			<div id="div_Owner">
				<!--<p>
					貨主(到達1)：<input type="text" id="Type0_PkgOwner1" size="5px" disabled>&nbsp;
					送貨地點：<input type="text" id="Type0_Country1" size="5px" value="" disabled>
				</p>
				<div id="div_Driver1"></div>
				
				<p>
					到達(貨主2)：<input type="text"  id="Type0_PkgOwner2" disabled>&nbsp;
					送貨地點：<input type="text" id="Type0_Country2" value="" disabled>
				</p>
				-->
			</div>
			<!--
			<p>
				司機1：<input type="text" list="Driver" id="Type0_Dirver1">&nbsp;
				車種1：<input type="text" id="Type0_CarType1" onchange="CarTypeChanged('Type0_Country1','Type0_CarType1','Type0_esPrice1');">&nbsp;
				金額1：<input type="text" id="Type0_Price1">&nbsp;
				預估金額1：<input type="text" id="Type0_esPrice1" disabled>
			</p>
			<div id="div_Driver2" style="display:inline">
				<p>
					司機2：<input type="text" list="Driver" id="Type0_Dirver2">&nbsp;
					車種2：<input type="text" id="Type0_CarType2" onchange="CarTypeChanged('Type0_Country2','Type0_CarType2','Type0_esPrice2');">&nbsp;
					金額2：<input type="text" id="Type0_Price2">&nbsp;
					預估金額2：<input type="text" id="Type0_esPrice2" disabled>
				</p>
			</div>
			
			-->
		</div>
		<!--出口-->
		<div id="div_Type1" style="display:none">
			<p>
				起運(貨主)：<input type="text" id="Type1_PkgOwner1" disabled>&nbsp;
				提貨地點：<input type="text" id="Type1_Country1" value="" disabled>
			</p>
			<p>到達(貨櫃場)：<input type="text" id="Type1_Terminal" disabled></p>
			<p>
				司機：<input type="text" list="Driver" id="Type1_Dirver1">&nbsp;
				車種：<input type="text" id="Type1_CarType1" onchange="CarTypeChanged('Type1_Country1','Type1_CarType1','Type1_esPrice1');">&nbsp;
				金額：<input type="text" id="Type1_Price1">&nbsp;
				預估金額：<input type="text" id="Type1_esPrice1" disabled>
			</p>
			<p>備註：<input type="text" id="Notes" size="50px"></p>
		</div>
	</fieldset>
	<button type="button" id="btn_save" onclick="submit_save();">儲存資料&編輯次筆</button>
