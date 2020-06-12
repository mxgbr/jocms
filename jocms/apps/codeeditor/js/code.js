function jo_get_parameter(name, url) {
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function jo_taskvalid(){
  var content_complete = {};
  content_complete.url = jo_get_parameter("file",window.location.href);
  jo_ajax('/jocms/core/inc/taskvalid.php',content_complete,function(e,data,url){});
}

$(document).ready(function(e){
  setInterval("jo_taskvalid()",10000);
});
