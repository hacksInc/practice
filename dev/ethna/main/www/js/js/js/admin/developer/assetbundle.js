$(function(){
	$("input[type='file']").change(function (evt) {
	    var files = evt.target.files;
		
		if (!files || !(files.length > 0)) {
			return;
		}
		
		var f = files[0];
		
		var is_error = false;

		var attr_name = $(this).attr('name');
		if (attr_name === 'asset_bundle_android') {
			var splitted = splitFileName(f.name);
			if (!splitted || (splitted.device_name !== 'Android')) {
				is_error = true;
			}
		} else if (attr_name === 'asset_bundle_iphone') {
			var splitted = splitFileName(f.name);
			if (!splitted || (splitted.device_name !== 'iPhone')) {
				is_error = true;
			}
		} else if (attr_name === 'asset_bundle_pc') {
			var splitted = splitFileName(f.name);
			if (!splitted || (splitted.device_name !== '')) {
				is_error = true;
			}
		} else if (attr_name === 'monster_icon') {
			if (strncmp(f.name, 'monster_icon_', strlen('monster_icon_')) !== 0) {
				is_error = true;
			}
		} else if (attr_name === 'monster_image') {
			if (strncmp(f.name, 'monster_image_', strlen('monster_image_')) !== 0) {
				is_error = true;
			}
		}
		
		if (is_error) {
			$(this).next().html('<i class="icon-warning-sign"></i>ファイル名が不正です。');
			$(this).next().css('display', 'block');
		} else {
			$(this).next().css('display', 'none');
		}
	});
	
	/**
	 * ファイル名を分割する
	 * 
	 * @see Jm_AdminAssetbundleManager.php  function splitFileName
	 * @param string $joint_file_name ファイル名
	 * @return array キーが'file_name','version','device_name'の連想配列
	 */
	function splitFileName(joint_file_name)
	{
		var arr = explode('.', joint_file_name);
		if (!arr || (arr.length < 3)) {
			return;
		}
		
		var file_name = arr[0];
		var version   = arr[1];
		
		if (arr.length > 3) {
			var device_name = arr[2];
		} else {
			var device_name = '';
		}
		
		return {
			'file_name'   : file_name,
			'version'     : version,
			'device_name' : device_name
		};
	}
});