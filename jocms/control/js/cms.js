//Executed when frame has loaded DOM, adds url to buttons and adress bar and checks if contains data-jo attribute
function jo_frame_ready(){
    clearInterval(window.jo_frame_interval);
    window.jo_frame_interval = false;

    var iframe = document.querySelector('#frame');

    iframe.contentWindow.onunload = jo_frame_loading;
    var url = $("#frame").contents().get(0).location.href;
    var a = document.createElement('a');
    a.href = url;
    var url = a.pathname.split('#')[0].split('?')[0];

    history.pushState({info: "jocms"},"jocms","?path=" + encodeURIComponent(url));

    var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
    if(iframedoc.querySelectorAll("*[data-jo='true'], *[data-cms='cms']").length != 0){
        $("#edit").remove();
        $("#buttons").append('<div id="edit" onclick="jo_openfile(\'' + url + '\')"><img src="/jocms/core/style/icons/edit.svg" title="'+jo_lang.CMS_EDIT+'"/></div>');
    }else{
        $("#edit").remove();
    }

    $("#areaselector").attr("href","/jocms/apps/areaselector/index.php?file=" + encodeURIComponent(url));
    $("#code").attr("href","/jocms/apps/codeeditor/index.php?file=" + encodeURIComponent(url));
    $("#history").attr("href","/jocms/apps/history/index.php?file=" + encodeURIComponent(url));

}
//Interval for checking if DOM is ready
function jo_frame_loading(){
    $("#edit").remove();
    var iframe = document.querySelector('#frame');
    window.jo_frame_interval = setInterval(function(){
        var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
        if(iframedoc.querySelectorAll("*[data-jo='true'], *[data-cms='cms']").length != 0){
            jo_frame_ready();
        }
    },500)

}
$(document).ready(function(){
        document.getElementById("frame").contentWindow.addEventListener('error', function (error) {
          jocms_noticebar_func('js_error','File: ' + error.error.fileName + ' Location: ' + error.error.lineNumber + '/' + error.error.columnNumber);
        });
        function jocms_explorer(e){
            var type = $(e).attr("id");
            if($(e).hasClass("explorer_close") === true){
              type = "";
            }

            $(".openmenu").find(".explorer_close").remove();
            $(".openmenu").removeClass("explorer_close");
            $(".openmenu").find("img").css("display","");

            switch (type) {
              case "explorer_files":
                  $("#explorer div").hide();
                  $("#file_explorer").show();
                  jo_explorer_toggle(e);
                break;
              case "explorer_more":
                  $("#explorer div").hide();
                  $("#options_explorer").show();
                  jo_explorer_toggle(e);
                break;
              default:
                if($("#navigation").width() > 50){
                    $("#navigation").width("45px");
                }
            }
        }

        function jo_explorer_toggle(e){
          $(e).find("img").css("display","none");
          $(e).addClass("explorer_close");
          $(e).append("<img src='/jocms/core/style/icons/close.svg' class='explorer_close' title='"+jo_lang.FORM_DISM+"' />");
          if($("#navigation").width() < 50){
              $("#navigation").width("350px");
          }
        }

        //file explorer functions
        jo_folder_toggle($(".jo_folder"));

        $(".jo_folder").each(function(){
            if($(this).parent().find(".jocms_file").length == 0){
              $(this).addClass("jo_folder_inactive");
              $(this).css({"pointer-events":"none"});
            }
        });

        $(".jocms_file a").click(function(){
            var url = $(this).data("url") + "?v=" + Math.floor((Math.random() * 100) + 1);
            $('iframe').attr("src",url);
            jocms_explorer();
        });

        $(".openmenu").click(function(){
            jocms_explorer(this);
        });

        //set up edit button
        jo_frame_loading();
        $("#frame").load(function(){
            if(window.jo_frame_interval != false){
                jo_frame_ready();
            }

        });

    });

function jo_openfile(url){
  $("#edit").addClass("jo_load");
  var content_complete = {};
  content_complete.url = encodeURIComponent(url);
  jo_ajax('/jocms/apps/editor/inc/filemanager.php',content_complete,function(e,data,url){
      window.location.assign(decodeURIComponent(e.url)+ "_cms_temp.php?jocms=" +  e.pin);
  });
}
