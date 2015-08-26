$(function(){

	var tab = $('.search_panel_tab'),
		panel = $('.search_panel_select'),
		headerH = $('.header').height();

	checkInput();
	$('.main').css({'padding-top': headerH});

	$(document).on('touchstart', '.header_menu_toggle', menu_toggle);

	$(document).on('touchstart', '.keep_delete', keep_delete);

	$(document).on('touchstart', '.keep_all_delete', keep_all_delete);

    // タブ切り替え
	$(document).on('touchstart', '.search_panel_tab', tab_change);

    // checkbox, radioをチェックした際の処理
	$(document).on('change','.search_panel_box input', search_panel);

    // selected_itemに表示されている文字をクリックした時の処理
    $(document).on('touchstart', '.selected_item span', delete_item);

    // すべて消す処理
	$(document).on('touchstart', '.selected_item_all_delete', all_delete);
	
	$(document).on('touchstart', '.search_panel_open', open_panel);

	function scroll_sidebar() {
		var	windowH = $(this).height(),
			scrollTop = $(this).scrollTop(),
			scrollBottom = scrollTop + windowH,
			targetBottom = targetTop + targetH,			
			footerTop = footer.offset().top,
			mainH = $('.main_content').outerHeight();

		if ( mainH > targetBottom ){
			if ( scrollBottom > targetBottom ) {

				if ( scrollBottom > footerTop ) {

					target.css({width: targetW, position: 'fixed', left: targetLeft, bottom: (scrollBottom - footerTop)});

				} else {

					target.css({width: targetW, position:'fixed', left: targetLeft, bottom: 0});
				}

			} else {

				target.css({width: targetW,position: 'static', left: targetLeft, bottom: 'auto'});
			}
		} else {
			target.css({width: targetW,position: 'static', left: targetLeft, bottom: 'auto'});
		}
	}
	
	function tab_change() {
		var num = tab.index(this);
		tab.removeClass('on');
		$(this).addClass('on');
		panel.hide();
		panel.eq(num).fadeIn();
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

	function open_panel() {
		$('.search_panel_open').toggleClass('active');
		if($('.search_panel_open').hasClass('active')) {
			$('.search_panel_open').text('× パネルを閉じる');
			$('.search').slideDown();
		} else {
			$('.search_panel_open').text('検索条件を変更する');
			$('.search').slideUp();
		}
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
	

	function checkInput() {

		$('.search_panel_box input').each(function(){

			if ($(this).prop('checked')) {

				var id = $(this).attr('id'),
					label = $('label[for="'+id+'"]').text(),
					item = '<span value="'+id+'">'+label+' ×</span>';

				$(item).appendTo('.selected_item').hide().fadeIn();
			}
		});
		check();
	}

	function keep_delete(){

		var project_id = $(this).attr('value');
		 $.ajax({
            url: "/keeps/delete",
            type: "POST",
            dataType: "json",
            data: { name : project_id },
            success : function(data){
            	if ( data.keep_count <= 0 ) {
            		location.reload();
            	} else {
	            	var button = $('.keep_delete[value='+project_id+']');
	            	var hidden = $('.entry_id[value='+project_id+']');
	            	button.closest('section').remove();
	            	hidden.remove();
	            	$('.keep_count').text('('+data.keep_count+'件)');
            	}
	        },
        });
	}

	function keep_all_delete() {

		if ( !confirm('気になるリストから案件をすべて削除します。\nよろしければ「OK」を押して下さい。') ){
			return false;
		} else {
			$.ajax({
	            url: "/keeps/all_delete",
	            dataType: "json",
	            success : function(data){
	            	$('section').remove();
	            	$('.keep_count').text('('+data.keep_count+'件)');
	            	location.reload();
		        },
	        });
		}
	}
});



