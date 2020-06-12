document.addEventListener("DOMContentLoaded", function(){
    function jo_append(elem,type,src){
      var s = document.createElement(elem);
      s.type = type;
      s.src = src;
      s.async = false;
      document.head.appendChild(s);
    }

    if(!window.jQuery){
        jo_append('script','text/javascript','https://code.jquery.com/jquery-latest.js');
    }

    var s = document.createElement('link');
    s.rel = 'stylesheet';
    s.href = '/jocms/core/style/css/stylesheet.css';
    document.head.appendChild(s);

    jo_append("script","text/javascript","/jocms/apps/editor/tinymce/js/tinymce/tinymce.min.js");

    jo_append('script','text/javascript','https://code.jquery.com/ui/1.12.1/jquery-ui.js');

    jo_append("script","text/javascript","/jocms/core/js/ui.js");

    jo_append("script","text/javascript","/jocms/apps/editor/js/interface.js");
}, false);
