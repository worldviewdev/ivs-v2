function checkall(objForm){	
	var len = objForm.elements.length;
	var i=0;
	for( i=0 ; i<len ; i++) {
		if (objForm.elements[i].type=='checkbox') {
			objForm.elements[i].checked=objForm.check_all.checked;
		}
	}
}
function checkall_link(objForm,act){
	
	var len = objForm.elements.length;
	var i=0;
	for( i=0 ; i<len ; i++) {
		if (objForm.elements[i].type=='checkbox') {
			if(act=='check'){
				objForm.elements[i].checked=true;
			}else{
				objForm.elements[i].checked=false;
			}
		}
	}
}


	


function confirm_submit(objForm) {
	return true;
}

function translator(pattern) {
	var open_in_same_window = 1;
	var my_location = unescape(document.location.toString());
	var new_location ='';
	var new_pattern = '';
	if (my_location.indexOf('translate_url_content?') != -1) {
		/// From google...
		var indexof_u = my_location.indexOf('trurl=');
		if (indexof_u == -1) {
			new_location = document.location;
		}
		else {
			var subs = my_location.substring(indexof_u, my_location.length);
			var ss = subs.split('&');
			new_location = ss[0].substring(2, ss[0].length);
			
			var indexof_url = new_location.indexOf('=url=');
			new_location=new_location.substring(indexof_url+5, new_location.length);
		}
	}
	else {
		new_location = document.location;
	}

	indexof_p = pattern.indexOf('|');

	var isen = '';
	if (indexof_p == -1) {
		indexof_p1 = pattern.indexOf('><');
		if (indexof_p1 == -1) {
			new_pattern = pattern;
			if (pattern == 'en') {
				isen = 1;
			}
		}
		else {
			var psplit =pattern.split('><');
			new_pattern = psplit[0]+'|'+psplit[1];
			if (psplit[1] == 'en') {
				isen = 1;
			}
		}
	}
	else {
		var psplit = pattern.split('|');
		new_pattern = psplit[0]+'|'+psplit[1];
		if (psplit[1] == 'en') {
			isen = 1;
		}
	}

	var thisurl = '';
	if (isen == 1) {
		thisurl = new_location;
	}
	else {
		thisurl = 'http://translate.google.com/translate?hl='+psplit[0]+'&ie=UTF-8&oe=UTF-8&langpair='+psplit[0]+'%7C'+psplit[1]+'&u='+ new_location+'&prev=%2Flanguage_tools';
	}

	if (open_in_same_window == 1) {
		window.location.href = thisurl;
	}
	else {
		if (CanAnimate ){
			msgWindow=window.open('' ,'subwindow','toolbar=yes,location=yes,directories=yes,status=yes,scrollbars=yes,menubar=yes,resizable=yes,left=0,top=0');
			msgWindow.focus();
			msgWindow.location.href = thisurl;
		}
		else {
			msgWindow=window.open(thisurl,'subwindow','toolbar=yes,location=yes,directories=yes,status=yes,scrollbars=yes,menubar=yes,resizable=yes,left=0,top=0');
		}
	}
}