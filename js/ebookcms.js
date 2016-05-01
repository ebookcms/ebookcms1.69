/* ------------------------------------------------------------------------------

	eBookCMS - Version: 1.69 - Revision 0 (September 07, 2015)
	Copyright (C) 2015 Rui Mendes. All Rights Reserved. 
	eBookRTE is licensed under a "Creative Commons License"

--------------------------------------------------------------------------------- */

document.cookie="ejava=enabled";

// *************************************************************************************
// Add new EVENT
// *************************************************************************************
function addEvent(element, event, func) {
	if (element.addEventListener) {
		element.addEventListener(event, func);
	} else if (element.attachEvent) {
		element.attachEvent('on'+event, func);
	} else {
		element["on"+event] = func;
	}
};

// *************************************************************************************
// TOGGLE
// *************************************************************************************
ftoggle = function(div) {
	var elem = document.getElementById(div);
	if (elem.style.display === '') {
		elem.style.display = 'none';
	} else {elem.style.display = '';}
};

// *************************************************************************************
// CHANGE IMAGE
// *************************************************************************************
function chgImg(id,pth,img) {
	var imagem = document.getElementById(id);
	imagem.setAttribute("src", img.value);
};

// *************************************************************************************
// SET MAXIMUM LENGTH TO THE TEXTAREA
// *************************************************************************************
function textarea_max(Object, MaxLen) {return (Object.value.length <= MaxLen);};

// *************************************************************************************
// HIDE SOME DIV-SPACE
// *************************************************************************************
function hideplace(vx, vy, op) {
	if (!document.getElementById) {return;}
	var chk = document.getElementById(vx);
	var s=document.getElementById(vy);
	if (op == '1') {
		if(s&&chk.checked){s.style.display="inline";}
		else if(s){s.style.display="none";}
	} else {
		if(s&&chk.checked){s.style.display="none";}
		else if(s){s.style.display="inline";}
	}
};

// *************************************************************************************
// MULTIPLE SELECTORS TO HIDE PLACE
// *************************************************************************************
function chooseplace(e,t,g) {
	if(!document.getElementById){return;}
	var n=document.getElementById(e);
	var m=g.split("|");
	for(i=0;i<t;i++) {
		var r=e+i; var s=document.getElementById(r);
		if (s && i != n.value) {s.style.display="none";}
		else if (s && i == n.value) {s.style.display="inline";}
	}
	for (j=0; j<m.length; j++) {
		var vx = m[j].split("=");
		var r=e+vx[0]; var s=document.getElementById(r);
		if (n.value == vx[1]){s.style.display="inline";}
	}
};

// *************************************************************************************
// ADMIN TABS
// *************************************************************************************
function tabs(index) {
	var s_tab_content = "tab_content_" + index;
	var contents = document.getElementsByTagName("div");
	for (var x = 0; x < contents.length; x++) {
		name = contents[x].getAttribute("name");
		if (name == 'tab_content') {
			if (contents[x].id == s_tab_content) {
				contents[x].style.display = "block";
			} else {
				contents[x].style.display = "none";
			}
		}
	}
	// tabs
	var s_tab = "tab_" + index;
	var tabs = document.getElementsByTagName("a");
	var litabs = document.getElementsByTagName("li");
	for (var x = 0; x < tabs.length; x++) {
		var name = tabs[x].getAttribute("name");
		if (name == 'tab') {
			if (tabs[x].id == s_tab) {
				tabs[x].className = "active";
			} else {
				tabs[x].className = "";
			}
		}
	}
	// li tag
	var s_tab = "ltab_" + index;
	for (var x = 0; x < litabs.length; x++) {
		name = litabs[x].getAttribute("name");
		if (name == 'tab') {
			if (litabs[x].id == s_tab) {
				litabs[x].className = "current";
			} else {
				litabs[x].className = "";
			}
		}
	}
};

// *************************************************************************************
// SET FOCUS ON FIRST FIELD
// *************************************************************************************
head_onload = function(env) {
	if (!document.getElementById) {return;}
	var url = window.location;
	var url_str = url.toString();
	var anchor = url.hash.substring(1);
	var xhref1 = window.location.href;
	var xhref2 = "";
	xhref1 = xhref1.split('?');
	if (xhref1[1] != undefined) {
		xhref1 = xhref1[1].split('&');
		xhref1 = xhref1[0].split('=');
		xhref2 = "ff"+xhref1[1];
		xhref1 = "ff"+xhref1[0];
	}
	var xhref3 = window.location.href;
	xhref3 = xhref3.split('/');
	var n3=xhref3.length;
	if (xhref3[1] != undefined) {
		xhref3 = xhref3[1].split('/');
		xhref3 = xhref3[0].split('=');
		xhref4 = "ff"+xhref3[1];
		xhref3 = "ff"+xhref3[0];
	}
 	var forms1 = new Array("login","register","activate","ffpages","ffmyprofile","ffmypassword","fflangs","ffsections","ffasections","ffusers","ffiplugins","ffplugins",xhref1,xhref2);
	for (var i=0; i< forms1.length; i++) {
		if (forms1[i]){
			var myelement = document.getElementById(forms1[i]);
			if (myelement !== null ){
				var num = myelement.length;
				var found = false;
				for (var j=0; j<num; j++) {
					if ((myelement[j].type=='text' || myelement[j].type=='password' || myelement[j].type=='email') && myelement[j].value === ""){
						myelement[j].focus(); found =true; break;
					}
				}
				if (found !== true) {
					for (var j=0; j<num; j++) {
						if (myelement[j].type=='text' || myelement[j].type=='password' || myelement[j].type=='email'){
							myelement[j].focus(); break;
						}
					}	
				}
			}
		}
	}
};

// *************************************************************************************
// RESPONSE ERROR AJAX TEXT
// *************************************************************************************
function cmdResponseErr(status_code, output) {
	var objecto = document.getElementById(output), msg = "";
	switch(status_code) {
		case 0:	msg = "Request not initialized"; break;
		case 1: msg = "Server connection established"; break;
		case 2:	msg = "Request received"; break;
		case 3: msg = "Processing request"; break;
		case 4:	msg = "Request finished and response is ready";	break;
		case 404: msg = "Page not found"; break;
		default: msg = "Error found code=" + status_code; 
	} objecto.innerHTML = msg;
};

// *************************************************************************************
// SHOW IMAGES
// *************************************************************************************
function showimages(str, g1) {
	if (str == "") {document.getElementById("images_place").innerHTML = ""; return;}
	else {
		// Code for IE7+, Firefox, Chrome, Opera, Safari
		if (window.XMLHttpRequest) {xmlhttp = new XMLHttpRequest();} 
		// Code for IE6, IE5
		else {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("images_place").innerHTML = xmlhttp.responseText;
			} else {cmdResponseErr(xmlhttp.readyState, "images_place");}
		}
		xmlhttp.open("GET", "ebookcms.php?services=showimage&q="+str+"&p=images"+"&o="+g1, true);
		xmlhttp.send();
	}
};

// *************************************************************************************
// DELETE IMAGE
// *************************************************************************************
function deleteimage(imgfile, folder) {
	var image1 = folder + "/" + imgfile;
	var image2 = folder + "/thumbs/" + imgfile;
	if (window.XMLHttpRequest) {xmlhttp = new XMLHttpRequest();} 
	else {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		// code
		} else {cmdResponseErr(xmlhttp.readyState, "images_place");}
	}
	xmlhttp.open("GET", "ebookcms.php?services=delete_image&img1="+image1+"&img2="+image2, true);
	xmlhttp.send();
};
// *************************************************************************************
// DELETE IMAGE
// *************************************************************************************
function checkimage(str){
	var file = str.value.split('.');
	var ext = file[file.length-1];
	if (ext!="jpg" && ext!="png" && ext!="gif" && ext!="bmp") {
		alert('The file you picked was not an image file');
		var iput = document.getElementById(str.name);
		iput.value = "";
	}
};
// *************************************************************************************
// SHOW GALLERY
// *************************************************************************************
function showgalMin(pth,fdir) {
	var folder = pth != "" ? pth + "/" + fdir : fdir;
	if (fdir == "") {document.getElementById("images_place").innerHTML = ""; return;}
	else {
		// Code for IE7+, Firefox, Chrome, Opera, Safari
		if (window.XMLHttpRequest) {xmlhttp = new XMLHttpRequest();} 
		// Code for IE6, IE5
		else {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("images_place").innerHTML = xmlhttp.responseText;
			} else {cmdResponseErr(xmlhttp.readyState, "images_place");}
		}
		xmlhttp.open("GET", "ebookcms.php?services=showgal&q="+folder, true);
		xmlhttp.send();
	}
};
// *************************************************************************************
// SET FOCUS - ADD EVENTS
// *************************************************************************************
if (document.addEventListener) {
	document.addEventListener( "DOMContentLoaded", head_onload, false );
} else if (window.attachEvent) {
	window.onload = function (evt){head_onload(evt);};
} window.onload = function (evt) {head_onload(evt);};