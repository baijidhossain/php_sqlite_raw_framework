function uploadFile(){
	var filecount = _("input_file").files.length;
	//alert(file.name+" | "+file.size+" | "+file.type);
	var formdata = new FormData();
	for(var x = 0; x < filecount; x++)
	{
		formdata.append("files[]", _("input_file").files[x]);
	}
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "/includes/file_parser.php");
	ajax.send(formdata);
}
function progressHandler(event){
	//_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
	var percent = (event.loaded / event.total) * 100;
	_("progressBar").style.width = Math.round(percent)+'%';
	_("status").innerHTML = 'uploading ' + Math.round(percent)+"%";
}
function completeHandler(event){
	_("status").innerHTML = event.target.responseText;
	_("progressBarC").classList.remove("active");
	_("up-more").style.display = "block";
	_("rp-btn").style.display = "block";
}
function errorHandler(event){
	_("status").innerHTML = "Upload Failed";
}
function abortHandler(event){
	_("status").innerHTML = "Upload Aborted";
}

$(document).ready(function(){
    $("#file_search").keyup(function(){
        if(this.value != "")
		{
			var items = _("file_list").getElementsByTagName("li");
			for(i = 0; i < items.length; i++)
			{
				var tag = items[i].getElementsByTagName("a")[0];
				var found = tag.innerHTML.search(new RegExp(this.value, "i"));
				if(found < 1)
				{
					items[i].classList.add("hidden");
				}
				if(found > 0 && items[i].classList.contains("hidden"))
				{
					items[i].classList.remove("hidden");
				}
			}
		}
		else
		{
			var items = _("file_list").getElementsByTagName("li");
			for(i = 0; i < items.length; i++)
			{
				if(items[i].classList.contains("hidden"))
				{
					items[i].classList.remove("hidden");
				}
			}
		}
    });
	
});

$(document).on('click', '#s_rename', function() {
	this.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
	var o_name =  this.parentElement.getElementsByTagName('a')[0].getAttribute("data-text");
	var n_name = _("input_rename").value;
	var formdata = new FormData();
	formdata.append("rename", n_name);
	formdata.append("oldname", o_name);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", RenameCompleteHandler, false);
	ajax.open("POST", "/includes/file_parser.php");
	ajax.send(formdata);
});
	
function RenameCompleteHandler(event){
	var NewName =  event.target.responseText;
	var aTag = _("input_rename").parentElement.getElementsByTagName('a')[0];
	var pItem = _("input_rename").parentElement.parentElement;
	aTag.innerHTML = NewName;
	aTag.setAttribute("data-text", NewName);
	_("input_rename").remove();
	_("s_rename").remove();
	aTag.classList.remove('hidden');
	pItem.getElementsByClassName("rename")[0].setAttribute("data-text", NewName);
	pItem.getElementsByClassName("delete")[0].setAttribute("data-text", NewName);
	var dTag = pItem.getElementsByClassName("download")[0];
	var dLink = dTag.getAttribute("href");
	var LastS = dLink.substring(dLink.lastIndexOf('/') + 1);
	dLink = dLink.replace(LastS, NewName);
	dTag.setAttribute("download", NewName);
	dTag.setAttribute("href", dLink);
}

$(document).on('click', '.file', function() {
	var ImageExt = ["jpg", "jpeg", "png", "gif", "bmp"];
	var CodeExt = ["html", "css", "js", "php", "xml", "htm", "json", "txt"];
	var filename = this.getAttribute("data-text");
	var fileExt = filename.split('.').pop();
	fileExt = fileExt.toLowerCase();
	
	if(ImageExt.indexOf(fileExt) > -1)
	{
		var target_img = _("image-popup").getAttribute("data-text") + filename;
		_("image-popup").setAttribute("href", target_img);
		_("image-popup").click();
	}
	else if(CodeExt.indexOf(fileExt) > -1)
	{
		_("folder").value = this.getAttribute("data-text");
		_("folder").name = "edit";
		_("folder").parentElement.submit();
	}
	
	});


$(".rename").click(function(){
		var container = this.parentElement.parentElement.getElementsByTagName('span')[0];
		var iconTag = container.getElementsByTagName('i')[0]; 
		var filename = this.getAttribute('data-text');
		var aTag = container.getElementsByTagName('a')[0];
		var items = _("file_list").getElementsByTagName('li');
		
		for(i = 0; i < items.length; i++)
		{
			var itemAtag = items[i].getElementsByTagName('a')[0];
			if(itemAtag.classList.contains("hidden"))
			{
				itemAtag.classList.remove("hidden");
				_("input_rename").remove();
				_("s_rename").remove();
			}
		}
		container.getElementsByTagName('a')[0].classList.add("hidden");
		container.innerHTML += ' <input stype="text" name="rename" id="input_rename" value="'+filename+'"><button id="s_rename" class="s_rename"><i class="fa fa-check-square-o"></i></button>';
		
	});

	
$("#input_file").change(function(){
	$("#file_selector").slideUp();
	$("#file_uploader").slideDown();
	uploadFile();
	});

$("#up-more").click(function(){
	$("#file_selector").slideDown();
	$("#file_uploader").slideUp();
	_("up-more").style.display = "none";
	_("rp-btn").style.display = "none";
	_("status").innerHTML = "uploading";
	_("progressBar").style.width = '0%';
	_("input_file").value = '';
	});
	
	
$(".delete").click(function(){
		var confirmed = confirm('Are you sure want to delete this item??');
		if(confirmed)
		{
			_("folder").value = this.getAttribute("data-text");
			_("folder").name = "delete";
			_("folder").parentElement.submit();
		}
	});
	
	
$(".folder").click(function(){
		_("folder").value = this.getAttribute("data-text");
		_("folder").parentElement.submit();
	});

$("#upload").click(function(){
		$("#folderContainer").slideUp();
		$("#uploadContainer").slideToggle();
	});
	
$("#mk-folder").click(function(){
		$("#uploadContainer").slideUp();
		$("#folderContainer").slideToggle();
	});
	
$(document).on('keyup', '.CodeMirror', function() {
	if(_("save_code").classList.contains("disabled"))
	{
		_("save_code").classList.remove("disabled");
		_("save_code").innerHTML = '<i class="fa fa-save"></i> Save';
	}
});


$(document).on('click', '#save_code', function() {
	var textdata = editor.getValue();
	this.innerHTML = '<i class="fa fa-spin fa-spinner"></i><span>Saving</span>';
	this.classList.add("disabled");
	var fileName =  this.getAttribute("data-text");
	var Cdomain = _("c_domain").value;
	var formdata = new FormData();
	formdata.append("name", fileName);
	formdata.append("data", btoa(textdata));
	formdata.append("domain", Cdomain);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", SaveCodeCompleteHandler, false);
	ajax.open("POST", "/includes/file_parser.php");
	ajax.send(formdata);
});


function SaveCodeCompleteHandler(event){
	if(event.target.responseText == "saved")
	{
		_("save_code").innerHTML = '<i class="fa fa-check-square"></i><span>Saved</span>';
	}
	else
	{
		_("save_code").outerHTML = event.target.responseText;
	}
}