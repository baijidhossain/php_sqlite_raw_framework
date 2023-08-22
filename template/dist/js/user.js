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
		sText = '<i class="fa published1" data-toggle="tooltip" data-original-title="Deactive User"></i>';
		setText = "1";
	}
	else
	{
		sText = '<i class="fa published0" data-toggle="tooltip" data-original-title="Active User"></i>';
		setText = "0";
	}
	$.post(_url, {"resetdata": "null", "active": setText, "_key": $('#key').attr("data-text"), "_table": btoa('users'), "target": "id = '"+itemID+"'"}, function(result){
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
