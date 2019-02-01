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
		url: "/proc_maintain_003.php",
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
		colNames:['維護', '代號','名稱','負責人','Email','室內電話','手機電話','傳真號碼','聯絡地址','備註'],
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
			{name:'ID',index:'ID',width:20, editable: false, key:true},
			{name:'Name',index:'Name', width:20, editable: true, editoptions:{size:"10",maxlength:"10"}},
			{name:'Principal',index:'Principal', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Email',index:'Email', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Phone',index:'Phone', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'CellPhone',index:'CellPhone', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Fax',index:'Fax', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Address',index:'Address', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}},
			{name:'Notes',index:'Notes', width:20, editable: true, editoptions:{size:"10",maxlength:"15"}}
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

		editurl: "proc_maintain_003.php?func=modify",
		//caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
function submit_add() {
	var ID = $.trim($("#ID").val());
	var Name = $.trim($("#Name").val());
	var Principal = $.trim($("#Principal").val());
	var Email = $.trim($("#Email").val());
	var Phone = $.trim($("#Phone").val());
	var CellPhone = $.trim($("#CellPhone").val());
	var Fax = $.trim($("#Fax").val());
	var Address = $.trim($("#Address").val());
	var Notes = $.trim($("#Notes").val());
	if (ID == "") {
		alert("請輸入代號");
		$("#ID").focus();
		return;
	}
	if (Name == "") {
		alert("請輸入名稱");
		$("#Name").focus();
		return;
	}
	if (Phone == "" || CellPhone =="") {
		alert("請輸入室內電話或手機號碼");
		$("#Phone").focus();
		return;
	}
	
	$.ajax({
		type : "POST", cache : false, dataType : "text",
		url: "/proc_maintain_003.php",
		data: {func:'add',ID:ID,Name:Name,Principal:Principal,Email:Email,Phone:Phone,CellPhone:CellPhone,Fax:Fax,Address:Address,Notes:Notes},
		success: function (data) {						
			if(data=='0') {
				window.location = "/index.php?action=maintain&type=003";
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
<fieldset>
	<legend>新增資料</legend>
 <!--<form method="post">action="page_maintain_001.php"-->
	<p>代　　號 <input type="text" placeholder="代號" id="ID"></p>
	<p>名　　稱 <input type="text" placeholder="名稱" id="Name"></p>
	<p>負 責 人 <input type="text" placeholder="負責人" id="Principal"></p>
	<p>Email　　<input type="text" placeholder="Email" id="Email"></p>
	<p>室內電話 <input type="text" placeholder="室內電話" id="Phone"></p>
	<p>手機電話 <input type="text" placeholder="手機電話" id="CellPhone"></p>
	<p>傳真號碼 <input type="text" placeholder="傳真號碼" id="Fax"></p>
	<p>聯絡地址 <input type="text" placeholder="聯絡地址" id="Address"></p>
	<p>備　　註 <input type="text" placeholder="備註" id="Notes"></p>
	<button class="btn btn-app btn-grey btn-xs radius-4" onclick="submit_add();">
		<i class="ace-icon fa fa-floppy-o bigger-160"></i>
		Save
		<span class="badge badge-transparent">
			<i class="light-red ace-icon fa fa-asterisk"></i>
		</span>
	</button>
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
