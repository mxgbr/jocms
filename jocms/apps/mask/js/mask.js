$(document).ready(function(e){
    $("a[data-id]").click(function(){
        var id = $(this).attr("data-id");
        content_complete = {};
        content_complete.id = id;
        jo_ajax('/jocms/apps/mask/inc/mask.php',content_complete,function(e,data,url){
            $("#name").val(e.name);
            $('.CodeMirror')[0].CodeMirror.setValue(e.code);
            $("#id").attr("value", e.id);
            $("#delete").show();
            $("#delete").attr("href","/jocms/apps/mask/mask.php?deleted=true&id=" + e.id);
            $(".list_active").removeClass("list_active");
            $("a[data-id='" + e.id +"']").addClass("list_active");
        });
    });
    $(".mask_list .jo_btn").click(function(){
        $("#name").val("");
        $('.CodeMirror')[0].CodeMirror.setValue("");
        $("#id").attr("value", 0);
        $("#delete").hide();
        $(".list_active").removeClass("list_active");
    });
});
