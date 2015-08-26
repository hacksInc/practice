		<div class="sub_content">
			<section class="sub_keyword">
				<h3>＊ 人気のキーワード</h3>
				<ul>
					<li><a href="/projects?Skill=2" alt="PHP">PHP</a></li>
					<li><a href="/projects?Skill=1" alt="Ruby">Ruby</a></li>
					<li><a href="/projects?Skill=4" alt="java">Java</a></li>
					<li><a href="/projects?Skill=8" alt="HTML5/CSS3">HTML5</a></li>
					<li><a href="/projects?Skill=100" alt="Unity">Unity</a></li>
					<li><a href="/projects?Skill=12" alt="JavaScript">JavaScript</a></li>
					<li><a href="/projects?Position=15" alt="PM/PMO">PM/PMO</a></li>
					<li><a href="/projects?Skill=7" alt="Android">Android</a></li>
					<li><a href="/projects?Skill=5" alt="Objective-C">Objective-C</a></li>
					<li><a href="/projects?Position=6" alt="webデザイナー">webデザイナー</a></li>
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
						<p><?php echo h($value['station'])." / ¥".number_format(h($key['MinPrice']['name']))." 〜 ¥".number_format(h($key['MaxPrice']['name'])); ?></p>
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
			<div class="sub_sns">
				<div class="fb-page" data-href="https://www.facebook.com/pages/Connect/1588548634743761" data-width="500" data-height="300" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/pages/Connect/1588548634743761"><a href="https://www.facebook.com/pages/Connect/1588548634743761">Connect</a></blockquote></div></div>
			</div>
		<!-- sub_content --></div>