
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<url>
  <loc>https://connect-job.com/</loc>
  <priority>1.0</priority>
  <lastmod><?php echo date(DATE_W3C, strtotime($home['Project']['modified'])); ?></lastmod>
</url>
<?php foreach ($projects as $key) : ?>
    <url>
        <loc>https://connect-job.com/projects/<?php echo h($key['Project']['id']); ?></loc>
        <lastmod><?php echo date(DATE_W3C, strtotime($key['Project']['modified'])); ?></lastmod>
        <priority>0.9</priority>
    </url>
<?php endforeach; ?>
<?php foreach ($skills as $key) : ?>
    <url>
        <loc>https://connect-job.com/projects/projects?Skill=<?php echo h($key['Skill']['id']); ?></loc>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
<?php foreach ($positions as $key) : ?>
    <url>
        <loc>https://connect-job.com/projects/projects?Positions=<?php echo h($key['Position']['id']); ?></loc>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
<url>
  <loc>https://connect-job.com/Projects</loc>
  <priority>0.7</priority>
  <lastmod><?php echo date(DATE_W3C, strtotime($home['Project']['modified'])); ?></lastmod>
</url>
<url>
  <loc>https://connect-job.com/keeps</loc>
  <priority>0.7</priority>
</url>
<url>
  <loc>https://connect-job.com/members</loc>
  <priority>0.7</priority>
</url>
</urlset>