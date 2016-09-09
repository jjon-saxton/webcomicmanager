$(function(){
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
    
    // Uninitialize modal on close
    $('body').on('hidden.bs.modal','.modal',function(){
        $(this).removeData('bs.modal');
    });
});