
<textarea>
<?php

$i = 0;
foreach ($post as $key => $value) {

		echo $value.',';
		$i++;
		if(($i % 30) == 0){
			echo "</textarea><br><br><textarea>";
		}
	
}
?>
</textarea>