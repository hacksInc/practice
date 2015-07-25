$(function(){

	var	tab = $('.search_panel_tab'),
		panel = $('.search_panel_select');

	if(!navigator.userAgent.match(/(iPhone|iPad|Android)/)){

		$('.header_menu_list, .header_menu_list_detail').hover(hover_menu);

		$(document).on('click', '.header_menu_toggle', menu_toggle);

	    // タブ切り替え
		$(document).on('click', '.search_panel_tab', tab_change);

	    // checkbox, radioをチェックした際の処理
		$(document).on('change','.search_panel_box input', search_panel);

	    // selected_itemに表示されている文字をクリックした時の処理
	    $(document).on('click', '.selected_item span', delete_item);

	    // すべて消す処理
		$(document).on('click', '.selected_item_all_delete', all_delete);

	} else {

		var windowH = $(window).height(),
			topimage = $('.topimage');			
	
		topimage.css({height: windowH * 0.45});

		$(document).on('touchstart', '.header_menu_toggle', menu_toggle);

	    // タブ切り替え
		$(document).on('touchstart', '.search_panel_tab', tab_change);

	    // checkbox, radioをチェックした際の処理
		$(document).on('change','.search_panel_box input', search_panel);

	    // selected_itemに表示されている文字をクリックした時の処理
	    $(document).on('touchstart', '.selected_item span', delete_item);

	    // すべて消す処理
		$(document).on('touchstart', '.selected_item_all_delete', all_delete);
	}


	function tab_change() {
		var num = tab.index(this);
		tab.removeClass('on');
		$(this).addClass('on');
		panel.hide();
		panel.eq(num).show();
		
	}

	function search_panel() {
		var id = $(this).attr('id'),
			label = $('label[for="'+id+'"]').text(),
			item = '<span value="'+id+'">'+label+' ×</span>';

        // priceがクリックされた場合、一旦priceを初期化
		if ( item.indexOf('price') != -1 ) {
			$(".selected_item span").each(function(){
				if( $(this).attr('value').indexOf('price') != -1 && !$(this).attr('value').match(id) ){
					$(this).remove();
				}
			});
		}
		
		if( $(this).prop('checked') ) {
		
			$(item).appendTo('.selected_item').hide().fadeIn();

		} else {

			$(".selected_item span").each(function(){
                if( $(this).attr('value').match(id) ){
                    $(this).remove();
                }
			});
		}
		check();
	}

    function delete_item() {

    	var key = $(this).attr('value');
        $(this).remove();
        $('.search_panel_box input').each(function(){
            if( $(this).attr('id') == key ) {
                $(this).prop('checked', false);
            }
            check();
        });
    }

	function all_delete() {

		$(".selected_item span").remove();
		$('.search_panel_box input').prop('checked', false);
		check();
	}

	function hover_menu() {
		$(this).find('.header_menu_list_detail').stop().slideToggle();
	}


	function menu_toggle(event) {
		event.preventDefault();
		$('.header_menu').slideToggle();
	}

    // 値の有無で表示を変える処理
	function check(){
		if ($(".selected_item span").length == 0) {
			$('.selected_item_default').fadeIn();
			$(".selected_item_all_delete").hide();
		} else {
			$('.selected_item_default').hide();
			$(".selected_item_all_delete").fadeIn();
		}
		return;
	}
});



