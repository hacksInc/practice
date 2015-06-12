<div class="container-fluid" style="witdh: 800px;">

	{foreach from=$app.character_list item="data" key="data_key"}
	<div class="dialog-title">{$app.character_master[$data.character_id].name_ja}</div>
	<div class="content-part-quest-info">
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				犯罪係数
			</div>
			<div class="content-part-quest-info-line-item">
				{$data.crime_coef}
			</div>
		</div>

		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				身体係数
			</div>
			<div class="content-part-quest-info-line-item">
				{$data.body_coef}
			</div>
		</div>

		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				知能係数
			</div>
			<div class="content-part-quest-info-line-item">
				{$data.intelli_coef}
			</div>
		</div>

			<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				心的係数
			</div>
			<div class="content-part-quest-info-line-item">
				{$data.mental_coef}
			</div>
		</div>

			<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				臨時ストレスケア
			</div>
			<div class="content-part-quest-info-line-item">
				{$data.ex_stress_care}
			</div>
		</div>

</div>
	{/foreach}
</div>



