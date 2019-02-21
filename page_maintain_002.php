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
		url: "/proc_maintain_002.php",
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
		colNames:['維護','原代號', '代號','名稱','身份證', '電話1', '電話2','聯絡地址'],
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
			{name:'OldGUID',index:'OldGUID', width:20, editable: true, key:true, hidden:true},
			{name:'GUID',index:'GUID', width:20, editable: true, key:true},
			{name:'Name',index:'Name',width:20, editable:true, editoptions:{size:"10",maxlength:"10"}},
			{name:'ID',index:'ID', width:20, editable: true, editoptions:{size:"10",maxlength:"10"}},
			{name:'Phone1',index:'Phone1', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Phone2',index:'Phone2', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Address',index:'Address', width:50, editable: true, editoptions:{size:"20",maxlength:"30"}}
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

		editurl: "proc_maintain_002.php?func=modify",
		//caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
function submit_add() {
	var GUID = $.trim($("#GUID").val());
	var Name = $.trim($("#Name").val());
	var ID = $.trim($("#ID").val());
	var Phone1 = $.trim($("#Phone1").val());
	var Phone2 = $.trim($("#Phone2").val());
	var Address = $.trim($("#Address").val());
	if (GUID == "") {
		alert("請輸入代號");
		$("#GUID").focus();
		return;
	}
	if (Name == "") {
		alert("請輸入名稱");
		$("#Name").focus();
		return;
	}
	if (ID == "") {
		alert("請輸入身份證");
		$("#ID").focus();
		return;
	}	
	if (Phone1 == "" && Phone2 == "") {
		alert("請輸入電話");
		$("#Phone1").focus();
		return;
	}
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_maintain_002.php",
		data: {func:'add',GUID:GUID,Name:Name,ID:ID,Phone1:Phone1,Phone2:Phone2,Address:Address},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=maintain&type=002";
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
<h3 class="header smaller lighter green">新增資料</h3>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">代號</label>
		<div class="col-sm-1">
			<input type="text" id="GUID" class="form-control" placeholder="代號" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">名稱</label>
		<div class="col-sm-1">
			<input type="text" id="Name" class="form-control" placeholder="名稱" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">身份證</label>
		<div class="col-sm-1">
			<input type="text" id="ID" class="form-control" placeholder="身份證" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">電話1</label>
		<div class="col-sm-1">
			<input type="text" id="Phone1" class="form-control" placeholder="電話1" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">電話2</label>
		<div class="col-sm-1">
			<input type="text" id="Phone2" class="form-control" placeholder="電話2" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">聯絡地址</label>
		<div class="col-sm-3">
			<input type="text" id="Address" class="form-control" placeholder="聯絡地址" class="form-control">
		</div>
	</div>
</form>
<button class="btn btn-lg btn-success" onclick="submit_add();">新增</button>
<p></p>
<h3 class="header smaller lighter blue">維護資料</h3>
<div class="row">
	<div class="col-xs-12">
		<div class="row" style="margin-bottom:20px;">
			<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
				<div class="input-group input-group-sm" style="min-width:200px;">
					<input type="text" id="keyword" class="form-control" placeholder="請輸入司機名稱" style="font-size:16px;height:40px;">
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
