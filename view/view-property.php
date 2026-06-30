<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { redirect('./properties'); }

$stmt = mysqli_prepare($con, "SELECT * FROM properties WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res  = mysqli_stmt_get_result($stmt);
$prop = mysqli_fetch_assoc($res);

if (!$prop) {
    include('includes/header.php');
    echo '<section style="min-height:70vh;display:flex;align-items:center">
            <div class="container" style="text-align:center">
                <div class="section-label" style="justify-content:center">
                    <div class="section-label-line"></div><span>404</span>
                </div>
                <h1 class="section-title">Property not found</h1>
                <p style="margin:2rem 0;color:var(--text-muted)">This listing has been moved or no longer exists.</p>
                <a class="btn-primary" href="./properties">Back to Listings</a>
            </div>
          </section>';
    include('includes/footer.php');
    exit;
}

/* ── Core fields ── */
$title    = htmlspecialchars($prop['title']);
$location = htmlspecialchars($prop['location'] ?? '');
$price    = htmlspecialchars($prop['price'] ?? '');
$body     = !empty($prop['body']) ? $prop['body'] : ''; // TinyMCE HTML — do NOT escape

/* ── Division label map ── */
$divisionMap = [
    'south_east' => 'South-East Division',
    'abuja'      => 'Abuja Division',
];
$divisionRaw   = $prop['division'] ?? '';
$divisionLabel = $divisionMap[$divisionRaw] ?? ucwords(str_replace('_', ' ', $divisionRaw));

/* ── Media array — handles old plain-string rows and new JSON rows ── */
$mediaRaw = $prop['img'] ?? '';
$media    = json_decode($mediaRaw, true);
if (!is_array($media) || empty($media)) {
    // Legacy: plain single path
    $media = $mediaRaw ? [$mediaRaw] : [];
}

/* Helper: is a path a video? */
function isVideo(string $path): bool {
    return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'webm', 'mov']);
}

$heroMedia   = !empty($media) ? $media[0] : '';
$heroIsVideo = $heroMedia && isVideo($heroMedia);
$gallery     = count($media) > 1 ? $media : []; // gallery only if more than one file

/* ── Related listings ── */
$related = [];
$rs = mysqli_prepare($con, "SELECT id, title, location, price, img FROM properties WHERE id != ? ORDER BY id DESC LIMIT 3");
mysqli_stmt_bind_param($rs, 'i', $id);
mysqli_stmt_execute($rs);
$rsResult = mysqli_stmt_get_result($rs);
while ($r = mysqli_fetch_assoc($rsResult)) $related[] = $r;

/* Related: get first image from each listing's media */
foreach ($related as &$r) {
    $rMedia = json_decode($r['img'], true);
    $r['thumb'] = is_array($rMedia) && !empty($rMedia) ? $rMedia[0] : $r['img'];
}
unset($r);

include("includes/header.php");
?>

<!-- ======================================================
     HERO
====================================================== -->
<section class="vp-hero">
    <div class="vp-hero-media" id="vp-hero-media">
        <?php if ($heroIsVideo): ?>
            <video
                src="<?php echo htmlspecialchars($heroMedia); ?>"
                id="vp-main-media"
                autoplay muted loop playsinline
            ></video>
        <?php elseif ($heroMedia): ?>
            <img
                src="<?php echo htmlspecialchars($heroMedia); ?>"
                alt="<?php echo $title; ?>"
                id="vp-main-media"
            />
        <?php endif; ?>
        <div class="vp-hero-scrim"></div>
    </div>

    <div class="container vp-hero-inner">
        <a href="./properties" class="vp-back">&larr; All Listings</a>

        <div class="vp-meta-row">
            <?php if ($divisionLabel): ?>
                <span class="vp-chip vp-chip-accent"><?php echo htmlspecialchars($divisionLabel); ?></span>
            <?php endif; ?>
            <span class="vp-chip vp-chip-ghost">
                <i class="bx bx-map"></i> <?php echo $location; ?>
            </span>
            <?php if (count($media) > 1): ?>
                <span class="vp-chip vp-chip-ghost">
                    <i class="bx bx-images"></i> <?php echo count($media); ?> media files
                </span>
            <?php endif; ?>
        </div>

        <h1 class="vp-title"><?php echo $title; ?></h1>

        <div class="vp-price-row">
            <div>
                <div class="vp-price-label">Investment from</div>
                <div class="vp-price"><?php echo $price; ?></div>
            </div>
            
            <a href="https://wa.me/<?php echo preg_replace('/\D/', '', PHONE_NO); ?>?text=I'm%20interested%20in%20<?php echo urlencode($prop['title']); ?>"
                target="_blank" rel="noopener"
                class="btn-primary"
            >Enquire on WhatsApp</a>
        </div>
    </div>
</section>

<!-- ======================================================
     BODY
====================================================== -->
<section class="vp-body">
    <div class="container vp-grid">

        <!-- Main content column -->
        <article class="vp-content">

            <!-- Overview / body -->
            <div class="section-label">
                <div class="section-label-line"></div>
                <span>Overview</span>
            </div>
            <h2 class="vp-h2">About this <em>listing</em></h2>

            <div class="vp-desc">
                <?php if (!empty($body)): ?>
                    <?php echo $body; /* TinyMCE HTML — already sanitised at input */ ?>
                <?php else: ?>
                    <p>An exclusive opportunity in <?php echo $location; ?> — meticulously
                    documented, AMGC-secured, and ready for the next chapter of your
                    investment journey.</p>
                <?php endif; ?>
            </div>

            <!-- Media gallery (only shown when more than one file) -->
            <?php if (!empty($gallery)): ?>
            <div class="vp-section-divider"></div>
            <div class="section-label">
                <div class="section-label-line"></div>
                <span>Gallery</span>
            </div>
            <h2 class="vp-h2">Visual <em>tour</em></h2>

            <div class="vp-gallery">
                <?php foreach ($media as $index => $item):
                    $itemIsVideo = isVideo($item);
                    $itemPath    = htmlspecialchars($item);
                ?>
                    <button
                        type="button"
                        class="vp-thumb <?php echo $index === 0 ? 'is-active' : ''; ?>"
                        data-full="<?php echo $itemPath; ?>"
                        data-type="<?php echo $itemIsVideo ? 'video' : 'image'; ?>"
                    >
                        <?php if ($itemIsVideo): ?>
                            <video src="<?php echo $itemPath; ?>" muted playsinline></video>
                            <span class="vp-thumb-play"><i class="bx bx-play"></i></span>
                        <?php else: ?>
                            <img src="<?php echo $itemPath; ?>" alt="" loading="lazy" />
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </article>

        <!-- Sticky sidebar -->
        <aside class="vp-aside">

            <!-- Property highlights card -->
            <div class="vp-card">
                <div class="vp-card-title">Property Highlights</div>
                <ul class="vp-spec">
                    <?php if ($divisionLabel): ?>
                    <li>
                        <span>Division</span>
                        <strong><?php echo htmlspecialchars($divisionLabel); ?></strong>
                    </li>
                    <?php endif; ?>
                    <li>
                        <span>Location</span>
                        <strong><?php echo $location; ?></strong>
                    </li>
                    <li>
                        <span>Price</span>
                        <strong><?php echo $price; ?></strong>
                    </li>
                    <?php if (!empty($prop['size'])): ?>
                    <li>
                        <span>Size</span>
                        <strong><?php echo htmlspecialchars($prop['size']); ?></strong>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($prop['title_doc'])): ?>
                    <li>
                        <span>Title</span>
                        <strong><?php echo htmlspecialchars($prop['title_doc']); ?></strong>
                    </li>
                    <?php endif; ?>
                    <li>
                        <span>Listed</span>
                        <strong>
                            <?php
                                // date stored as "d/m/Y h:i:s" — parse it safely
                                $listed = '—';
                                if (!empty($prop['date'])) {
                                    $d = DateTime::createFromFormat('d/m/Y h:i:s', $prop['date']);
                                    if ($d) $listed = $d->format('M Y');
                                }
                                echo $listed;
                            ?>
                        </strong>
                    </li>
                    <?php if (count($media) > 0): ?>
                    <li>
                        <span>Media</span>
                        <strong><?php echo count($media); ?> file<?php echo count($media) !== 1 ? 's' : ''; ?></strong>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- CTA card -->
            <div class="vp-card vp-card-dark">
                <div class="vp-card-eyebrow">Talk to a Realtor</div>
                <h3 class="vp-card-h3">Schedule a private inspection</h3>
                <p class="vp-card-p">
                    Our team is on standby to walk you through the documentation
                    and arrange a site visit at your convenience.
                </p>
                
                <a class="btn-primary"
                    style="width:100%;text-align:center;display:block"
                    href="tel:<?php echo PHONE_NO; ?>"
                >Call <?php echo PHONE_NO; ?></a>
                
                <a class="btn-ghost"
                    style="margin-top:1rem;color:var(--cyan);display:inline-flex"
                    href="./contact"
                >Send a message &rarr;</a>
            </div>

        </aside>
    </div>
</section>

<!-- ======================================================
     RELATED
====================================================== -->
<?php if (!empty($related)): ?>
<section class="vp-related">
    <div class="container">
        <div class="section-header-row">
            <div>
                <div class="section-label">
                    <div class="section-label-line"></div>
                    <span>You may also like</span>
                </div>
                <h2 class="section-title">Other <em>opportunities</em></h2>
            </div>
            <a href="./properties" class="view-all">All Listings &rarr;</a>
        </div>

        <div class="props-all-grid">
            <?php foreach ($related as $r): ?>
                <a href="./property?id=<?php echo (int)$r['id']; ?>" class="prop-card">
                    <?php
                        $rThumb    = $r['thumb'];
                        $rIsVideo  = isVideo($rThumb);
                    ?>
                    <?php if ($rIsVideo): ?>
                        <video src="<?php echo htmlspecialchars($rThumb); ?>"
                               muted playsinline loop
                               style="width:100%;height:100%;object-fit:cover;opacity:.7"></video>
                    <?php else: ?>
                        <img
                            src="<?php echo htmlspecialchars($rThumb); ?>"
                            alt="<?php echo htmlspecialchars($r['title']); ?>"
                            loading="lazy"
                        />
                    <?php endif; ?>
                    <div class="prop-overlay">
                        <div class="prop-location"><?php echo htmlspecialchars($r['location']); ?></div>
                        <div class="prop-name"><?php echo htmlspecialchars($r['title']); ?></div>
                        <div class="prop-price"><?php echo htmlspecialchars($r['price']); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======================================================
     SCRIPTS
====================================================== -->
<script>
(function () {
    const heroWrap = document.getElementById('vp-hero-media');
    let currentMedia = document.getElementById('vp-main-media');

    document.querySelectorAll('.vp-thumb').forEach(thumb => {
        thumb.addEventListener('click', () => {
            const src  = thumb.dataset.full;
            const type = thumb.dataset.type; // 'image' | 'video'

            /* Swap hero element if type changed */
            if (type === 'video' && currentMedia.tagName === 'IMG') {
                const vid = document.createElement('video');
                vid.id        = 'vp-main-media';
                vid.autoplay  = true;
                vid.muted     = true;
                vid.loop      = true;
                vid.playsInline = true;
                vid.style.cssText = currentMedia.style.cssText;
                heroWrap.replaceChild(vid, currentMedia);
                currentMedia = vid;
            } else if (type === 'image' && currentMedia.tagName === 'VIDEO') {
                const img = document.createElement('img');
                img.id        = 'vp-main-media';
                img.alt       = '';
                img.style.cssText = currentMedia.style.cssText;
                heroWrap.replaceChild(img, currentMedia);
                currentMedia = img;
            }

            currentMedia.src = src;

            /* Active state on thumbs */
            document.querySelectorAll('.vp-thumb').forEach(t => t.classList.remove('is-active'));
            thumb.classList.add('is-active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
})();
</script>

<?php include "includes/footer.php"; ?>