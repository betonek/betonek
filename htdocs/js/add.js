$(document).ready(function(){
    $("#search_box [name=query]").autocomplete({
        source: function(request, response){
            $.rpc("search_authors", { 'query': request.term },
               function(data){
                    objs = $.map(data.objects, function(obj){ return $.extend(obj, {'label': obj.author_name });} );
                    if(!objs.length)
                        objs = [{'label': "No elements matching criteria: " + request.term, "REPRESENTS_NULL": true}];
                    response(objs);
            });
        },
        select: function(event, ui){
            if(objs.length == 1 && objs[0].REPRESENTS_NULL){
                alert("no authors found found");
                event.preventDefault();
                return;
            }
            $("#add [name=author_id]").val(ui.item.author_id);
            $("#add [name=author]").val(ui.item.author_name);
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
