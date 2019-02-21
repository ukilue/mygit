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
		url: "/proc_maintain_004.php",
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
		colNames:['維護', '帳號','密碼','權限'],
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
			{name:'account',index:'account',width:20, editable: false, key:true},
			{name:'password',index:'password', width:20, editable: true, editoptions:{size:"10",maxlength:"10"}},
			{name:'permission',index:'permission', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}}
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

		editurl: "proc_maintain_004.php?func=modify",
		//caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
function submit_add() {
	var account = $.trim($("#account").val());
	var password = $.trim($("#password").val());
	var permission = $.trim($("#permission").val());
	if (account == "") {
		alert("請輸入帳號");
		$("#account").focus();
		return;
	}
	if (password == "") {
		alert("請輸入密碼");
		$("#password").focus();
		return;
	}
	if (permission == "") {
		alert("請選擇權限值");
		$("#permission").focus();
		return;
	}
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_maintain_004.php",
		data: {func:'add',account:account,password:password,permission:permission},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=maintain&type=004";
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
 <!--<form method="post">action="page_maintain_001.php"-->
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">帳號</label>
		<div class="col-sm-1">
			<input type="text" id="account" class="form-control" placeholder="帳號" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">密碼</label>
		<div class="col-sm-1">
			<input type="text" id="password" class="form-control" placeholder="密碼" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-right" style="width:70px">權限</label>
		<div class="col-sm-1">
			<select class="form-control" id="permission">
				<option value="2">客戶</option>
				<option value="3">內部員工</option>
				<option value="4">管理者</option>
			</select>
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
