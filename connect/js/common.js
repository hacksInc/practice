
// 検索フォームの処理
$(function() {

    // タブ切り替え
	$(document).on('click', '.search_panel_tab', function() {

        var tab = $('.search_panel_tab');
        var panel = $('.search_panel_select');
		var num = tab.index(this);

		tab.removeClass('on');
		$(this).addClass('on');
		panel.removeClass('select');		
		panel.eq(num).addClass('select');

	});

    // checkbox, radiowをチェックした際の処理
	$(document).on('change','.search_panel_box :input', function(){

		var id = $(this).attr('id');
		var label = $('label[for="'+id+'"]').text();
		var item = '<span value="'+id+'">'+label+' ×</span>';

        // priceがクリックされた場合、一旦priceを初期化
		if ( item.indexOf('price') != -1 ) {
			$(".selected_item span").each(function(){
				if( $(this).attr('value').indexOf('price') != -1 && !$(this).attr('value').match(id) ){
					$(this).remove();
				}
			});
		}

		if( $(this).prop('checked') ) {
		
			$('.selected_item').append(item);

		} else {

			$(".selected_item span").each(function(){
                if( $(this).attr('value').match(id) ){
                    $(this).remove();
                }
			});
		}
		check();
	});

    // selected_itemに表示されている文字をクリックした時の処理
    $(document).on('click', '.selected_item span', function(){

        var key = $(this).attr('value');
        $(this).remove();
        $('.search_panel_box :input').each(function(){
            if( $(this).attr('id').match(key) ) {
                $(this).prop('checked', false);
            }
        });
        
    });

    // すべて消す処理
	$(document).on('click', '.selected_item_all_delete', function(){
		$(".selected_item span").remove();
		$('.search_panel_box :input').prop('checked', false);
		check();
	});

    // 値の有無で表示を変える処理
	function check(){

		if ($(".selected_item span").length == 0) {
			$('.selected_item_default').show();
			$(".selected_item_all_delete").hide();
		} else {
			$('.selected_item_default').hide();
			$(".selected_item_all_delete").show();
		}
		return;
	}
});

// ヘッダーメニューの処理
$(function(){
	$('.header_menu_list, .header_menu_list_detail').hover(function(){
		$(this).find('.header_menu_list_detail').stop().slideDown();
	}, function(){
		$(this).find('.header_menu_list_detail').stop().slideUp();
	});
});

$(function(){
	$(document).on('click', '.search_panel_open', function(){
		$('.search_panel_open').toggleClass('active');
		if($('.search_panel_open').hasClass('active')) {
			$('.search_panel_open').text('× パネルを閉じる');
			$('.search').slideDown();
		} else {
			$('.search_panel_open').text('検索条件を変更する');
			$('.search').slideUp();
		}
	});
});
