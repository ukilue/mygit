<script src="/assets/js/jquery.jqGrid.min.js"></script>
<script src="/assets/js/grid.locale-en.js"></script>
<script src="/resource/js/jqgrid.cus.js"></script>

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
	//$( "#datepicker" ).zIndex(500);
	var today = new Date();
	var day = today.getDate();
	if (day <10) day = "0"+day;
	var month = today.getMonth()+1;
	if (month <10) month = "0"+month;
	var year = today.getFullYear();
	$( "#datepicker" ).val(year+"/"+month+"/"+day);
	
	$( "#StartDate" ).datepicker({
		showOtherMonths: true,
		selectOtherMonths: false,
		dateFormat: 'yy/mm/dd',
	});	
	//$( "#StartDate" ).zIndex(600);
	$( "#EndDate" ).datepicker({
		showOtherMonths: true,
		selectOtherMonths: false,
		dateFormat: 'yy/mm/dd',
	});	
	//$( "#EndDate" ).zIndex(700);
	$( "#toprow" ).zIndex(1000);
});

jQuery(getdata("","",""));
function SelectData()
{
	var CID = $("#CID").val();
	var sDate = $("#StartDate").val();
	var eDate = $("#EndDate").val();
	jQuery("#grid-table").jqGrid("clearGridData");
	getdata(CID,sDate,eDate);
	//jQuery("#grid-table").trigger("reloadGrid");
}
function getdata(CID,sDate,eDate) {
	$.ajax({
		type : "POST", cache : false, dataType : "json",
		url: "/proc_maintain_005.php",
		data: {	func:'query', CustomerID:CID, StartDate:sDate, EndDate:eDate},
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
		colNames:['維護','代號','日期','客戶', '貨主', '貨櫃場','金額'],
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
			{name:'GUID',index:'GUID', width:20, editable: true, key:true, hidden:true},
			{name:'Date',index:'Date', width:20, editable: true, key:true},
			{name:'CustomerID',index:'CustomerID',width:20, editable:true, editoptions:{size:"10",maxlength:"10"}},
			{name:'PkgOwner',index:'PkgOwner', width:20, editable: true, editoptions:{size:"10",maxlength:"10"}},
			{name:'Terminal',index:'Terminal', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Price',index:'Price', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}}
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

		editurl: "proc_maintain_005.php?func=modify",
		//caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
function submit_add() {
	var Date = $.trim($("#datepicker").val());
	var CustomerID = $.trim($("#CustomerID").val());
	var PkgOwner = $.trim($("#PkgOwner").val());
	var Terminal = $.trim($("#Terminal").val());
	var Price = $.trim($("#Price").val());
	if (Date == "") {
		alert("請輸入日期");
		$("#datepicker").focus();
		return;
	}
	if (CustomerID == "") {
		alert("請輸入客戶");
		$("#CustomerID").focus();
		return;
	}
	if (PkgOwner == "") {
		alert("請輸入貨主");
		$("#PkgOwner").focus();
		return;
	}
	if (Terminal == "") {
		alert("請輸入櫃場");
		$("#Terminal").focus();
		return;
	}	
	if (Price == "") {
		alert("請輸入金額");
		$("#Price").focus();
		return;
	}
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_maintain_005.php",
		data: {func:'add',Date:Date,CustomerID:CustomerID,PkgOwner:PkgOwner,Terminal:Terminal,Price:Price},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=maintain&type=005";
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
function queryCustomer()
{
	var ID = $("#CustomerID").val();
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_global.php",
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
		url: "/proc_global.php",
		data: {	func:'QueryOwnerByCustomerID',ID:ID},
		success: function (data) {
			document.getElementById("owners").innerHTML=data;
		},
		error: function () {
			alert("系統異常, 請稍候再試");
		}		
	});	
}

</script>

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

<!--客戶資料-->
<datalist id="owners">
</datalist>

<h3 class="header smaller lighter green">新增資料</h3>
 <!--<form method="post">action="page_maintain_001.php"-->
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right">日期</label>
		<div class="col-sm-1">
			
			<input type="text" id="datepicker" class="form-control" placeholder="請點選日期" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right">客戶</label>
		<div class="col-sm-1">
			<input type="text" placeholder="請輸入客戶ID" id="CustomerID" onchange="queryCustomer()" class="form-control"> 
		</div>
		<div class="col-sm-1">
			<input type="text" id="CustomerName" disabled >
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right">貨主</label>
		<div class="col-sm-1">
			<input type="text" list="owners" placeholder="請輸入貨主" id="PkgOwner" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right">貨櫃場</label>
		<div class="col-sm-1">
			<input type="text" list="warehouse" placeholder="請輸入櫃場" id="Terminal" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right">金額</label>
		<div class="col-sm-1">
			<input type="text" placeholder="請輸入金額" id="Price" class="form-control">
		</div>
	</div>
</form>

<button class="btn btn-lg btn-success" onclick="submit_add();">新增</button>
<p></p>
<h3 class="header smaller lighter blue">維護資料</h3>
<div class="container">
	<div class="row" id="toprow">
		<div class="col-sm-2">
			<label>客戶</label><input type="text" id="CID"  placeholder="請輸入客戶ID">
		</div>
		<div class="col-sm-3">
			<label>起始日期</label><input type="text" id="StartDate" placeholder="請選擇起始日期">
		</div>
		<div class="col-sm-3">
			<label>結束日期</label><input type="text" id="EndDate" placeholder="請選擇結束日期">
		</div>
		<div class="col-sm-1">
			<button type="button" class="width-100 pull-right btn btn-sm btn-primary" onclick="SelectData();">
				<span class="bigger-110">查詢</span>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<table id="grid-table"></table>
			<div id="grid-pager"></div>
		</div>
	</div>
</div>
