//-------------------------------------------------------------------------------------mobile

window.jo_mobilecheck = jo_check_device();
window.jo_labelset = [
  {
    selector: '[data-jo-content="repeated"]',
    class: 'jo_label_large',
    features: [
      ["/jocms/core/style/icons/plusgray.svg", jo_additem]
    ],
    init: function(e){
      $(e).find(".ui-sortable").add($(e).filter(".ui-sortable")).each(function(){
        if(typeof $(this).sortable('instance') != "undefined"){
          $(this).sortable("destroy");
        }
      });
      $(e).sortable({
        items: ">*:not(.jo_elem)",
        handle: ".jo_handle"
      });
    }
  },
  {
    selector: '[data-jo-content="repeated"]>*:not(.jo_label)',
    class: 'jo_label_small',
    features: [
      ["/jocms/core/style/icons/drag.svg", "jo_handle"],
      ["/jocms/core/style/icons/delete.svg", jo_delete]
    ],
    init: function(e){
      if($(e).parents("[data-jo='true']").is('.mce-content-body')){
        var mce_id = $(e).parents("[data-jo='true']").attr("id");
        $(e).find(".jo_label .jo_handle").mousedown(function(){
          tinymce.get(mce_id).setMode('readonly');
        }).mouseup(function(){
          tinymce.get(mce_id).setMode('design');
        });
      }
    }
  }
];

$(document).ready(function(e){
    //Speicher warnung
    window.onbeforeunload = function() {
        return jo_lang.MSG_LEAV;
    };

    //taskvalid
    window.jo_interval = setInterval("jo_taskvalid()",2000);

    jo_actionwindow(jocms_imageupload,function(){
      jocms_content('cancel');
    });

    jo_code_prepare($(document.body), false);
    jo_highlightarea($(document.body), window.jo_labelset);

    var selector = '[data-jo="true"]:not([data-jo-content="repeated"]), [data-jo="true"][data-jo-content="repeated"] [data-jo-content="editable"]:not([data-jo-content="noneditable"] [data-jo-content="editable"])';
    $(selector).each(function(){
      jo_tinymce_init(this, window.jo_mobilecheck);
    });
});

//-------------------------------------------------------------------------------------initialize editor
//input: object for editor
//output: id of editor
function jo_tinymce_init(elem, mobile = false){
  if($(elem).is('.mce-content-body[id]')){
     tinymce.get($(elem).attr('id')).destroy();
  }
  if($(elem).is(jo_set.ed_block)){
    block = true;
  }else{
    block = false;
  }
  var setup = "";
  if(mobile==false){
    if(block == false){
      var tools = ['undo redo',
          'bold italic underline forecolor',
          'backcolor link unlink'
      ];
    }else{
      var tools = ['undo redo styleselect',
          'bold italic underline forecolor',
          'fontselect fontsizeselect',
          'backcolor link unlink image',
          'alignleft alignright aligncenter alignjustify',
          'numlist bullist addmask'
      ];
      var setup = function (editor) {
          //actionbars for masks
          editor.on('SetContent', function(){
            jo_highlightarea(editor.bodyElement, window.jo_labelset);
          });
          //button for new masks
          editor.addButton('addmask', {
              icon: 'insertdatetime',
              image: tinymce.baseURL + "../../../../../../core/style/icons/maskblack.svg",
              tooltip: window.jo_lang['MSK_NEW'],
              onclick: function (){
                   jo_window({
                       id: "mask",
                       type: "list",
                       desc: window.jo_lang['MSK_NEW'],
                       cb: function(opt, cb){
                           var message = {content: "all"};
                           jo_ajax('/jocms/apps/mask/inc/getmask.php', message, function(e,data,url){
                               var mask_list = {};
                               e.masks.forEach(function(val){
                                   mask_list[val.id] = val.name;
                               });
                               cb(mask_list);
                           });
                       }
                   }, function(mask){
                       var message = {content: mask["mask"]};
                       jo_ajax('/jocms/apps/mask/inc/getmask.php', message, function(e,data,url){
                           editor.insertContent(jo_code_prepare(e.masks[0].code));
                           jo_highlightarea(editor.bodyElement, window.jo_labelset)
                       });
                   });
                }
          });
        };
    }
    var plugins = "link image imagetools lists textcolor noneditable";
  }else{
    if(block == false){
      var tools = ['undo redo bold italic underline'];
    }else{
      var tools = ['undo redo bold italic underline numlist bullist'];
    }
    var plugins = "lists noneditable";
  }

  var jo_tinymce_settings = {
    //init
    target: elem,
    toolbar: tools,
    plugins: plugins,
    setup: setup,

    //apperance
    skin: "custom",
    inline: true,
    statusbar: false,
    branding: false,
    fixed_toolbar_container:"#jocms_buttons",

    //functionality
    //custom_ui_selector: ".jo_panel_drag",
    relative_urls : false,
    entity_encoding: "raw",
    force_br_newlines : false,

    //menu
    menubar:"",
    language: window.jo_lang.MCE_FILE,

    //fonts
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",

    //media handling
    paste_data_images: false ,
    file_picker_types: "image",
    image_title: true,
    automatic_uploads: false,
    images_upload_url: "/jocms/apps/editor/inc/upload.php",
    file_picker_types: "image",
    images_reuse_filename: true,

    //filepicker for images
    file_picker_callback: function(callback, value, meta) {
        var form = [];
        form.push({
            id: "url",
            cb: function(opt, cb){
              jo_ajax('/jocms/apps/editor/inc/galery.php',"",function(e,data,url){
                  var url_list = {};
                  e.files.forEach(function(val){
                      url_list[val] = val;
                  });
                  cb(url_list);
              });
            },
            opt: meta.filetype,
            type: "thumbnails",
            desc: jo_lang.FORM_IMG
        });
        form.push({
            id: "desc",
            type: "text",
            desc: jo_lang.FORM_DESC
        });
       jo_window(form, function(attr){
           callback(attr.url, {alt: attr.desc});
       });
    }
  };
  tinymce.init(jo_tinymce_settings);
  return $(elem).attr('id');
}

//------------------------------------------------------------------------------------- jocms jo_ajax

function jo_taskvalid(){
  var content_complete = {};
  content_complete.url = window.location.href;
  jo_ajax('/jocms/core/inc/taskvalid.php',content_complete,function(e,data,url){});
}

function jocms_imageupload(){
    $("#jocms_functions_submit_save").addClass("jo_load");
    var ed = tinymce.editors;
    ed.counter = 0;
    for(i=0; i<ed.length; i++){
        tinymce.editors[i].uploadImages(function(success) {
             ed.counter = ed.counter + 1;
            if(ed.counter==ed.length){
                jocms_content("save");
            }
        });
    }

}

function jocms_content(advice){
    //Warnung ausschalten
    window.onbeforeunload = null;

    //var zum verschicken
    var content_complete = {};
    content_complete.type = "";

    //unterscheiden zw cancel und save
    if(advice != "save"){
        content_complete.type = "cancel";
        $("#jocms_functions_submit_cancel").addClass("jo_load");
    }else{
        //Editierbare Elemente in array
        content_complete.html = [];


        $("[data-jo='true']").each(function(){
          $(this).find('.mce-content-body').add($(this).filter('.mce-content-body')).each(function(){
            var id = $(this).attr("id");
            if($(this).is('[id^=mce]')){
              $(this).removeAttr("id");
            }
            var html = tinymce.get(id).getContent();
            tinymce.get(id).destroy();
            $(this).html(html);
          });
        });
        $('.jo_label').remove();
        $('.jo_elem').remove();
        $('.ui-sortable').removeClass('ui-sortable');
        $('.mceEditable').removeClass('mceEditable');
        $('.mceNonEditable').removeClass('mceNonEditable');
        $('.MsoNormal').removeClass('MsoNormal');
        $('[spellcheck]').removeAttr('spellcheck');
        $('[id^=mce]').removeAttr("id");

        $("[data-jo='true']").each(function(){
          if($(this).is('[data-cmspos]')){
            var pos = $(this).attr('data-cmspos');
            content_complete.html[pos] = jo_code_clean($(this).html());
          }
        });

    }
    content_complete.url = window.location.href;
    // console.log(document.documentElement.outerHTML);
    // console.log(content_complete);
    // alert(content_complete.html[0]);
    jo_ajax('/jocms/apps/editor/inc/save.php',content_complete,function(e,data,url){
        clearInterval(window.jo_interval);
        window.location.replace("/jocms/control/cms.php?path=" + encodeURIComponent(e.redirection));
    },false);
}


//------------------------------------------------------------------------------------- jocms floating labels

//inserts editable and noneditable areas in code segment
//input: code (text or jquery object), plain = false if jquery object
//return: plain html code if plain = true
function jo_code_prepare(code, plain=true){
    if(plain == true){
      var code = $.parseHTML(code);
    }
    var select_editable = "[data-jo-content='editable']";
    var select_noneditable = "[data-jo-content='repeated'], [data-jo-content='noneditable']"
    $(code).filter(select_editable).add($(code).find(select_editable)).each(function(){
        $(this).addClass("mceEditable");
    });
    $(code).filter(select_noneditable).add($(code).find(select_noneditable)).each(function(){
        $(this).addClass("mceNonEditable");
    });
    if(plain==true){
      return $(code).prop('outerHTML');
    }
    return code;
}
//removes all jocms items from code
//input: html code
//return: cleaned code
function jo_code_clean(code){
  var wrapper = $("<div></div>").html(code);
  var selector = ".mceEditable, .mceNonEditable";
  $(wrapper).find(selector).each(function(){
    $(this).removeClass(selector.replace(",", " "));
  });
  var selector = ".jo_label";
  $(wrapper).find(selector).each(function(){
    $(this).remove();
  });
  return $(wrapper).html();
}


function jo_delete(e){
    var remaining = $(e).parent().children("*:not(.jo_label)").length;
    if(remaining > 1){
      $(e).remove();
    }else{
      $(e).filter("[data-jo-content='editable']").add($(e).find("[data-jo-content='editable']")).each(function(){
          $(this).html("");
      });
      $(e).css("display", "none");
      $(e).find(".jo_label").remove();
    }
}

//adds masks
//input: repeated container object
//return: ref to new element
function jo_additem(elem){
    var code = $(elem).children("*:not(.jo_label)"); //////if no element?
    var len = $(code).length;
    var code_new = $($(code)[0].outerHTML);
    if(len < 2 && $(code[0]).is(":hidden")){
      $(code_new).css("display", "");
      $(code).remove();
    }
    $(code_new).filter("[data-jo-content='editable']").add($(code_new).find("[data-jo-content='editable']")).each(function(){
        $(this).html("Insert content here");
    });
    $(code_new).find(".jo_label").remove();
    $(elem).append(jo_code_prepare(code_new, false));
    $(code_new).find("[id]").add($(code_new).filter("[id]")).each(function(){
      $(this).removeAttr("id");
    });
    //tinymce.execCommand('mceAddEditor', false, mce_id);
    if($(elem).parents('.mce-content-body').length > 0){
      var mce_id = $(elem).parents("[data-jo='true']").attr("id");
      jo_highlightarea(tinymce.activeEditor.bodyElement, window.jo_labelset);
    }else{
      var selector = $(code_new).find('[data-jo-content="editable"]:not([data-jo-content="noneditable"] [data-jo-content="editable"])');
      var ed_id = "";
      $(selector).each(function(){
        ed_id = jo_tinymce_init(this, window.jo_mobilecheck);
      });
      jo_highlightarea(elem, window.jo_labelset);
      // if(typeof ed_id != 'undefined'){
      //     tinyMCE.get(ed_id).focus();
      // }
      return code_new;
    }

}
