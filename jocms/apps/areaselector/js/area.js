$(document).ready(function(e){

    //warning bevore leaving
    window.onbeforeunload = function() {
        return jo_lang.MSG_LEAV;
    };

    //taskvalid interval
    setInterval("jo_taskvalid()",2000);

    //create floating panel
    jo_actionwindow(function(){
      jocms_content('save');
    },function(){
      jocms_content('cancel');
    });
    $('<input id="jo_delete_check" type="checkbox"/><label for="jo_delete_check">'+jo_lang.AREA_DEL+'</label>').appendTo('#jocms_buttons');

    //highlight editable areas
    $('[data-jo="true"]').each(function(){
      var id = $(this).attr('data-cmsid');
      $(this).addClass('jo_selected');
    });

    $('*[data-cmsid]').each(function(){
      $(this).off();
    });


    //adds class to an element
    //input: element reference, class class_name
    //return: processed element reference
    function element_select(elem_input,class_name){
      $('*[data-cmsid]').removeClass('jo_target');
      if($(elem_input).parents('.jo_selected').length == 0){
        var elem = elem_input;
      }else{
        var elem = $(elem_input).parents('.jo_selected')[0];
      }

      if($(elem).hasClass('jo_selected') == true && $(elem).parent('*[data-cmsid]').length != 0){
        var elem = $(elem).parent();
      }
      $(elem).addClass(class_name);
      $(elem).find('.' + class_name).each(function(){
        $(this).removeClass(class_name);
      });
      return elem;
    }

    var selector = '[data-cmsid]';

    //handles click events on elements
    //input: click element reference
    //return: -
    $('*' + selector).click(function(event){
      event.preventDefault();

      function remove_class(elem){
        if(!$(elem).is('[data-jo-content="repeated"]')){
          $(elem).removeClass('jo_selected');
        }
        $(elem).find('[data-jo-content="repeated"]:not([data-jo-content="repeated"] *)').each(function(){
          element_select(this, 'jo_selected');
        });
      }

      if($('#jo_delete_check').prop('checked')){
        if($(this).is('.jo_selected')){
          remove_class(this);
        }
        $(this).parents('.jo_selected').each(function(){
          remove_class(this);
        });

      }else{
        var elem = element_select(this,'jo_selected');
        element_select(elem,'jo_target');
      }
      event.stopPropagation();
    });

    //handles hover events on elements
    //input: hover element reference
    //return: -
    $('*' + selector).mouseover(function(event){
      if($('#jo_delete_check').prop('checked') == false){
        element_select(this,'jo_target');
        event.stopPropagation();
      }
    });
});


//------------------------------------------------------------------------------------- jocms jo_ajax

//checks if task is still valis
//input: -
//return: -
function jo_taskvalid(){
  var content_complete = {};
  content_complete.url = window.location.href;
  jo_ajax('/jocms/core/inc/taskvalid.php',content_complete,function(e,data,url){});
}

//processes save/cancel jo_action
//input: save/cancel advice
//return: custom callback function
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
        content_complete.sec = [];

        $('.jo_selected').each(function(){
          content_complete.sec.push($(this).attr('data-cmsid'));
        });

    }

    content_complete.url = window.location.href;
    jo_ajax('/jocms/apps/areaselector/inc/save.php',content_complete,function(e,data,url){
        clearInterval(window.jo_interval);
        window.location.replace("/jocms/control/cms.php?path=" + encodeURIComponent(e.redirection));
    },false);
}
