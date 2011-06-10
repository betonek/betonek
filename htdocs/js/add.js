$(document).ready(function(){
    var selected_author = undefined; // represents selected author from those in database
    var proposed_auhors = undefined;

    // called to indicate that no author from databse is choosen as author of added item
    var set_no_selected_author_id = function(){
        selected_author = undefined;
        $("#add [name=author_id]").val(undefined);
    }

    $("#search_box [name=query]").autocomplete({
        source: function(request, response){
            $.rpc("author_search", { 'query': request.term },
               function(data){
                    proposed_auhors= $.map(data.authors, function(obj){ return $.extend(obj, {'label': obj.author });} );
                    if(!proposed_auhors.length)
                        proposed_auhors = [{'label': "No elements matching criteria: " + request.term, "REPRESENTS_NULL": true}];
                    response(proposed_auhors);
            });
        },
        select: function(event, ui){
            if(proposed_auhors.length == 1 && proposed_auhors[0].REPRESENTS_NULL){
                alert("no authors found found");
                event.preventDefault();
                return;
            }
            selected_author = ui.item;
        },
    });

    //TODO: use jQuery's template plugin
    $("#search_box").hide().find('button[name=submit]').click(function(){
        if(selected_author){
            // ordering of setting tese fields is important, because of change handler added
            // to $("#add [name=author")
            $("#add [name=author]").val(selected_author.author_name);
            $("#add [name=author_id]").val(selected_author.author_id);
        }
        $(this).parent().dialog("close");
    });

    $("#btn_choose_author").click(function(){
        set_no_selected_author_id();
        $("#search_box").dialog({'modal': true});
    });

    $("form input[name=author]").change(set_no_selected_author_id).// change handler is added because of usage of copy-paste into input
        keyup(set_no_selected_author_id).
        keydown(set_no_selected_author_id);

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
//                alert('added succesfully. add next?');// inform that item was addes succesfully, and ask whether to add next item
            });
        };

        var author_id = $("#add [name=author_id]").eq(0).val(), 
            author_name = $("#add [name=author]").eq(0).val();

        add(author_name, author_id);
        return false;
    });
});
