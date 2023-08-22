$(document).on('click', '.publish', function() {
	var sText = "";
	var setText = "";
	var cElem = this;
	var eText = this.innerHTML;
	var _url = "http://"+document.getElementById("cdomain").getAttribute("data-text")+"/mysql_bridge.php";
	var itemID = this.getAttribute("data-text");
	this.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
	if(this.getAttribute("data-status") == 0)
	{
		sText = '<i class="fa published1" data-toggle="tooltip" data-original-title="Unpublish"></i>';
		setText = "1";
	}
	else
	{
		sText = '<i class="fa published0" data-toggle="tooltip" data-original-title="Publish"></i>';
		setText = "0";
	}
	$.post(_url, {"resetdata": "null", "active": setText, "_key": $('#key').attr("data-text"), "_table": btoa('menu'), "target": "id = '"+itemID+"'"}, function(result){
        if(result.includes('Done'))
		{
			cElem.innerHTML = sText;
			cElem.setAttribute("data-status", setText);
		}
		else
		{
			cElem.innerHTML = eText;
			alert(result);
		}
    });
});

$(document).on('click', '.homepage', function() {
	var cElem = this;
	var eText = this.innerHTML;
	var oldElem = document.getElementsByClassName("homepage1")[0];
	var _url = "http://"+$("#cdomain").attr("data-text")+"/mysql_bridge.php";
	var itemID = this.getAttribute("data-text");
	this.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
	$.post(_url, {"resetdata": "null", "home": "0", "_key": $('#key').attr("data-text"), "_table": btoa('menu'), "target": "home = '1'"}, function(result){
        if(result.includes('Done'))
		{
			try{
				document.getElementsByClassName("homepage1")[0].outerHTML = '<a class="homepage" data-text="'+oldElem.getAttribute("data-text")+'"><i class="fa fa-home homepage0"></i></a>';
			}catch(err){ }
			$.post(_url, {"resetdata": "null", "home": "1", "_key": $('#key').attr("data-text"), "_table": btoa('menu'), "target": "id = '"+itemID+"'"}, function(result){
				if(result.includes('Done'))
				{
					cElem.outerHTML = '<i class="fa fa-home homepage1" data-text="'+itemID+'"></i>';
				}
				else
				{
					cElem.innerHTML = '<i class="fa fa-home homepage0"></i>';
					alert(result);
				}
			});
		}
		else
		{
			cElem.innerHTML = '<i class="fa fa-home homepage0"></i>';
			alert(result);
		}
    });
});

$(document).on('click', '.addpage', function() {
	window.location.href = "/menu_pages.php?cat="+this.getAttribute('data-text')+"&task=create";
});


$(document).on('click', '.delete', function() {
	var confirmed = confirm('Are you sure want to delete this Page??');
	if(!confirmed)
	{
		return false;
	}
});

$(document).on('click', '.delete_cat', function() {
	var confirmed = confirm('It will be delete with all containing pages. is it OK??');
	if(confirmed)
	{
		_("delete_cat").value = this.getAttribute("data-text");
		_("delete_cat").parentElement.submit();
	}
});

$(document).on('click', '.rename_cat', function() {
	var container = this.parentElement.parentElement.getElementsByTagName('td')[1];
	var s_container = container.getElementsByTagName('span')[0];
	var o_name = s_container.innerHTML;
	if ($('#input_rename').length > 0) {
		_("input_rename").parentElement.getElementsByTagName("span")[0].classList.remove("hidden");
		_("input_rename").remove();
		_("s_rename").remove();
	}
		
	s_container.classList.add("hidden");
	container.innerHTML += ' <input stype="text" name="rename" id="input_rename" value="'+o_name+'"><button id="s_rename" data-text="'+this.getAttribute('data-text')+'" ><i class="fa fa-check-square-o"></i></button>';
});

$(document).on('click', '#s_rename', function() {
	this.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
	var targetID =  this.getAttribute("data-text");
	var n_name = _("input_rename").value;
	$.post("http://"+document.getElementById("cdomain").getAttribute("data-text")+"/mysql_bridge.php", {"resetdata": "null", "name": n_name, "_key": $('#key').attr("data-text"), "_table": btoa('menu_category'), "target": "id = '"+targetID+"'"}, function(result){
        if(result.includes('Done'))
		{
			_("input_rename").parentElement.getElementsByTagName("span")[0].innerHTML = n_name;
			_("input_rename").parentElement.getElementsByTagName("span")[0].classList.remove("hidden");
			_("input_rename").remove();
			_("s_rename").remove();
		}
		else
		{
			_("input_rename").parentElement.getElementsByTagName("span")[0].classList.remove("hidden");
			_("input_rename").remove();
			_("s_rename").remove();
			alert(result);
		}
    });
	
});

_("param_type").addEventListener("change", SetContentType);
function SetContentType()
{
	_("delete_cat").name = "setcontent";
	_("delete_cat").value = $("#param_type").val();
	_("delete_cat").parentElement.submit();
}