		<div class="sub_content">
			<div class="sub_sns">
			</div>
			<section class="sub_keyword">
				<h3>＊ 人気のキーワード</h3>
				<ul>
					<li><a href="/projects?Skill=1" alt="PHP">PHP</a></li>
					<li><a href="/projects?Skill=2" alt="Ruby">Ruby</a></li>
					<li><a href="/projects?Skill=3" alt="java">Java</a></li>
					<li><a href="/projects?Skill=6" alt="HTML5/CSS3">HTML5/CSS3</a></li>
					<li><a href="/projects?Skill=11" alt="Unity">Unity</a></li>
					<li><a href="/projects?Skill=7" alt="JavaScript">JavaScript</a></li>
					<li><a href="/projects?Skill=12" alt="Swift">Swift</a></li>
					<li><a href="/projects?Skill=11" alt="Android">Android</a></li>
					<li><a href="/projects?Skill=10" alt="Objective-C">Objective-C</a></li>
					<li><a href="/projects?Position=7" alt="webデザイナー">webデザイナー</a></li>
				</ul>
			</section>
			<section class="sub_project">
				<h3>＊ 人気の案件</h3>
				<ul>
					<?php
						foreach($sub_project as $key) :
							$value = $key['Project'];
					?>
					<li>
					<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
						<h4><?php echo h($value['title']); ?></h4>
						<p><?php echo h($value['station']); ?> / ¥<?php echo h($value['min_price']); ?>〜¥<?php echo h($value['max_price']); ?></p>
						<p>
						<?php
							for( $i=0; $i < count($key['Skill']);$i++) {
								echo h($key['Skill'][$i]['name']).'/';
							}
						?>
						</p>
					</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</section>
			<div class="sub_partner">
				<img src="img/partner.png" width="100%">
			</div>
		<!-- sub_content --></div>