<?php
	require_once 'db_config.php'; 
	//$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	$conn = new PDO("sqlsrv:Server=$host;Database=$dbname",$username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>	
<script src="/assets/js/jquery.jqGrid.min.js"></script>
<script src="/assets/js/grid.locale-en.js"></script>
<script src="/resource/js/jqgrid.cus.js"></script>
<script>
jQuery(getdata(""));
function SelectData()
{
	var sKeyword = $("#keyword").val();
	jQuery("#grid-table").jqGrid("clearGridData");
	getdata(sKeyword);
	//jQuery("#grid-table").trigger("reloadGrid");
}
function getdata(sKeyword) {
	$.ajax({
		type : "POST", cache : false, dataType : "json",
		url: "/proc_maintain_001.php",
		data: {	func:'query', keyword:sKeyword},
		success: function (data) {	
			//alert(data);
			loadGrid(data);
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});	
}
function loadGrid (g_data) {
	var grid_selector = "#grid-table";
	var pager_selector = "#grid-pager";
	
	var oldData = jQuery(grid_selector).getGridParam("data");
	if (oldData!= null)
	{
		$(grid_selector).jqGrid('setGridParam',{
		datatype:'local',
		data:g_data,
		page:1
		}).trigger("reloadGrid");
		return;
	}
	
	var parent_column = $(grid_selector).closest('[class*="col-"]');
	//resize to fit page size
	$(window).on('resize.jqGrid', function () {
		$(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
    })
	jQuery(grid_selector).jqGrid({
		data: g_data,
		datatype: "local",
		height: 200,
		colNames:['維護', '名稱','電話','手機', '縣市', '地址','統編','報關行','備註'],
		colModel:[
			{name:'mfunc',index:'', width:20, resize:true,
				formatter:'actions', 
				//formatter: buttonFormatter, 
				formatoptions:{ 
					//keys:true,
					//delbutton: false,//disable delete button
					delOptions:{recreateForm: true, beforeShowForm:beforeDeleteCallback},
					editOptions:{recreateForm: true, beforeShowForm:beforeEditCallback}
					//editformbutton:true 
				}
			},
			{name:'Name',index:'Name', width:20, editable: false, key:true},
			{name:'Phone',index:'Phone',width:20, editable:true, editoptions:{size:"10",maxlength:"10"}},
			{name:'CellPhone',index:'CellPhone', width:20, editable: true, editoptions:{size:"10",maxlength:"10"}},
			{name:'Country',index:'Country', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Address',index:'Address', width:50, editable: true, editoptions:{size:"40",maxlength:"50"}},
			{name:'CompanyID',index:'CompanyID', width:20, editable: true, editoptions:{size:"8",maxlength:"8"}},
			{name:'CustomerID',index:'CustomerID', width:20, editable: true, editoptions:{size:"8",maxlength:"8"}},
			{name:'Notes',index:'Notes', width:50, editable: true, editoptions:{size:"40",maxlength:"50"}}
		], 

		viewrecords : true,
		rowNum:5,
		rowList:[5,10,20,30],
		pager : pager_selector,
		altRows: true,
		//toppager: true,
		
		multiselect: true,
		//multikey: "ctrlKey",
        multiboxonly: true,

		loadComplete : function() {
			var table = this;
			setTimeout(function(){
				styleCheckbox(table);
				
				updateActionIcons(table);
				updatePagerIcons(table);
				enableTooltips(table);
			}, 0);
		},

		editurl: "proc_maintain_001.php?func=modify",
		//caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
function submit_add() {
	var Name = $.trim($("#Name").val());
	var Phone = $.trim($("#Phone").val());
	var CellPhone = $.trim($("#CellPhone").val());
	var Country = $.trim($("#Country").val());
	var Address = $.trim($("#Address").val());
	var CompanyID = $.trim($("#CompanyID").val());
	var CustomerID = $.trim($("#CustomerID").val());
	var Notes = $.trim($("#Notes").val());
	if (Name == "") {
		alert("請輸入貨主名稱");
		$("#Name").focus();
		return;
	}
	
	if (Phone == "" && CellPhone == "") {
		alert("請輸入貨主室內電話或手機號碼");
		$("#Phone").focus();
		return;
	}
	
	if (Country == "") {
		alert("請輸入貨主所在縣市");
		$("#Phone").focus();
		return;
	}
	
	if (Address == "") {
		alert("請輸入貨主詳細地址");
		$("#Phone").focus();
		return;
	}
	alert(CustomerID);
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_maintain_001.php",
		data: {func:'add',Name:Name,Phone:Phone,CellPhone:CellPhone,Country:Country,Address:Address,CompanyID:CompanyID,CustomerID:CustomerID,Notes:Notes},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=maintain&type=001";
			}else  {
				alert(data);
			}					
			/*$("#user_account").val("");
			$("#user_password").val("");
			$("#user_id").focus();
			*/
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});		
	
}
</script>

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
<fieldset>
	<legend>新增資料</legend>
 <!--<form method="post">action="page_maintain_001.php"-->
	<table style="text-align:right;">
		<tr>
			<td><p>貨主名稱</p></td>
			<td><p><input type="text" id="Name"></p></td>
			<td><p>報關行</p></td>
			<td><p><input type="text" list="customers" id="CustomerID"></p></td>
		</tr>
		<tr>
			<td><p>室內電話</p></td>
			<td><p><input type="text" id="Phone"></p></td>
			<td><p>手機號碼</p></td>
			<td><p><input type="text" id="CellPhone"></p></td>
		</tr>
		<tr>
			<td><p>縣市</p></td>
			<td><p><input type="text" id="Country"></p></td>
			<td><p>聯絡地址</p></td>
			<td><p><input type="text" id="Address"></p></td>
		</tr>
		<tr>
			<td><p>統一編號</p></td>
			<td><p><input type="text" id="CompanyID"></p></td>
			<td colspan="2" rowspan="2" style="text-align:center;">
				<button class="btn btn-app btn-grey btn-xs radius-4" onclick="submit_add();">
					<i class="ace-icon fa fa-floppy-o bigger-160"></i>
					Save
					<span class="badge badge-transparent">
						<i class="light-red ace-icon fa fa-asterisk"></i>
					</span>
				</button>
			</td>
		</tr>
		<tr>
			<td><p>備註</p>
			<td><p><input type="text" id="Notes"></p></td>
		</tr>
		</p>
	</table>
	<!--<button type="button" class="width-10 pull-left btn btn-sm btn-primary" onclick="submit_add();">
		<i class="ace-icon fa fa-key"></i>
		<span class="bigger-110">新增資料</span>
	</button>
	-->
 <!--</form>-->
</fieldset>
<p></p>
<fieldset>
	<legend>維護資料</legend>
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin-bottom:20px;">
				<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
					<div class="input-group input-group-sm" style="min-width:200px;">
						<input type="text" id="keyword" class="form-control" placeholder="輸入關鍵字" style="font-size:16px;height:40px;">
						<span class="input-group-addon">
							<i class="ace-icon fa fa-pencil-square-o"></i>
						</span>
						<button type="button" class="width-100 pull-right btn btn-sm btn-primary" style="margin-left:10px;height:40px;" onclick="SelectData();">
							<span class="bigger-110">查詢</span>
						</button>
					</div>
				</div>
			</div>
			
			<table id="grid-table"></table>
			<div id="grid-pager"></div>
		</div>
	</div>
</fieldset>
