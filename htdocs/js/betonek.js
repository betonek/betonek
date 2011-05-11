/*
 * Betonek
 */


var obj_to_str = function(obj){
    var ret = [];
    for(var key in obj)
        ret.push(key + '=' + obj[key]);
    return ret.join(';');
};

var callbacks = {
    "search": function(data){
        for(var i=0;i<data.titles.length;++i){
            GUI.add_item(data.titles[i]);
        }
    },
    "order": function(data){
        GUI.status_changed("order", data.item_id);
    }
};

var callback_dispatcher = function(data, method){
	callbacks[method](data);
};

var B = {
	rpc: function(method, args, cb, errcb)
	{
		return $.rpc(method, args, callback_dispatcher, errcb ? errcb : function(err)
		{
			alert("Błąd rpc " + method + " nr " + err.code + ": " + err.message);
		});
	},

	/*****************************************/
	init: function()
	{
		/* select first input box */
		$("form:first input:first").focus();

		$("form span.searchbutton").click(function()
		{
			$(this).parent().submit();
		});
	},

	init_search: function(query)
	{
        var $menu = $("#sw_menu"), $book = $("#sw_book");
		B.search(query, $menu, $book );
        GUI.init($menu, $book);
	},

	/*****************************************/
	search: function(what, $menu, $book)
	{
        B.rpc("search", { query: what });	
    }
};



// returns new jQuery object representing DOM element and adds given content to it
create_simple_row_creator = function(){
    return function(content){
        return $("<div>").append(content); 
    };
};

// returns function that when called returns jQuery object with overloaded append method
// each time append method on object returned by calling returned functio will be called, then it's arguments will be mapped 
// via given row_creator function
// this function provides default values for all arguments
var create_row_container_creator = function(row_creator, DOM_type, mapped_functions){
    row_creator = row_creator || create_simple_row_creator();
    DOM_type = DOM_type || '<div>';
    mapped_functions = mapped_functions || ['append', 'prepend'];// TODO: implement

    return function(){
            var $ret = $(DOM_type);
            var old_append = $ret.append;

            $ret.append = (function($orig, fnct){
                return function(){
                    return fnct.apply($orig, $.map(arguments, row_creator));
                };
            })($ret, old_append);

            return $ret;
    };
};

// factory of presenter objects
// each value of presenters object is a function, that takes one argument - data containing information about given item
// that function returns <span> element representing rendered representation of given item
big_presenters = function(cc, id_prefix){

        this.__id_prefix = id_prefix || 'big_';

        this.__get_id = function(id){
            return this.__id_prefix + id;
        };

        this.__cc = cc || create_row_container_creator();

        this._cc = function(id){
            var $ret = this.__cc();
            $ret.attr('id', this.__get_id(id))
            .data('sq', id);
            return $ret;
        };

        this.get_item = function(id){
            return $("#" + this.__get_id(id));
        };


        // should be overriten by subclasses
        this.__book = function($ret, title, author, id){
            return $ret.append( "Tytuł: " + title ).
                append( "Autor: " + author ).append($("<button>Wypożycz</button>").click(function(){
                    var sq = $(this).parent().parent().data('sq');
                    B.rpc("order", {'item_id': sq});
                }));
        };

        this.book = function(data){
            var title = data["title"], id = data['id'], author = data['author'];
            return this.__book(this._cc(id), title, author, id);
        };
        return true;
};

small_presenters = function(cc, main_presenter){
    this.__id_prefix = 'small_';
    this.__cc = cc || 
        create_row_container_creator(function(content){
            return $("<span>").
                css({"background":"green", "color":"white"}).
                click(function(){
                    var sq = $(this).parent().data('sq');
                    main_presenter.get_item(sq).toggle();
                }).append(content);
        });

    this.__book = function($ret, title, author, book){
        return $ret.append("TYTOL: " + title);
    };

};
small_presenters.prototype = new big_presenters();


GUI = new (function(){
    this.controls = {
        $menu: undefined,
        $book: undefined
    };

    this.menu_presenters = new big_presenters();
    this.small_presenters = new small_presenters(undefined, this.menu_presenters);

    this.init = function($menu, $book){
        this.controls.$menu = $menu;
        this.controls.$book = $book;
    };

    this.__status_change_handlers = {
        "order": function(sq, extra){
            this.menu_presenters.get_item(sq).find("button").hide();
        }
    };

    this.status_changed = function(change_type, sq){
        // remove change_type from arguments
        var arr = [];for(var i=0;i<arguments.length;++i)arr[i] = arguments[i];arr = arr.slice(1);
        this.__status_change_handlers[change_type].apply(this, arr);
    };

    this.add_item = function(info){
        var type = info['type'];

        var $menu_el = GUI.menu_presenters[type](info);
        this.controls.$menu.append($menu_el);

        var $book_el = this.small_presenters[type](info);
        this.controls.$book.append($book_el);
    };
    return true;
})();
