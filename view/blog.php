<?php
include "../config/dbconfig.php";

function blogExcerpt(string $body, int $limit = 200): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($body)));
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit - 1)) . '...';
    }

    if (strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(substr($text, 0, $limit - 1)) . '...';
}

function blogDate(string $rawDate): string
{
    $rawDate = trim($rawDate);
    if ($rawDate === '') {
        return '';
    }

    $formats = ['d/m/Y h:i:s', 'd/m/Y H:i:s', 'Y-m-d H:i:s', 'Y-m-d'];
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $rawDate);
        if ($date instanceof DateTime) {
            return $date->format('M j, Y');
        }
    }

    $timestamp = strtotime($rawDate);
    if ($timestamp !== false) {
        return date('M j, Y', $timestamp);
    }

    return $rawDate;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$post = null;

if ($id > 0) {
    $stmt = mysqli_prepare($con, "SELECT id, title, subtitle, img, body, date FROM posts WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result) ?: null;
}

if ($post) {
    $title = htmlspecialchars($post['title']) . ' | ' . SITE_NAME;
} else {
    $title = 'Blog | ' . SITE_NAME;
}

include "includes/header.php";

$latest = [];
$latestSql = "SELECT id, title, subtitle, img, body, date FROM posts ORDER BY STR_TO_DATE(date, '%d/%m/%Y %h:%i:%s') DESC, id DESC LIMIT 4";
$latestRes = mysqli_query($con, $latestSql);
if ($latestRes) {
    while ($row = mysqli_fetch_assoc($latestRes)) {
        if ($post && (int)$row['id'] === (int)$post['id']) {
            continue;
        }
        $latest[] = $row;
    }
}

$heroMedia = '';
if ($post) {
    $heroMedia = propertyMediaSrc($post['img'] ?? '', '');
    if ($heroMedia === '') {
        $heroMedia = 'assets/img/archmate-logo.png';
    }
}
?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="section-label"><div class="section-label-line"></div><span>Blog</span></div>
      <?php if ($post): ?>
        <h1 class="section-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="page-hero-sub">
          <?php echo htmlspecialchars($post['subtitle'] ?? ''); ?>
        </p>
      <?php else: ?>
        <h1 class="section-title">Latest <em>Stories</em> and Updates</h1>
        <p class="page-hero-sub">
          Read the newest announcements, highlights, and behind-the-scenes notes from Arch-Mate.
        </p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($post): ?>
  <section style="padding-top:0;background:#fff">
    <div class="container">
      <div class="blog-detail-hero">
        <div class="blog-detail-media">
          <img src="<?php echo htmlspecialchars($heroMedia); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="eager">
        </div>
        <div class="blog-detail-meta">
          <?php $date = blogDate((string)($post['date'] ?? '')); ?>
          <?php if ($date !== ''): ?>
            <div class="section-label" style="margin-bottom:1rem;">
              <div class="section-label-line"></div><span><?php echo htmlspecialchars($date); ?></span>
            </div>
          <?php endif; ?>
          <h2 class="blog-detail-title"><?php echo htmlspecialchars($post['title']); ?></h2>
          <?php if (!empty($post['subtitle'])): ?>
            <p class="blog-detail-subtitle"><?php echo htmlspecialchars($post['subtitle']); ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section style="background:var(--offwhite)">
    <div class="container blog-layout">
      <article class="blog-content">
        <?php echo !empty($post['body']) ? $post['body'] : '<p>More details will be shared soon.</p>'; ?>
      </article>

      <aside class="blog-sidebar">
        <div class="blog-sidebar-card">
          <div class="section-label"><div class="section-label-line"></div><span>Latest</span></div>
          <h3 class="blog-sidebar-title">Recent posts</h3>
          <div class="blog-recent-list">
            <?php if (!empty($latest)): ?>
              <?php foreach ($latest as $item): ?>
                <?php
                  $thumb = propertyMediaSrc($item['img'] ?? '', '');
                  if ($thumb === '') {
                      $thumb = 'assets/img/archmate-logo.png';
                  }
                  $itemDate = blogDate((string)($item['date'] ?? ''));
                ?>
                <a href="./blog?id=<?php echo urlencode($item['id']); ?>" class="blog-recent-item">
                  <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                  <div>
                    <?php if ($itemDate !== ''): ?>
                      <span><?php echo htmlspecialchars($itemDate); ?></span>
                    <?php endif; ?>
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                    <p><?php echo htmlspecialchars(blogExcerpt((string)($item['body'] ?? ''), 90)); ?></p>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="blog-empty-note">No recent posts yet.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="blog-sidebar-card blog-sidebar-card-dark">
          <div class="blog-sidebar-eyebrow">Stay updated</div>
          <h3 class="blog-sidebar-title">See everything we publish</h3>
          <p>Browse the full post feed for all recent updates and announcements.</p>
          <a href="./posts" class="btn-primary" style="display:inline-flex;align-items:center;justify-content:center;">All Posts</a>
        </div>
      </aside>
    </div>
  </section>
<?php else: ?>
  <section style="background:#fff">
    <div class="container">
      <div class="section-header-row reveal">
        <div>
          <div class="section-label"><div class="section-label-line"></div><span>Latest</span></div>
          <h2 class="section-title">Recent <em>stories</em></h2>
        </div>
        <a href="./posts" class="view-all">View all posts &rarr;</a>
      </div>

      <?php if (!empty($latest)): ?>
        <div class="blog-feature-grid">
          <?php foreach ($latest as $index => $item): ?>
            <?php
              $thumb = propertyMediaSrc($item['img'] ?? '', '');
              if ($thumb === '') {
                  $thumb = 'assets/img/archmate-logo.png';
              }
              $itemDate = blogDate((string)($item['date'] ?? ''));
              $excerpt = blogExcerpt((string)($item['body'] ?? ''), 140);
            ?>
            <a href="./blog?id=<?php echo urlencode($item['id']); ?>" class="blog-feature-card<?php echo $index === 0 ? ' is-featured' : ''; ?>">
              <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
              <div class="blog-feature-overlay">
                <?php if ($itemDate !== ''): ?>
                  <span class="blog-feature-date"><?php echo htmlspecialchars($itemDate); ?></span>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <?php if (!empty($item['subtitle'])): ?>
                  <p class="blog-feature-subtitle"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                <?php endif; ?>
                <?php if ($excerpt !== ''): ?>
                  <p class="blog-feature-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                <?php endif; ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="padding:4rem 0;text-align:center;color:var(--text-muted);">No posts have been published yet.</div>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
