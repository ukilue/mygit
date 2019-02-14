	
<script src="/assets/js/jquery.jqGrid.min.js"></script>
<script src="/assets/js/grid.locale-en.js"></script>
<script src="/resource/js/jqgrid.cus.js"></script>

<!-- 日期 -->
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>
		
<script type="text/javascript">
//var gdata;
jQuery(getdata(""));
jQuery(function($) {			
	$( "#datepicker" ).datepicker({
		showOtherMonths: true,
		selectOtherMonths: false,
		dateFormat: 'yy/mm/dd',
	});	
	$( "#datepicker" ).zIndex(1000);
	var today = new Date();
	var day = today.getDate();
	if (day <10) day = "0"+day;
	var month = today.getMonth()+1;
	if (month <10) month = "0"+month;
	var year = today.getFullYear();
	$( "#datepicker" ).val(year+"/"+month+"/"+day);
});
function ChangeData()
{
	var sdate = $("#datepicker").val();
	jQuery("#grid-table").jqGrid("clearGridData");
	getdata(sdate);
	//jQuery("#grid-table").trigger("reloadGrid");
}
function getdata(sdate) {
	$.ajax({
		type : "POST", cache : false, dataType : "json",
		url: "/proc_receipt_modify.php",
		data: {	func:'query', date:sdate},
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
		height: 350,
		colNames:['維護', 'GUID','日期','進出口', '客戶', '貨主','貨櫃場','件數','單位','重量','材積','備註','船名','SO','結關日'],
		colModel:[
			{name:'mfunc',index:'', width:30, resize:true,
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
			{name:'GUID',index:'GUID', width:50, editable: false, key:true, hidden:true},
			{name:'Date',index:'Date',width:35, editable:true, editoptions:{size:"8",maxlength:"10"}},
			{name:'TradeType',index:'TradeType', width:30, editable: true, edittype:"select", editoptions:{value:"0:進口;1:出口"}},
			{name:'CustomerID',index:'CustomerID', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'PkgOwner',index:'PkgOwner', width:30, editable: true, editoptions:{size:"8",maxlength:"8"}},
			{name:'Terminal',index:'Terminal', width:30, editable: true, editoptions:{size:"8",maxlength:"8"}},
			{name:'PkgCount',index:'PkgCount', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Unit',index:'Unit', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Weight',index:'Weight', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Volume',index:'Volume', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Note',index:'Note', width:60, editable: true, edittype:'textarea', editoptions:{size:"15",maxlength:"100"}},
			{name:'ShipName',index:'ShipName', width:30, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'SO',index:'SO', width:30,editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'CloseDate',index:'CloseDate', width:30, editable: true, editoptions:{size:"10",maxlength:"10"}} 
		], 

		viewrecords : true,
		rowNum:10,
		rowList:[10,20,30],
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

		editurl: "proc_receipt_modify.php?func=modify",
		caption: "送貨單(進出口)資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	
	setnavGrid(grid_selector, pager_selector);
}
</script>

	<!-- Yandex.Metrika counter -->
<div class="row">
	<div class="col-xs-12">
		<!-- PAGE CONTENT BEGINS -->
		<div class="alert alert-info">
			<button class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			</button>

			<i class="ace-icon fa fa-hand-o-right"></i>
			修改送貨單後需重新填寫貨運逐筆資料。
		</div>
		<div class="row" style="margin-bottom:20px;">
			<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
				<div class="input-group input-group-sm" style="min-width:200px;">
					<input type="text" id="datepicker" class="form-control" placeholder="請點選日期" style="font-size:16px;height:40px;">
					<span class="input-group-addon">
						<i class="ace-icon fa fa-calendar"></i>
					</span>
					<button type="button" class="width-100 pull-right btn btn-sm btn-primary" style="margin-left:10px;height:40px;" onclick="ChangeData();">
						<span class="bigger-110">查詢</span>
					</button>
				</div>
			</div>
		</div>
		<table id="grid-table"></table>

		<div id="grid-pager"></div>

		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.col -->
</div>
