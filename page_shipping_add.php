<!-- 日期 -->
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>

<script type="text/javascript">
$.ajax({
	type : "POST", cache : false, dataType : "json",
	url: "/proc_shipping_add.php",
	data: {	func:'query'},
	success: DataLoaded,
	error: function (e) {
		alert("系統異常, 請稍候再試!");
	}		
});

jQuery(function($) {			
	$( "#DeliveryDate" ).datepicker({
		showOtherMonths: true,
		selectOtherMonths: false,
		dateFormat: 'yy/mm/dd',
	});	
	$( "#DeliveryDate" ).zIndex(1000);
});
function ChangeData()
{
	var sdate = $("#DeliveryDate").val();
}
function DataLoaded(data)
{
	if (data=='')
	{
		//alert("無下一筆資料需編輯，請自由新增");
		$("#AddOwner").attr('style', 'display:inline');
		//日期
		var today = new Date();
		var day = today.getDate();
		if (day <10) day = "0"+day;
		var month = today.getMonth()+1;
		if (month <10) month = "0"+month;
		var year = today.getFullYear();
		$( "#DeliveryDate" ).val(year+"/"+month+"/"+day);
		//terminal
		$( "#Terminal" ).attr("list","warehouse");
		//預設新增一筆貨主
		AddOwner('');
		return;
	}
	var TradeType = data[0]['TradeType'];	//同一編號送貨單應為同一類型(出口或進口)
	
	//共通資料
	$("#DeliveryDate").val(data[0]['Date']);
	$("#CustomerID").val(data[0]['CustomerID']);
	$("#CustomerName").val(data[0]['CustomerName']);
	$("#PkgCount").val(data[0]['PkgCount']);
	$("#Unit").val(data[0]['Unit']);
	$("#Weight").val(data[0]['Weight']);
	$("#Volume").val(data[0]['Volume']);
	var input_TradeType = $("#TradeType");
	input_TradeType.val(TradeType);
	input_TradeType.select();
	
	
	//outerDIV
	//var outerdiv = $("#div_Type_Owner");
	
	var i;
	for (i = 0; i < data.length; i++)
	{
		AddOwner(data[i]);
		/*
		$str = "貨主(到達"+(i+1)+")：";
		if (TradeType == '1') $str = "貨主(起運)：";
		outerdiv.append(
			"<p>" +
			"	<input type=\"text\" id=\"Type_DeliveryGUID"+(i+1)+"\" name=\"DeliveryGUID[]\" value=\""+data[i]['GUID']+"\" >&nbsp; " +
			"	<label name=\"OwnerLabel[]\">"+$str+"</label> "+
			"	<input type=\"text\" id=\"Type_PkgOwner"+(i+1)+"\" name=\"Owner[]\" size=\"10px\" value=\""+data[i]['PkgOwner']+"\" >&nbsp; " +
			"	<label>送貨地點：</label> "+
			"	<input type=\"text\" id=\"Type_Country"+(i+1)+"\" name=\"OwnerPlace[]\" size=\"5px\" value=\""+data[i]['Country']+"\" >&nbsp; "+
			"	<label>備註：</label> "+
			"	<input type=\"text\" id=\"Type_Notes"+(i+1)+"\" name=\"OwnerNotes[]\" size=\"20px\" value=\""+data[i]['Note']+"\" >&nbsp; "+
			"	<button type=\"button\" name=\"BtnAddDriver[]\" onclick=\"AddDriver('"+(i+1)+"')\" >新增司機</button> "+
			"</p>"+
			"<div id=\"div_Driver"+(i+1)+"\"></div>"
		);
		*/
	}
	
	$("#Terminal").val(data[0]['Terminal']);
}
function AddOwner(data)
{
	var Owners = document.getElementsByName("Owner[]");
	var OwnerCount = Owners.length + 1;
	var TradeType = $("#TradeType").val();
	$str = "貨主(到達"+OwnerCount+")：";
	if (TradeType == '1') $str = "貨主(起運)：";
	
	var GUID = data['GUID'];
	var PkgOwner = data['PkgOwner'];
	var Country = data['Country'];
	var Note = data['Note'];
	if (GUID == null) GUID = '-1';
	if (PkgOwner == null) PkgOwner = '';
	if (Country == null) Country = '';
	if (Note == null) Note = '';
	
	var outerdiv = $("#div_Type_Owner");
	outerdiv.append(
		"<p>" +
		"	<input type=\"hidden\" id=\"Type_DeliveryGUID"+OwnerCount+"\" name=\"DeliveryGUID[]\" value=\""+GUID+"\" disabled>&nbsp; " +
		"	<label name=\"OwnerLabel[]\">"+$str+"</label> "+
		"	<input type=\"text\" list=\"owners\" id=\"Type_PkgOwner"+OwnerCount+"\" name=\"Owner[]\" size=\"10px\" value=\""+PkgOwner+"\" onchange=\"ownerChanged('"+OwnerCount+"')\">&nbsp; " +
		"	<label>送貨地點：</label> "+
		"	<input type=\"text\" id=\"Type_Country"+OwnerCount+"\" name=\"OwnerPlace[]\" size=\"5px\" value=\""+Country+"\" >&nbsp; "+
		"	<label>備註：</label> "+
		"	<input type=\"text\" id=\"Type_Notes"+OwnerCount+"\" name=\"OwnerNotes[]\" size=\"20px\" value=\""+Note+"\" >&nbsp; "+
		"	<button type=\"button\" name=\"BtnAddDriver[]\" onclick=\"AddDriver('"+OwnerCount+"')\" >新增司機</button> "+
		"</p>"+
		"<div id=\"div_Driver"+OwnerCount+"\"></div>"
	);
}
function AddDriver(pkgnum)
{
	var outerdiv = $("#div_Driver"+pkgnum);
	var Drivers = document.getElementsByName("Driver"+pkgnum+"[]");
	var DriverNum = Drivers.length + 1;
	outerdiv.append(
		"<div id=\"Owner"+pkgnum+"_Driver"+DriverNum+"\" name=\"DivDriver"+pkgnum+"[]\" >"+
		"	<p>" +
		"		<button type=\"button\" name=\"BtnDelDriver"+pkgnum+"[]\" onclick=\"DelDriver('"+pkgnum+"','"+DriverNum+"')\" >刪除</button> " +
		"		<label name=\"DriverLabel"+pkgnum+"[]\">司機"+DriverNum+"：</label> "+
		"		<input type=\"text\" id=\"Type_Driver"+pkgnum+"_"+DriverNum+"\" name=\"Driver"+pkgnum+"[]\" onchange=\"DriverChanged('"+pkgnum+"','"+DriverNum+"');\" size=\"5px\">&nbsp; "+
		"		<input type=\"text\" id=\"Type_DriverName"+pkgnum+"_"+DriverNum+"\" name=\"DriverName"+pkgnum+"[]\"  size=\"10px\" disabled > "+
		"		<label name=\"CarLabel"+pkgnum+"[]\">車種"+DriverNum+"：</label> "+
		//"		<input type=\"text\" id=\"Type_CarType"+pkgnum+"_"+DriverNum+"\" name=\"CarType"+pkgnum+"[]\" onchange=\"CarTypeChanged('0','"+pkgnum+"','"+DriverNum+"');\" size=\"5px\">&nbsp; "+
		"		<select id=\"Type_CarType"+pkgnum+"_"+DriverNum+"\" name=\"CarType"+pkgnum+"[]\" onchange=\"CarTypeChanged('"+pkgnum+"','"+DriverNum+"');\"> "+
		"			<option value=\"0\">請選擇</option> "+
		"			<option value=\"0.5\">0.5</option> "+
		"			<option value=\"1.5\">1.5</option> "+
		"			<option value=\"3.5\">3.5</option> "+
		"			<option value=\"4.5\">4.5</option> "+
		"			<option value=\"8.8\">8.8</option> "+
		"			<option value=\"12\">12</option> "+
		"			<option value=\"15\">15</option> "+
		"		</select> "+
		"		<label name=\"PriceLabel"+pkgnum+"[]\">金額"+DriverNum+"：</label> "+
		"		<input type=\"text\" id=\"Type_Price"+pkgnum+"_"+DriverNum+"\" name=\"Price"+pkgnum+"[]\" size=\"5px\">&nbsp; "+
		"		<label name=\"esPriceLabel"+pkgnum+"[]\">運費表報價"+DriverNum+"：</label> "+
		"		<input type=\"text\" id=\"Type_esPrice"+pkgnum+"_"+DriverNum+"\" name=\"esPrice"+pkgnum+"[]\" size=\"10px\" disabled > "+
		"	</p>"+
		"	<label name=\"lastPriceLabel"+pkgnum+"[]\">※貨主近五筆同車種資料(選擇車種後顯示)"+DriverNum+"：</label> "+
		"	<div id=\"Owner"+pkgnum+"_lastTransport"+DriverNum+"\" name=\"DivlastTransport"+pkgnum+"[]\"></div> "+
		"</div>"
	);
}
function DelDriver(pkgnum,DriverNum)
{
	var outerdiv = $("#div_Driver"+pkgnum);
	var driverDiv = $("#Owner"+pkgnum+"_Driver"+DriverNum);
	driverDiv.remove();
	//重新設定html
	var Drivers = document.getElementsByName("Driver"+pkgnum+"[]");
	//div
	var Div_Driver = document.getElementsByName("DivDriver"+pkgnum+"[]");
	var Div_Transport = document.getElementsByName("DivlastTransport"+pkgnum+"[]");
	//button
	var Button_DelDriver = document.getElementsByName("BtnDelDriver"+pkgnum+"[]");
	//label
	var Label_Driver = document.getElementsByName("DriverLabel"+pkgnum+"[]");
	var Label_Car = document.getElementsByName("CarLabel"+pkgnum+"[]");
	var Label_Price = document.getElementsByName("PriceLabel"+pkgnum+"[]");
	var Label_esPrice = document.getElementsByName("esPriceLabel"+pkgnum+"[]");
	var Label_lastPrice = document.getElementsByName("lastPriceLabel"+pkgnum+"[]");
	//input
	var input_Driver = document.getElementsByName("Driver"+pkgnum+"[]");
	var input_DriverName = document.getElementsByName("DriverName"+pkgnum+"[]");
	var input_CarType = document.getElementsByName("CarType"+pkgnum+"[]");
	var input_Price = document.getElementsByName("Price"+pkgnum+"[]");
	var input_esPrice = document.getElementsByName("esPrice"+pkgnum+"[]");
	var i;
	for (i = 0; i < Drivers.length; i++)
	{
		
		Div_Driver[i].setAttribute("id", "Owner"+pkgnum+"_Driver"+(i+1));
		Div_Transport[i].setAttribute("id", "Owner"+pkgnum+"_lastTransport"+(i+1));
		
		Button_DelDriver[i].setAttribute("onclick", "DelDriver('"+pkgnum+"','"+(i+1)+"')");
		
		Label_Driver[i].innerHTML = "司機"+(i+1)+"：";
		Label_Car[i].innerHTML = "車種"+(i+1)+"：";
		Label_Price[i].innerHTML = "金額"+(i+1)+"：";
		Label_esPrice[i].innerHTML = "運費表報價"+(i+1)+"：";
		Label_lastPrice[i].innerHTML = "※貨主近五筆同車種資料(選擇車種後顯示)"+(i+1)+"：";
		
		input_Driver[i].setAttribute("id", "Type_Driver"+pkgnum+"_"+(i+1));
		input_Driver[i].setAttribute("onchange", "DriverChanged('"+pkgnum+"','"+(i+1)+"');");
		input_DriverName[i].setAttribute("id", "Type_DriverName"+pkgnum+"_"+(i+1));
		input_CarType[i].setAttribute("id", "Type_CarType"+pkgnum+"_"+(i+1));
		input_CarType[i].setAttribute("onchange", "CarTypeChanged('"+pkgnum+"','"+(i+1)+"');");
		input_Price[i].setAttribute("id", "Type_Price"+pkgnum+"_"+(i+1));
		input_esPrice[i].setAttribute("id", "Type_esPrice"+pkgnum+"_"+(i+1));
	}
}
function ownerChanged(pkgnum)
{
	var Owner = $("#Type_PkgOwner"+pkgnum);
	var OwnerCountry = $("#Type_Country"+pkgnum);
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_shipping_add.php",
		data: {	func:'queryOwnerCountry',Name:Owner.val()},
		success: function (data) {
			OwnerCountry.val(data);
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});
}
function CustomerIDChanged()
{
	var CustomerID = $("#CustomerID").val();
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_shipping_add.php",
		data: {	func:'queryCustomer',ID:CustomerID},
		success: function (data) {
			$("#CustomerName").val(data);
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});

	document.getElementById("owners").innerHTML="";
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_receipt_add.php",
		data: {	func:'AJAXOwner',ID:CustomerID},
		success: function (data) {
			document.getElementById("owners").innerHTML=data;
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});		
}
function DriverChanged(pkgnum, DriverNum)
{
	var input_Driver = $("#Type_Driver"+pkgnum+"_"+DriverNum);
	var input_DriverName = $("#Type_DriverName"+pkgnum+"_"+DriverNum);
	//alert(input_Country.val()+" " +input_CarType.val());
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_shipping_add.php",
		data: {	func:'queryDriver',Driver:input_Driver.val()},
		success: function (DriverName) {
			if (DriverName=="") input_DriverName.val("查無資料");
			 else input_DriverName.val(DriverName);
		},
		error: function (e) {
			input_esPrice.val("查無資料或車種錯誤");
		}		
	});
}
function CarTypeChanged(pkgnum, DriverNum)
{
	var input_PkgOwner = $("#Type_PkgOwner"+pkgnum);
	var input_Country = $("#Type_Country"+pkgnum);
	var input_CarType = $("#Type_CarType"+pkgnum+"_"+DriverNum);
	var input_esPrice = $("#Type_esPrice"+pkgnum+"_"+DriverNum);
	var div_lastTransport = $("#Owner"+pkgnum+"_lastTransport"+DriverNum);
	//alert(input_Country.val()+" " +input_CarType.val());
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_shipping_add.php",
		data: {	func:'queryFee',Country:input_Country.val(),CarType:input_CarType.val()},
		success: function (fee) {
			if (fee=="" || fee=="0") input_esPrice.val("查無資料");
			 else input_esPrice.val(fee);
		},
		error: function (e) {
			input_esPrice.val("查無資料");
		}		
	});
	
	$.ajax({
		type : "POST", cache : false, dataType : "json",
		url: "/proc_shipping_add.php",
		data: {	func:'querylastData',PkgOwner:input_PkgOwner.val(),CarType:input_CarType.val()},
		success: function (data) {
			if (data.length < 1)
			{
				div_lastTransport.html("查無資料");
			}
			else
			{
				var innerhtml = 
					"<table class=\"table table-striped table-bordered\" >" + 
					"	<tr>" +
					"		<td>日期</td>" +
					"		<td>載運人</td>" +
					"		<td>車種</td>" +
					"		<td>起運</td>" +
					"		<td>送達</td>" +
					"		<td>件數</td>" +
					"		<td>重量</td>" +
					"		<td>材積</td>" +
					"		<td>金額</td>" +
					"		<td>備註</td>" + 
					"	</tr>";
				
				var i;
				for (i = 0; i < data.length; i++)
				{
					innerhtml = innerhtml +
					"	<tr>" +
					"		<td>"+data[i]['Date']+"</td>" +
					"		<td>"+data[i]['Name']+"</td>" +
					"		<td>"+data[i]['CarType']+"</td>" +
					"		<td>"+data[i]['StartPlace']+"</td>" +
					"		<td>"+data[i]['SendPlace']+"</td>" +
					"		<td>"+data[i]['PkgCount']+"</td>" +
					"		<td>"+data[i]['Weight']+"</td>" +
					"		<td>"+data[i]['Volume']+"</td>" +
					"		<td>"+data[i]['Price']+"</td>" +
					"		<td>"+data[i]['Notes']+"</td>" + 
					"	</tr>";
				}
				innerhtml = innerhtml+
					"	<tr>" +
					"</table>";
				div_lastTransport.html(innerhtml);
			}
		},
		error: function (e) {
			//input_esPrice.val("查無資料");
		}		
	});
}
function TradeTypeChanged()
{
	var TradeType = $("#TradeType").val();
	if (TradeType=='0')
	{
		$("#label_Terminal").html("貨櫃場(起運)：");
	}
	else if (TradeType=='1')
	{
		$("#label_Terminal").html("貨櫃場(到達)：");
	}
}
function submit_save()
{
	var TradeType = $("#TradeType").val();					//載運類型
	var DeliveryDate = $.trim($("#DeliveryDate").val());	//日期
	var CustomerID = $.trim($("#CustomerID").val());		//客戶
	var PkgCount = $.trim($("#PkgCount").val());			//件數
	var Unit = $.trim($("#Unit").val());					//單位
	var Weight = $.trim($("#Weight").val());				//重量
	var Volume = $.trim($("#Volume").val());				//材積
	var Terminal = $.trim($("#Terminal").val());			//貨櫃場
	
	var Owners = document.getElementsByName("Owner[]");
	var OwnerPlace = document.getElementsByName("OwnerPlace[]");
	var OwnerNotes = document.getElementsByName("OwnerNotes[]");
	var DeliveryGUID = document.getElementsByName("DeliveryGUID[]");
	var i, j;
	for (i = 0; i < Owners.length; i++)
	{
		var Drivers = document.getElementsByName("Driver"+(i+1)+"[]");
		var CarType = document.getElementsByName("CarType"+(i+1)+"[]");
		var Price = document.getElementsByName("Price"+(i+1)+"[]");
		var esPrice = document.getElementsByName("esPrice"+(i+1)+"[]");
		
		var ary_Drivers = new Array();
		var ary_CarType = new Array();
		var ary_Price = new Array();
		var ary_esPrice = new Array();
		
		for (j = 0; j < Drivers.length; j++)
		{
			ary_Drivers.push(Drivers[j].value);
			ary_CarType.push(CarType[j].value);
			ary_Price.push(Price[j].value);
			ary_esPrice.push(esPrice[j].value);
		}
		
	
		$.ajax({
			type : "POST", cache : false, dataType : "text",
			url: "/proc_shipping_add.php",
			data: {	func:'add',TradeType:TradeType,DeliveryDate:DeliveryDate,CustomerID:CustomerID,DeliveryGUID:DeliveryGUID[i].value,PkgCount:PkgCount,Unit:Unit,Weight:Weight,
					Volume:Volume,Terminal:Terminal,PkgOwner:Owners[i].value,Country:OwnerPlace[i].value,Notes:OwnerNotes[i].value,
					Driver:ary_Drivers,CarType:ary_CarType,Price:ary_Price,esPrice:ary_esPrice},
			success: function (data) {
				//Console.log(data);
			},
			error: function () {
				alert("系統異常, 請稍候再試");
			}		
		});	
	}
	window.location = "/index.php?action=shipping&type=add";
	
	//alert("CustomerID="+CustomerID+",Terminal="+Terminal+",PkgOwner1="+PkgOwner1+",PkgOwner2="+PkgOwner2+",PkgCount="+PkgCount+",Unit="+Unit+",Weight="+Weight+",Size="+Size);
		
}
</script>

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
<form class="form-inline" role="form">
	<div class="form-group" style="margin-bottom:20px;">
		<div class="col-sm-6 col-md-2 col-lg-2">
			<div class="input-group input-group-sm" style="min-width:200px;">
				<input type="text" id="DeliveryDate" class="form-control" placeholder="請點選日期" style="font-size:16px;height:40px;" >
				<span class="input-group-addon">
					<i class="ace-icon fa fa-calendar"></i>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group" style="margin-bottom:20px;">
		載運類型：
		<select id="TradeType"  onchange="TradeTypeChanged();" >
			<option value="0">進口</option>
			<option value="1">出口</option>
		</select>
	</div>
</form>
<p>
	<!--日期：<input type="text" id="DeliveryDate" size="10px" >&nbsp;-->
	客戶：<input type="text" id="CustomerID" onchange="CustomerIDChanged();"  size="5px" >&nbsp;
	客戶名稱：<input type="text" id="CustomerName" size="8px" disabled>
</p>
	<p>
		件數：<input type="text" id="PkgCount" size="3px" >&nbsp;
		單位：
		<!--<input type="text" id="Unit" size="3px" >&nbsp;-->
		
			<select id="Unit">
				<option value="C/T">C/T</option>
				<option value="C/S">C/S</option>
				<option value="D/M">D/M</option>
				<option value="P/L">P/L</option>
				<option value="P/L">P/L</option>
				<option value="BOX">BOX</option>
			</select>&nbsp;
		重量(kg)：<input type="text" id="Weight" size="3px" >&nbsp;
		材積：<input type="text" id="Volume" size="3px" >
	</p>
	<p>
		<label id="label_Terminal" size="5px">貨櫃場(起運)：</label>
		<input type="text" id="Terminal" size="8px" >
	</p>
	<button type="button" id="AddOwner" onclick="AddOwner('')" style="display:none">新增貨主</button>
	<!--進口-->
	<div id="div_Type" style="display:inline">
		<div id="div_Type_Owner">
		</div>
	</div>
	<!--出口-->
	<!--
	<div id="div_Type1" style="display:none">
		<div id="div_Type1_Owner">
		</div>
	</div>
	-->
<p></p>
<button type="button" id="btn_save" onclick="submit_save();">儲存資料&編輯次筆</button>