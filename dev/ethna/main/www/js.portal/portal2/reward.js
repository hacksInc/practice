$(function(){

	var list = $('#rewardList li').length;

	for( var i = 1; i <= list; i++ ) {
			
		$('#rewardList li:nth-child(' + i + ')').each( function(){

			var nowPt = $(this).find('.nowPt').text();
			var targetPt = $(this).find('.targetPt').text();
			nowPt = nowPt.split(",").join("");
			targetPt = targetPt.split(",").join("");

			var one = +targetPt * 0.01;

			if ( targetPt == "???????"　)　{

				$(this).find('.fill').css("width","0%");
				$(this).find('.nowPt').text('');
				return false;

			}

			if ( nowPt <= 0 ) {

				$(this).find('.fill').css("width","0%");
				return false;

			} else if ( +nowPt < +one ) {

				$(this).find('.fill').css("width","1%");
				$(this).find('.now').show();
				return false;

			} else if( +nowPt > +one ) {

				if( +nowPt >= +targetPt ) {
					$(this).find('.fill').css("width","100%");
					$(this).find('.nowPt').text(targetPt);
					$(this).find('.clear').show();
					return false;
				}

				var gauge = Math.floor( nowPt/one );
				$(this).find('.fill').css("width", gauge+"%");
				$(this).find('.now').show();
				return false;

			}

			
		});
	}

});