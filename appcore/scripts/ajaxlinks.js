$(function(){
    $(':file').on('fileselect',function(event,numFiles,label){
        var input=$(this).parents('.input-group').find(':text'),
                  log=numFiles > 1 ? numFiles + ' files selected' : label;
                  
        if (input.length){
            input.val(log);
        } else {
            if ( log) alert(log);
        }
        
        iUpload(this);
    });
    
    $("[data-target='#this-modal']").click(function(e){
        e.preventDefault();
        var loc=$(this).attr("href");
        $("div.modal.in .modal-content").load(loc);
    })
    
    $("button[data-target='#messageModal']").click(function(e){
        e.preventDefault();
        var data=$('form').serialize();
        var url=$('form').attr('action');
        if ($('form').attr('method') == 'post') {
            $.post(url,data,function(html){
                $("div.modal.in").modal('hide');
                $("div#messageModal .modal-content").html(html);
                $("div#messageModal").modal('show');
            });
        }
        else {
            $.get(url); //serilize data as query string?
        }
    });
    
    $("form.autoSave textarea").before("<div class=\"form-group\"><input type=\"checkbox\" id=\"saveOn\" checked=\"checked\"><label for=\"saveOn\"> Auto Save?</label></div>");
    
    // Initialize textarea's with required class as JQTE boxes
    $("textarea.editor.full").jqte({change:function(){ autoSave(); }});
    $("textarea.editor.limited").jqte({
        formats:false,
        link:false,
        unlink:false,
        ul:false,
        ol:false,
        strike:false,
        outdent:false,
        indent:false,
        rule:false,
        change:function(){
            autoSave();
        },
    });
    // Initialize textareas with required class as Comix Editors
    $("textarea.editor.script").comixeditor();
    
    $("textarea, input[type=text], input[type=number]").keyup(function(){
        autoSave();
    });
    
    $("#saveOn").click(function(){
        if($(this).is(':checked')){
            $("form.manager").addClass("autoSave");
        }
        else{
            $("form.manager").removeClass("autoSave");
        }
    });
     
    function autoSave(){
        var data=$('form.autoSave').serialize();
        var url=$('form.autoSave').attr('action')+"&json=1";
        var timeoutID;
        
        if ($("form.autoSave").length){
         if (timeoutID){
            clearTimeout(timeoutID);
         }
        
         timeoutID=setTimeout(function(){
            $("form.autoSave div .btn-primary").attr('disabled','disabled').text('Saving...');
            $.post(url,data,function(json){
                if (json.okay){
                    console.log(json.message);
                    if ($("#autoSaveError").length){
                        $("#autoSaveError").remove();
                    }
                    $("form.autoSave div button.btn-primary").text("Saved!").delay(150).removeAttr('disabled').text('Save');
                }
                else{
                    if ($("#autoSaveError").length){
                        $("#autoSaveError").text(json.message);
                    }
                    else{
                        $("form.autoSave div button.btn-primary").parent().prepend("<div id=\"autoSaveError\" class=\"alert alert-warning\">"+json.message+"</div>");
                    }
                    $("form.autoSave div button.btn-primary").removeAttr('disabled').text('Save');
                }
            },'json');
         },9000);
        }
    };
    
    $(".jqte_toolbar").addClass("btn-toolbar");
    $(".jqte_toolbar .jqte_tool").each(function(){
        $(this).addClass("btn btn-info").css('padding','1px');
    });
    
    // Uninitialize modal on close
    $('body').on('hidden.bs.modal','.modal',function(){
        $(this).removeData('bs.modal');
    });
});

function iUpload(field){
    var ext=/\.png|\.jpg|\.jpeg|\.svg/i;
    var filename=field.value;
    
    if (filename.search(ext) == -1){
        $("#AJAXModal").modal('hide');
        $("div#messageModal .modal-title").html("Upload Error");
        $("div#messageModal .modal-body").html("You must uploaded a supported image file! (.png, .jpg, .jpeg, or .svg)");
        $("#messageModal").modal('show');
        
        field.form.reset();
        return false;
    }
    
    $("div#art .progress-bar span").text("33%");
    $("div#art .progress-bar").attr('aria-valuenow','33').css('width','33%');
    $("div#art").removeClass('no-show');
    field.form.submit();
}