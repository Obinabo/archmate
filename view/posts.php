<?php
include "../config/dbconfig.php";
include "includes/header.php";

function postExcerpt(string $body, int $limit = 180): string
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

function formatPostDate(string $rawDate): string
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
?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="section-label"><div class="section-label-line"></div><span>Updates</span></div>
      <h1 class="section-title">Latest <em>Posts</em> and Announcements</h1>
      <p class="page-hero-sub">
        Read the latest Arch-Mate updates, highlights, and stories from across the business.
      </p>
    </div>
  </div>
</section>

<?php
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = '';

if ($q !== '') {
    $qe = mysqli_real_escape_string($con, $q);
    $where = "WHERE title LIKE '%$qe%' OR subtitle LIKE '%$qe%' OR body LIKE '%$qe%'";
}

$sql = "SELECT id, title, subtitle, img, body, date
        FROM posts
        $where
        ORDER BY STR_TO_DATE(date, '%d/%m/%Y %h:%i:%s') DESC, id DESC";

$res = mysqli_query($con, $sql);
$allRows = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allRows[] = $row;
    }
}

$count = count($allRows);
?>

<section style="background:#fff">
  <div class="container">
    <div class="section-header-row reveal">
      <div>
        <div class="section-label"><div class="section-label-line"></div><span>Feed</span></div>
        <h2 class="section-title">Browse all <em>posts</em></h2>
      </div>
      <p class="page-hero-sub" style="max-width:520px;margin:0;">
        Search by title, subtitle, or body copy to quickly find a post.
      </p>
    </div>

    <form method="get" class="props-filter" style="justify-content:space-between;gap:1rem;flex-wrap:wrap;">
      <input
        type="search"
        name="q"
        value="<?php echo htmlspecialchars($q); ?>"
        placeholder="Search posts..."
        style="flex:1;min-width:220px;border:1px solid rgba(10,30,50,.12);border-radius:999px;padding:.9rem 1.2rem;background:#fff;color:var(--text);font:inherit;"
      />
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <button class="active" type="submit">Search</button>
        <?php if ($q !== ''): ?>
          <a href="./posts" class="active" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>

<section style="background:var(--offwhite)">
  <div class="container">
    <?php if ($count > 0): ?>
      <div class="props-all-grid">
        <?php foreach ($allRows as $row): ?>
          <?php
            $thumbSrc = propertyMediaSrc($row['img'] ?? '', '');
            if ($thumbSrc === '') {
                $thumbSrc = 'assets/img/archmate-logo.png';
            }
            $title = trim((string)($row['title'] ?? 'Untitled post'));
            $subtitle = trim((string)($row['subtitle'] ?? ''));
            $body = (string)($row['body'] ?? '');
            $excerpt = postExcerpt($body);
            $date = formatPostDate((string)($row['date'] ?? ''));
          ?>
          <a href="./blog?id=<?php echo urlencode($row['id']); ?>" class="prop-card" style="text-decoration:none;display:block;">
            <img src="<?php echo htmlspecialchars($thumbSrc); ?>" alt="<?php echo htmlspecialchars($title); ?>" loading="lazy" />
            <div class="prop-overlay" style="align-items:flex-start;text-align:left;gap:.4rem;">
              <?php if ($date !== ''): ?>
                <div class="prop-location"><?php echo htmlspecialchars($date); ?></div>
              <?php endif; ?>
              <div class="prop-name"><?php echo htmlspecialchars($title); ?></div>
              <?php if ($subtitle !== ''): ?>
                <div style="color:rgba(255,255,255,.88);font-size:.95rem;line-height:1.5;">
                  <?php echo htmlspecialchars($subtitle); ?>
                </div>
              <?php endif; ?>
              <?php if ($excerpt !== ''): ?>
                <div style="color:rgba(255,255,255,.78);font-size:.9rem;line-height:1.55;">
                  <?php echo htmlspecialchars($excerpt); ?>
                </div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div style="padding:5rem 0;text-align:center;color:var(--text-muted);font-family:var(--serif);font-style:italic;font-size:1.4rem;">
        No posts match your search<?php echo $q ? ' for "' . htmlspecialchars($q) . '"' : ''; ?>.
        <br>
        <a href="./posts" style="color:var(--cyan);font-family:var(--sans);font-style:normal;font-size:.9rem;letter-spacing:2px;text-transform:uppercase;">Reset filters</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include "includes/footer.php"; ?>
