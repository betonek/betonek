var config_search_box = function(elem, url, printer){
    printer = printer || function(obj){ return obj; };
    var $cont = $(elem);
    var $res = $cont.find('[name=result]').eq(0),
        $txt_query = $cont.find('[name=query]').eq(0),
        $err = $cont.find('[name=error]').eq(0);
    var default_val = "None objects returned";
    var indicate_no_result = function(){ $res.children().remove(); $res.append($("<span>").html(default_val)); },
        clear_error = function(){ $err.html(''); };

    indicate_no_result();

    var on_change = function(){
        var val = $.trim( $txt_query.val() );
        if(!val.length){
            $err.html('trimmed search criteria have length==0');
            indicate_no_result();
        } else {
            clear_error();
            $.rpc(url, { 'query': $txt_query.val() },
               function(data){
                   $res.children().remove();
                   console.log(data);
                   objs = data.objects;
                   if(objs.length){
                        for(var i=0;i<objs.length;++i)
                           $res.append( printer(objs[i]) );
                   } else {
                       indicate_no_result();
                   }
               });
        }
    };

    $txt_query.keyup(on_change);
}

$(document).ready(function(){
    $("#search_box [name=query]").autocomplete({
        source: function(request, response){
            $.rpc("search_authors", { 'query': request.term },
               function(data){
                    objs = $.map(data.objects, function(obj){ return $.extend(obj, {'label': obj.author_name });} );
                    response(objs);
            });
        },
        select: function(event, ui){
            $("#add [name=author_id]").val(ui.item.author_id);
        },
    });

    $("#btn_choose_author").click(function(){
        $('#search_box').toggle();
        return false;
    });

    $("form").submit(function(){
        var add = function(author_name, author_id){
            var data  = { 'title': $("#add [name=title]").eq(0).val() };

            if(author_id) 
                author_data = {'author_id': author_id};
            else 
                author_data = {'author_name': author_name};

            $.extend(data, author_data);
            var method = author_id && 'add_item_author_id' || 'add_item_author';
            
            $.rpc(method, data, function(){
                console.log(arguments[0]);
                $("#add input").val('');
//                alert('added succesfully. add next?');
            });
        };

        var author_id = $("#add [name=author_id]").eq(0).val(), 
            author_name = $("#add [name=author]").eq(0).val();

        add(author_name, author_id);
        return false;
    });
});
