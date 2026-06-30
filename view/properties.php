<?php 
include "../config/dbconfig.php";
include "includes/header.php"; 
?>
<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="section-label"><div class="section-label-line"></div><span>Portfolio</span></div>
      <h1 class="section-title">A Curated Selection of<br><em>Prime Properties</em></h1>
      <p class="page-hero-sub">
        From hand-picked land parcels in Awka North to premium estates in Enugu and Asaba — every listing is verified,
        documented, and backed by our AMGC security guarantee.
      </p>
    </div>
  </div>
</section>

<?php
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $initialDivision = isset($_GET['division']) ? strtolower(trim($_GET['division'])) : 'south_east';
    if (!in_array($initialDivision, ['south_east', 'abuja'], true)) {
      $initialDivision = 'south_east';
    }
    $where = '';
    if ($q !== '') {
        $qe = mysqli_real_escape_string($con, $q);
        $where = "WHERE location LIKE '%$qe%' OR title LIKE '%$qe%'";
    }
    $sql = "SELECT * FROM properties $where ORDER BY DATE DESC";
    $res = mysqli_query($con, $sql);
    $allRows = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
        $allRows[] = $row;
        }
    }
    $count = count($allRows);

  $divisionLabels = [
    'south_east' => 'South East',
    'abuja' => 'Abuja',
  ];
  $divisionGroups = [
    'south_east' => [],
    'abuja' => [],
  ];
  foreach ($allRows as $row) {
    $division = strtolower(trim((string)($row['division'] ?? '')));
    if (isset($divisionGroups[$division])) {
      $divisionGroups[$division][] = $row;
    }
  }
?>

<section style="background:#fff">
  <div class="container">
    <div class="section-header-row reveal">
      <div>
        <div class="section-label"><div class="section-label-line"></div><span>By Region</span></div>
        <h2 class="section-title">Browse properties by <em>Region</em></h2>
      </div>
      <p class="page-hero-sub" style="max-width:520px;margin:0;">
        See properties grouped by their location division, with tabs for South East and Abuja.
      </p>
    </div>

    <div id="dividion-tabs"></div>
    <div class="division-tabs" id="division-tabs">
      <button class="<?php echo $initialDivision === 'south_east' ? 'active' : ''; ?>" type="button" data-division-tab="south_east">South Eastern Properties</button>
      <button class="<?php echo $initialDivision === 'abuja' ? 'active' : ''; ?>" type="button" data-division-tab="abuja">Abuja Properties</button>
    </div>

    <div class="division-panels">
      <?php foreach ($divisionGroups as $divisionKey => $divisionRows): ?>
        <div class="division-panel<?php echo $divisionKey === $initialDivision ? ' active' : ''; ?>" data-division-panel="<?php echo htmlspecialchars($divisionKey); ?>">
          <div class="division-grid reveal">
            <?php if (!empty($divisionRows)): ?>
              <?php foreach ($divisionRows as $row): ?>
                <?php
                  $type = strtolower($row['type'] ?? 'land');
                  $tag  = !empty($row['tag']) ? '<div class="prop-tag">'.htmlspecialchars($row['tag']).'</div>' : '';
                  $thumbSrc = propertyMediaSrc($row['img'] ?? '', '');
                ?>
                <a href="./property?id=<?php echo urlencode($row['id']); ?>" class="prop-card" data-type="<?php echo htmlspecialchars($type); ?>">
                  <img src="<?php echo htmlspecialchars($thumbSrc); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy"/>
                  <div class="prop-overlay">
                    <div class="prop-location"><?php echo htmlspecialchars($row['location']); ?></div>
                    <div class="prop-name"><?php echo htmlspecialchars($row['title']); ?></div>
                    <div class="prop-price"><?php echo htmlspecialchars($row['price']); ?></div>
                  </div>
                  <?php echo $tag; ?>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="grid-column:1/-1;padding:4rem 0;text-align:center;color:var(--text-muted);font-family:var(--serif);font-style:italic;font-size:1.2rem;">
                No <?php echo htmlspecialchars($divisionLabels[$divisionKey]); ?> properties yet.
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="background:var(--offwhite)">
  <div class="container">
    <div class="props-filter">
      <button class="active" data-filter="all">All Listings</button>
      <button data-filter="land">Land</button>
      <button data-filter="house">Houses</button>
      <button data-filter="apartment">Apartments</button>
      <button data-filter="promo">Promo</button>
    </div>

    <div class="props-all-grid">
      <?php
        if ($count > 0) {
          foreach ($allRows as $row) {
            $type = strtolower($row['type'] ?? 'land');
            $tag  = !empty($row['tag']) ? '<div class="prop-tag">'.htmlspecialchars($row['tag']).'</div>' : '';
            $thumbSrc = propertyMediaSrc($row['img'] ?? '', '');
            echo '<a href="./property?id='.urlencode($row['id']).'" class="prop-card" data-type="'.htmlspecialchars($type).'">
              <img src="'.htmlspecialchars($thumbSrc).'" alt="'.htmlspecialchars($row['title']).'" loading="lazy"/>
              <div class="prop-overlay">
                <div class="prop-location">'.htmlspecialchars($row['location']).'</div>
                <div class="prop-name">'.htmlspecialchars($row['title']).'</div>
                <div class="prop-price">'.htmlspecialchars($row['price']).'</div>
              </div>'.$tag.'</a>';
          }
        } else {
          echo '<div style="grid-column:1/-1;padding:5rem 0;text-align:center;color:var(--text-muted);font-family:var(--serif);font-style:italic;font-size:1.4rem;">No properties match your search'.($q?' for "'.htmlspecialchars($q).'"':'').'.<br><a href="./properties" style="color:var(--cyan);font-family:var(--sans);font-style:normal;font-size:.9rem;letter-spacing:2px;text-transform:uppercase;">Reset filters</a></div>';
        }
      ?>
    </div>
  </div>
</section>

<script>
  (function () {
    const tabs = document.querySelectorAll('[data-division-tab]');
    const panels = document.querySelectorAll('[data-division-panel]');
    if (!tabs.length || !panels.length) return;

    const initialKey = <?php echo json_encode($initialDivision); ?>;
    tabs.forEach((btn) => {
      const isActive = btn.dataset.divisionTab === initialKey;
      btn.classList.toggle('active', isActive);
    });
    panels.forEach((panel) => {
      panel.classList.toggle('active', panel.dataset.divisionPanel === initialKey);
    });

    tabs.forEach((btn) => {
      btn.addEventListener('click', () => {
        const key = btn.dataset.divisionTab;
        tabs.forEach((b) => b.classList.toggle('active', b === btn));
        panels.forEach((panel) => {
          panel.classList.toggle('active', panel.dataset.divisionPanel === key);
        });
      });
    });
  })();
</script>


<?php include "includes/footer.php"; ?>
