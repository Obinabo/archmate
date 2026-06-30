<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    echo '<div class="error">You have accessed this page in error!</div>';
    redirect("index.php");
}

$title = 'Admin dashboard | ' . SITE_NAME;
include "includes/head.php";

/* ── Fetch existing row ── */
$q    = "SELECT * FROM properties WHERE id = ?";
$stmt = mysqli_prepare($con, $q);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row    = mysqli_fetch_array($result);
$id     = $row['id'];

/* Decode stored media – handles both old plain-string and new JSON rows */
$existingMedia = json_decode($row['img'], true);
if (!is_array($existingMedia)) {
    $existingMedia = $row['img'] ? [$row['img']] : [];
}

/* ── POST handler ── */
if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['submit'])) {

    $successmsg = '';
    $msg        = [];

    if (empty($_POST['title'])) {
        $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Please enter post title</div>';
    } else {
        $title = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['title'])));
    }

    if (empty($_POST['location'])) {
        $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Please enter location of property</div>';
    } else {
        $location = mysqli_real_escape_string($con, $_POST['location']);
    }

    if (empty($_POST['price'])) {
        $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Please enter price of property</div>';
    } else {
        $price = mysqli_real_escape_string($con, $_POST['price']);
    }

    $body = $_POST['body'] ?? '';

    $date        = date("d/m/Y h:i:s");
    $max_size    = 5 * 1024 * 1024;
    $max_files   = 5;
    $allowed_ext = ['jpeg', 'jpg', 'png', 'mp4', 'webm', 'mov'];
    $upload_dir  = "uploads/";

    /* Reshape multi-file array */
    $raw        = $_FILES['files'] ?? [];
    $file_count = isset($raw['name']) ? count($raw['name']) : 0;
    $files      = [];

    for ($i = 0; $i < $file_count; $i++) {
        if ($raw['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        $files[] = [
            'name'     => $raw['name'][$i],
            'tmp_name' => $raw['tmp_name'][$i],
            'size'     => $raw['size'][$i],
            'error'    => $raw['error'][$i],
        ];
    }

    if (empty($files)) {
        /* No new media — update text fields only */
        if (empty($msg)) {
            updatePropImg($con, $title, $date, $price, $location, $body, $id);
            $successmsg = '<div class="adm-alert adm-alert--success"><i class="fa fa-check-circle"></i> Property updated successfully</div>
                <a href="' . URL . '/properties" class="adm-btn adm-btn--ghost">View Property</a>';
        }
    } else {
        /* Validate each new file */
        if (count($files) > $max_files) {
            $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> You may upload a maximum of ' . $max_files . ' files</div>';
        } else {
            foreach ($files as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> One or more files failed to upload. Please try again.</div>';
                    break;
                }
                if ($file['size'] > $max_size) {
                    $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Each file must be under 5 MB (failed: ' . htmlspecialchars($file['name']) . ')</div>';
                    break;
                }
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_ext)) {
                    $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Only JPG, PNG, MP4, WEBM and MOV allowed (failed: ' . htmlspecialchars($file['name']) . ')</div>';
                    break;
                }
            }
        }

        if (empty($msg)) {
            if (updateProp($con, $upload_dir, $files, $title, $date, $price, $location, $body, $id) === TRUE) {
                $successmsg = '<div class="adm-alert adm-alert--success"><i class="fa fa-check-circle"></i> Property updated successfully</div>
                <a href="' . URL . '/properties" class="adm-btn adm-btn--ghost">View Property</a>';
            } else {
                $msg[] = '<div class="adm-alert adm-alert--error"><i class="fa fa-exclamation-circle"></i> Failed to update property</div>';
            }
        }
    }
}
?>

<section class="admin-section adm-section">

    <!-- Page header -->
    <div class="adm-pagehead" data-aos="fade-up">
        <div>
            <span class="adm-eyebrow">Inventory · Edit</span>
            <h1 class="adm-pagehead__title">Edit <em>property</em></h1>
            <p class="adm-pagehead__lede">
                Update listing details below. Leave the media field empty to keep the existing files.
            </p>
        </div>
        <a href="./properties" class="adm-btn adm-btn--ghost">
            <i class="fa fa-arrow-left"></i> Back to list
        </a>
    </div>

    <div class="adm-formgrid" data-aos="fade-up">

        <!-- Sidebar guidelines -->
        <aside class="adm-formgrid__aside">
            <h3 class="adm-card__title">Guidelines</h3>
            <ul class="adm-bullets">
                <li>Leave the media field <strong>empty</strong> to keep existing files unchanged.</li>
                <li>Uploading new files <strong>replaces all</strong> existing media for this listing.</li>
                <li>Up to 5 files · JPG / PNG / MP4 / WEBM / MOV · 5 MB each.</li>
                <li>Format price with currency symbol (e.g. ₦2,000,000).</li>
            </ul>

            <!-- Current media preview -->
            <?php if (!empty($existingMedia)) : ?>
            <div class="adm-media-preview">
                <p class="adm-media-preview__label">Current media</p>
                <div class="adm-media-preview__grid">
                    <?php foreach ($existingMedia as $mediaPath) :
                        $mediaExt = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                        $isVideo  = in_array($mediaExt, ['mp4', 'webm', 'mov']);
                    ?>
                        <div class="adm-media-thumb">
                            <?php if ($isVideo) : ?>
                                <video src="../<?php echo htmlspecialchars($mediaPath); ?>" muted playsinline></video>
                                <span class="adm-media-thumb__badge"><i class="fa fa-play"></i></span>
                            <?php else : ?>
                                <img src="../<?php echo htmlspecialchars($mediaPath); ?>" alt="property media">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </aside>

        <!-- Form card -->
        <div class="reg-container2 adm-formcard">

            <?php
                if (!empty($msg))      echo implode('', $msg);
                if (isset($successmsg)) echo $successmsg;
            ?>

            <form action="" method="POST" enctype="multipart/form-data" class="adm-form">

                <label class="adm-field">
                    <span>Title</span>
                    <span class="input-container">
                        <input type="text" name="title"
                               value="<?php echo htmlspecialchars($row['title']); ?>"
                               placeholder="E.g Rockshelter Estate">
                    </span>
                </label>

                <label class="adm-field">
                    <span>Price</span>
                    <span class="input-container">
                        <input type="text" name="price"
                               value="<?php echo htmlspecialchars($row['price']); ?>"
                               placeholder="E.g ₦2,000,000">
                    </span>
                </label>

                <label class="adm-field">
                    <span>Location</span>
                    <span class="input-container">
                        <input type="text" name="location"
                               value="<?php echo htmlspecialchars($row['location']); ?>"
                               placeholder="E.g Mgbakwu">
                    </span>
                </label>

                <label class="adm-field adm-field--file">
                    <span>Replace media <span style="color:var(--adm-muted);font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></span>
                    <input type="file" name="files[]" multiple
                           accept=".jpg,.jpeg,.png,.mp4,.webm,.mov" />
                    <small class="adm-field__hint">
                        Up to 5 files · JPG / PNG / MP4 / WEBM / MOV · 5 MB each.
                        Uploading here replaces all current media.
                    </small>
                </label>
                <label class="adm-field">
                    <span>Body</span>
                    <span class="input-container">
                        <textarea name="body" id="default" cols="100"><?php echo htmlspecialchars($row['body']); ?></textarea>
                    </span>
                </label>
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <button type="submit" name="submit"
                        class="submit-button white adm-btn adm-btn--primary adm-btn--block">
                    <i class="fa fa-save"></i> Save changes
                </button>

            </form>
        </div>
    </div>
</section>

<?php include "includes/foot.php"; ?>