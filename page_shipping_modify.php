	
<script src="/assets/js/jquery.jqGrid.min.js"></script>
<script src="/assets/js/grid.locale-en.js"></script>

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
		url: "/proc_shipping_modify.php",
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
		//GUID, Date, DeliveryGUID, CustGUID, PkgOwner, DriverGUID, StartPlace, SendPlace, PkgCount, Weight, Volume, CarType, Price, Notes
		colNames:['維護', 'GUID','日期','送貨單號', '客戶', '貨主','司機','起運','送達','數量','重量','材積','車種','金額','備註'],
		colModel:[
			{name:'mfunc',index:'', width:18, resize:true,
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
			{name:'GUID',index:'GUID', width:15, editable: false, key:true},
			{name:'Date',index:'Date',width:25, editable:false},
			{name:'DeliveryGUID',index:'DeliveryGUID', width:20, editable: false},
			{name:'CustGUID',index:'CustGUID', width:20, editable: true, editoptions:{size:"5",maxlength:"10"}},
			{name:'PkgOwner',index:'PkgOwner', width:20, editable: true, editoptions:{size:"5",maxlength:"20"}},
			{name:'DriverGUID',index:'DriverGUID', width:20, editable: true, editoptions:{size:"7",maxlength:"10"}},
			{name:'StartPlace',index:'StartPlace', width:20, editable: true, editoptions:{size:"5",maxlength:"10"}},
			{name:'SendPlace',index:'SendPlace', width:20, editable: true, editoptions:{size:"5",maxlength:"10"}},
			{name:'PkgCount',index:'PkgCount', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Weight',index:'Weight', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Volume',index:'Volume', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'CarType',index:'CarType', width:20, editable: true, editoptions:{size:"5",maxlength:"10"}},
			{name:'Price',index:'Price', width:20, editable: true, editoptions:{size:"5",maxlength:"5"}},
			{name:'Notes',index:'Notes', width:50,editable: true, editoptions:{size:"30",maxlength:"50"}}
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

		editurl: "proc_shipping_modify.php?func=modify",
		caption: "貨運逐筆資料維護"
	});
	$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
	

	//navButtons
	jQuery(grid_selector).jqGrid('navGrid',pager_selector,
		{ 	//navbar options
			/*edit: true,
			editicon : 'ace-icon fa fa-pencil blue',
			add: true,
			addicon : 'ace-icon fa fa-plus-circle purple',*/
			del: true,
			delicon : 'ace-icon fa fa-trash-o red',
			/*search: true,
			searchicon : 'ace-icon fa fa-search orange',
			refresh: true,
			refreshicon : 'ace-icon fa fa-refresh green',
			view: true,
			viewicon : 'ace-icon fa fa-search-plus grey',*/
		},
		{
			//edit record form
			//closeAfterEdit: true,
			//width: 700,
			recreateForm: true,
			beforeShowForm : function(e) {
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
				style_edit_form(form);
			}
		},
		{
			//new record form
			//width: 700,
			closeAfterAdd: true,
			recreateForm: true,
			viewPagerButtons: false,
			beforeShowForm : function(e) {
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
				.wrapInner('<div class="widget-header" />')
				style_edit_form(form);
			}
		},
		{
			//delete record form
			recreateForm: true,
			beforeShowForm : function(e) {
                  $("td.delmsg",e).html("delete the row with <b>id=" +
                                                      jQuery(grid_selector).jqGrid('getGridParam','selrow') + "</b>?");
				var form = $(e[0]);
				if(form.data('styled')) return false;
				
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
				style_delete_form(form);
				
				form.data('styled', true);
			},
			/*reloadAfterSubmit:true,
			jqModal:true,
			closeOnEscape:true,
			caption: "Delete...",
			msg: "Delete me now!", // del options
			*/
			onClick : function(e) {
				//alert(1);
			}
			
		},
		{
			//search form
			recreateForm: true,
			afterShowSearch: function(e){
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
				style_search_form(form);
			},
			afterRedraw: function(){
				style_search_filters($(this));
			}
			,
			multipleSearch: true,
			/**
			multipleGroup:true,
			showQuery: true
			*/
		},
		{
			//view record form
			recreateForm: true,
			beforeShowForm: function(e){
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
			}
		}
	)
	

	//var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');
/*
	$(document).one('ajaxloadstart.page', function(e) {
		$.jgrid.gridDestroy(grid_selector);
		$('.ui-jqdialog').remove();
	});
	*/
}
function style_edit_form(form) {
	//enable datepicker on "sdate" field and switches for "stock" field
	form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
	
	form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
			   //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
			  //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');

			
	//update buttons classes
	var buttons = form.next().find('.EditButton .fm-button');
	buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
	buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
	buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')
	
	buttons = form.next().find('.navButton a');
	buttons.find('.ui-icon').hide();
	buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
	buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');		
}

function style_delete_form(form) {
	var buttons = form.next().find('.EditButton .fm-button');
	buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
	buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
	buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
}

function style_search_filters(form) {
	form.find('.delete-rule').val('X');
	form.find('.add-rule').addClass('btn btn-xs btn-primary');
	form.find('.add-group').addClass('btn btn-xs btn-success');
	form.find('.delete-group').addClass('btn btn-xs btn-danger');
}
function style_search_form(form) {
	var dialog = form.closest('.ui-jqdialog');
	var buttons = dialog.find('.EditTable')
	buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
	buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
	buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
}

function beforeDeleteCallback(e) {
	var form = $(e[0]);
	//alert(JSON_parse(form));
	if(form.data('styled')) return false;
	
	form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	style_delete_form(form);
	
	form.data('styled', true);
	
}

function beforeEditCallback(e) {
	var form = $(e[0]);
	form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	style_edit_form(form);
}



//it causes some flicker when reloading or navigating grid
//it may be possible to have some custom formatter to do this as the grid is being created to prevent this
//or go back to default browser checkbox styles for the grid
function styleCheckbox(table) {
/**
	$(table).find('input:checkbox').addClass('ace')
	.wrap('<label />')
	.after('<span class="lbl align-top" />')


	$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
	.find('input.cbox[type=checkbox]').addClass('ace')
	.wrap('<label />').after('<span class="lbl align-top" />');
*/
}


//unlike navButtons icons, action icons in rows seem to be hard-coded
//you can change them like this in here if you want
function updateActionIcons(table) {
	/**
	var replacement = 
	{
		'ui-ace-icon fa fa-pencil' : 'ace-icon fa fa-pencil blue',
		'ui-ace-icon fa fa-trash-o' : 'ace-icon fa fa-trash-o red',
		'ui-icon-disk' : 'ace-icon fa fa-check green',
		'ui-icon-cancel' : 'ace-icon fa fa-times red'
	};
	$(table).find('.ui-pg-div span.ui-icon').each(function(){
		var icon = $(this);
		var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
		if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
	})
	*/
}

//replace icons with FontAwesome icons like above
function updatePagerIcons(table) {
	var replacement = 
	{
		'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
		'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
		'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
		'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
	};
	$('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
		var icon = $(this);
		var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
		
		if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
	})
}

function enableTooltips(table) {
	$('.navtable .ui-pg-button').tooltip({container:'body'});
	$(table).find('.ui-pg-div').tooltip({container:'body'});
}



//enable search/filter toolbar
//jQuery(grid_selector).jqGrid('filterToolbar',{defaultSearch:true,stringResult:true})
//jQuery(grid_selector).filterToolbar({});


//switch element when editing inline
function aceSwitch( cellvalue, options, cell ) {
	setTimeout(function(){
		$(cell) .find('input[type=checkbox]')
			.addClass('ace ace-switch ace-switch-5')
			.after('<span class="lbl"></span>');
	}, 0);
}
//enable datepicker
function pickDate( cellvalue, options, cell ) {
	setTimeout(function(){
		$(cell) .find('input[type=text]')
			.datepicker({format:'yyyy-mm-dd' , autoclose:true}); 
	}, 0);
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
			送貨單號相同的資料是同一筆客戶訂單分為兩個司機運送。
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
