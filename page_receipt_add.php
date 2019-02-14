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
<!-- 日期 -->
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>

<script>
	jQuery(function($) {			
		$( "#datepicker" ).datepicker({
			showOtherMonths: true,
			selectOtherMonths: false,
			dateFormat: 'yy/mm/dd',
		});	
		$( "#datepicker" ).zIndex(1000);
		var today = new Date();
		var tomorrow = new Date();
		tomorrow.setDate(today.getDate()+1);
		var day = tomorrow.getDate();
		if (day <10) day = "0"+day;
		var month = tomorrow.getMonth()+1;
		if (month <10) month = "0"+month;
		var year = tomorrow.getFullYear();
		$( "#datepicker" ).val(year+"/"+month+"/"+day);
	});

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
	function func_showCustomerdata()
	{
		var ID = $("#CustomerID").val();
		
		$.ajax({
			type : "POST", cache : false, dataType : "text",
			url: "/proc_receipt_add.php",
			data: {	func:'QueryCustomer',ID:ID},
			success: function (data) {
				$("#CustomerName" ).val(data);
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});	
		
		document.getElementById("owners").innerHTML="";
		$.ajax({
			type : "POST", cache : false, dataType : "text",
			url: "/proc_receipt_add.php",
			data: {	func:'AJAXOwner',ID:ID},
			success: function (data) {
				document.getElementById("owners").innerHTML=data;
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});	
	}
	function func_ownerchanged(e, type, num)
	{
		var txt = $(e).val();
		var ownerPhone = $("#Type"+type+"_OwnerPhone"+num);
		var ownerCellphone = $("#Type"+type+"_OwnerCellphone"+num);
		var ownerPlace = $("#Type"+type+"_OwnerPlace"+num);
		var ownerNotes = $("#Type"+type+"_OwnerNotes"+num);

		$.ajax({
			type : "POST", cache : false, dataType : "json",
			url: "/proc_receipt_add.php",
			data: {	func:'QueryOwner',Name:txt},
			success: function (data) {
				ownerPhone.val(data[0]);
				ownerCellphone.val(data[1]);
				ownerPlace.val(data[2]);
				ownerNotes.val(data[3]);
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});	
		
	}
	function func_addOwner()
	{
		var outerdiv = $("#div_Pkgowners");
		var PkgOwnerCount = $("#Type0_PkgOwnerCount").val();
		PkgOwnerCount = parseInt(PkgOwnerCount) +1;
		outerdiv.append(
			"<div id=\"div_Pkgowner"+PkgOwnerCount+"\" name=\"OwnerDiv[]\">" + 
			"<p>"+
			"	<label id=\"Type0_OwnerLabel\" name=\"OwnerLabel[]\">貨主(到達"+PkgOwnerCount+")：</label><input type=\"text\" list=\"owners\" id=\"Type0_PkgOwner"+PkgOwnerCount+"\" name=\"Owner[]\" onchange=\"func_ownerchanged(this, '0','"+PkgOwnerCount+"')\">&nbsp; "+
			"	<button style=\"width:30px\" id=\"Type0_OwnerBtn"+PkgOwnerCount+"\" name=\"OwnerBtn[]\" onclick=\"func_removeOwner('"+PkgOwnerCount+"')\">-</button>&nbsp; "+
			"	貨主電話：<input type=\"text\" id=\"Type0_OwnerPhone"+PkgOwnerCount+"\" name=\"OwnerPhone[]\" value=\"\" size=\"8px\" disabled>&nbsp; "+
			"	行動電話：<input type=\"text\" id=\"Type0_OwnerCellphone"+PkgOwnerCount+"\" name=\"OwnerCellphone[]\" value=\"\" size=\"8px\" disabled>&nbsp; "+
			"	送貨地點：<input type=\"text\" id=\"Type0_OwnerPlace"+PkgOwnerCount+"\" name=\"OwnerPlace[]\" value=\"\" size=\"20px\" disabled> "+
			"	備註：<input type=\"text\" id=\"Type0_OwnerNotes"+PkgOwnerCount+"\" name=\"OwnerNotes[]\" size=\"25px\" > "+
			"</p>"+
			"</div>"
			)
		$("#Type0_PkgOwnerCount").val(PkgOwnerCount);
	}	
	function func_removeOwner(s)
	{
		//移除html
		var div = $("#div_Pkgowner"+s);
		div.remove();
		//設定數量
		var PkgOwnerCount = $("#Type0_PkgOwnerCount").val();
		PkgOwnerCount = parseInt(PkgOwnerCount) -1;
		$("#Type0_PkgOwnerCount").val(PkgOwnerCount);
		//設定html
		var OwnerDiv = document.getElementsByName("OwnerDiv[]");
		var OwnerLabel = document.getElementsByName("OwnerLabel[]");
		var Owners = document.getElementsByName("Owner[]");
		var OwnerBtn = document.getElementsByName("OwnerBtn[]");
		var OwnerPhone = document.getElementsByName("OwnerPhone[]");
		var OwnerCellphone = document.getElementsByName("OwnerCellphone[]");
		var OwnerPlace = document.getElementsByName("OwnerPlace[]");
		var OwnerNotes = document.getElementsByName("OwnerNotes[]");
		var i;
		for (i = 0; i < Owners.length; i++)
		{
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
	function submit_add()
	{
		var tradeType0 = $("#TradeType0");
		var tradeType1 = $("#TradeType1");
		var Type = "";
		if (tradeType0.is(":checked"))Type = "0";		//進口
		 else if (tradeType1.is(":checked")) Type = "1";	//出口
		//alert("Type="+Type);
		
		var Date = $.trim($("#datepicker").val());	//日期
		var CustomerID = $.trim($("#CustomerID").val());	//客戶
		var Terminal = $.trim($("#Type"+Type+"_Terminal").val());		//貨櫃場
		
		var Owner = document.getElementsByName("Owner[]");				//貨主
		var PkgOwner = new Array();
		for(var i=0;i<Owner.length;i++){
				PkgOwner.push(Owner[i].value);
		}
		
		var OwnerNotes = document.getElementsByName("OwnerNotes[]");	//備註
		var PkgOwnerNotes = new Array();
		for(var i=0;i<OwnerNotes.length;i++){
				PkgOwnerNotes.push(OwnerNotes[i].value);
		}
		//var PkgOwner1 = $.trim($("#Type"+Type+"_PkgOwner1").val());		
		var PkgCount = $.trim($("#Type"+Type+"_PkgCount").val());		//件數
		var Unit = $.trim($("#Type"+Type+"_Unit").val());				//單位
		var Weight = $.trim($("#Type"+Type+"_Weight").val());			//重量
		var Volume = $.trim($("#Type"+Type+"_Volume").val());			//大小
		//var Notes1 = $.trim($("#Type"+Type+"_Notes1").val());			
		var ShipName = $.trim($("#Type"+Type+"_ShipName").val());		//船名/航次
		var SO = $.trim($("#Type"+Type+"_SO").val());					//S/O
		var CompanyID = $.trim($("#Type"+Type+"_CompanyID").val());		//統一編號
		var CloseDate = $.trim($("#Type"+Type+"_CloseDate").val());		//結關日
		
		//alert("CustomerID="+CustomerID+",Terminal="+Terminal+",PkgOwner1="+PkgOwner1+",PkgOwner2="+PkgOwner2+",PkgCount="+PkgCount+",Unit="+Unit+",Weight="+Weight+",Size="+Size);
		
		$.ajax({
			type : "POST", cache : false, dataType : "text",
			url: "/proc_receipt_add.php",
			data: {	func:'add',TradeType:Type,Date:Date,CustomerID:CustomerID,Terminal:Terminal,PkgOwner:PkgOwner,
					PkgCount:PkgCount,Unit:Unit,Weight:Weight,Volume:Volume,Notes:PkgOwnerNotes,
					ShipName:ShipName,SO:SO,CompanyID:CompanyID,CloseDate:CloseDate},
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
	
	function ChangeData()
	{
		var sdate = $("#datepicker").val();
	}
</script>
<body>
	<!--貨主資料-->
	<datalist id="owners">
	</datalist>
	<!--貨櫃場資料-->
	<datalist id="warehouse">
		<option>環球</option>
		<option>東亞</option>
		<option>長邦</option>
		<option>中國</option>
		<option>興隆</option>
		<option>台基</option>
		<option>陽明</option>
		<option>聯興</option>
		<option>中華</option>
		<option>大統</option>
		<option>中央</option>
		<option>弘貿</option>
		<option>永碩</option>
		<option>台揚</option>
		<option>長春</option>
		<option>台聯</option>
		<option>匯連</option>
		<option>健泰</option>
	</datalist>
	
	<p>
		<input type="radio" id="TradeType0" name="TradeType" onclick="func_TypeChange(this)" value="0" checked>進口&nbsp;
		<input type="radio" id="TradeType1" name="TradeType" onclick="func_TypeChange(this)" value="1">出口
	</p>
	<div class="row" style="margin-bottom:20px;">
		<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
			<div class="input-group input-group-sm" style="min-width:200px;">
				<input type="text" id="datepicker" class="form-control" placeholder="請點選日期" style="font-size:16px;height:40px;" >
				<span class="input-group-addon">
					<i class="ace-icon fa fa-calendar"></i>
				</span>
			</div>
		</div>
	</div>
	
	<p>
		客戶：<input type="text" id="CustomerID" onchange="func_showCustomerdata()">&nbsp;
		<input type="text" id="CustomerName" disabled>
	<p>
	<!--進口-->
	<div id="div_Type0" style="display:inline">
		<input type="hidden" id="Type0_PkgOwnerCount" value="1"></input>
		<div id="div_Pkgowners">
			<div id="div_Pkgowner1" name="OwnerDiv[]">
				<p>
					<label id="Type0_OwnerLabel">貨主(到達1)：</label><input type="text" list="owners" id="Type0_PkgOwner1" name="Owner[]" onchange="func_ownerchanged(this, '0','1')">&nbsp;
					<button style="width:30px" id="Type0_OwnerBtn1" name="OwnerBtn[]" onclick="func_addOwner()">+</button>&nbsp;
					貨主電話：<input type="text" id="Type0_OwnerPhone1" name="OwnerPhone[]" size="8px" disabled>&nbsp;
					行動電話：<input type="text" id="Type0_OwnerCellphone1" name="OwnerCellphone[]" size="8px" disabled>&nbsp;
					送貨地點：<input type="text" id="Type0_OwnerPlace1" name="OwnerPlace[]" size="20px" disabled>
					備註：<input type="text" id="Type0_OwnerNotes1" name="OwnerNotes[]" size="25px" >
				</p>
			</div>
		</div>
		<p>起運(貨櫃場)：<input type="text" list="warehouse" id="Type0_Terminal"></p>
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
	</div>
	<!--出口-->
	<div id="div_Type1" style="display:none">
		<p>
			起運(貨主)：<input type="text" list="owners" id="Type1_PkgOwner1" onchange="func_ownerchanged(this, '1','1')">&nbsp;
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
