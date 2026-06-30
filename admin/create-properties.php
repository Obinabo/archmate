<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";

if (isset($_SESSION['id'])) {
    // authenticated
} else {
    redirect('index.php');
}

$title = 'Admin dashboard | ' . SITE_NAME;
include "includes/head.php";

if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['submit'])) {

    $successmsg = '';
    $msg        = [];

    /* ── Text fields ── */
    if (empty($_POST['title'])) {
        $msg[] = '<div class="error">Please enter post title</div>';
    } else {
        $title = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['title'])));
    }

    if (empty($_POST['location'])) {
        $msg[] = '<div class="error">Please enter location of property</div>';
    } else {
        $location = mysqli_real_escape_string($con, $_POST['location']);
    }

    if (empty($_POST['price'])) {
        $msg[] = '<div class="error">Please enter price of property</div>';
    } else {
        $price = mysqli_real_escape_string($con, $_POST['price']);
    }

    if (empty($_POST['division'])) {
        $msg[] = '<div class="error">Please select a division</div>';
    } else {
        $division = mysqli_real_escape_string($con, $_POST['division']);
    }

    $body = $_POST['body'] ?? '';
    $date = date("d/m/Y h:i:s");

    /* ── File handling ── */
    $max_size    = 50 * 1024 * 1024; // 50 MB per file
    $max_files   = 5;
    $allowed_ext = ['jpeg', 'jpg', 'png', 'mp4', 'webm', 'mov'];
    $upload_dir  = "uploads/";

    // Reshape PHP's multi-file array into a simple list of file entries
    $raw        = $_FILES['files'] ?? [];
    $file_count = isset($raw['name']) ? count($raw['name']) : 0;
    $files      = [];

    for ($i = 0; $i < $file_count; $i++) {
        // Skip slots the browser left empty
        if ($raw['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

        $files[] = [
            'name'     => $raw['name'][$i],
            'tmp_name' => $raw['tmp_name'][$i],
            'size'     => $raw['size'][$i],
            'error'    => $raw['error'][$i],
        ];
    }

    if (empty($files)) {
        $msg[] = '<div class="error">Please select at least one image or video</div>';
    } elseif (count($files) > $max_files) {
        $msg[] = '<div class="error">You may upload a maximum of ' . $max_files . ' files</div>';
    } else {
        foreach ($files as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $msg[] = '<div class="error">One or more files failed to upload. Please try again.</div>';
                break;
            }
            if ($file['size'] > $max_size) {
                $msg[] = '<div class="error">Each file must be under 50 MB (failed: ' . htmlspecialchars($file['name']) . ')</div>';
                break;
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_ext)) {
                $msg[] = '<div class="error">Only JPG, PNG, MP4, WEBM and MOV files are allowed (failed: ' . htmlspecialchars($file['name']) . ')</div>';
                break;
            }
        }
    }

    // Submit form if no errors
    if (empty($msg)) {
        if (createProp($con, $upload_dir, $files, $title, $date, $price, $location, $division, $body) === TRUE) {
            $successmsg = '<div class="adm-alert adm-alert--success">Property created</div>
            <a href="' . URL . '/properties" class="adm-btn adm-btn--ghost">View Property</a>';
        } else {
            $msg[] = '<div class="adm-alert adm-alert--error">Failed to create property</div>';
        }
    }
}
?>

<section class="admin-section adm-section">
    <div class="adm-pagehead" data-aos="fade-up">
        <div>
            <span class="adm-eyebrow">Inventory · New</span>
            <h1 class="adm-pagehead__title">Create a <em>property</em></h1>
            <p class="adm-pagehead__lede">Add a new listing with title, price, location and a hero image. JPG / PNG up to 50MB.</p>
        </div>
        <a href="./properties" class="adm-btn adm-btn--ghost"><i class="fa fa-arrow-left"></i> Back to list</a>
    </div>

    <div class="adm-formgrid" data-aos="fade-up">
        <aside class="adm-formgrid__aside">
            <h3 class="adm-card__title">Guidelines</h3>
            <ul class="adm-bullets">
                <li>Use a descriptive title (e.g. "Rockshelter Estate, Mgbakwu").</li>
                <li>Format price with currency (e.g. ₦2,000,000).</li>
                <li>Image must be JPG/PNG and under 50MB.</li>
                <li>Featured listings appear on the homepage editorial grid.</li>
            </ul>
        </aside>

        <div class="reg-container2 adm-formcard">
            <?php 
                if (!empty($msg)) {   
                    echo implode('<br/>', $msg);
                }
                if(isset($successmsg)){echo $successmsg;}
            ?>
            <form action="" method="POST" enctype="multipart/form-data" class="adm-form">
                <label class="adm-field">
                    <span>Title</span>
                    <span class="input-container"><input type="text" name="title" placeholder="E.g Rockshelter Estate"></span>
                </label>
                <label class="adm-field">
                    <span>Price</span>
                    <span class="input-container"><input type="text" name="price" placeholder="E.g ₦2,000,000"></span>
                </label>
                <label class="adm-field">
                    <span>Location</span>
                    <span class="input-container"><input type="text" name="location" placeholder="E.g Mgbakwu"></span>
                </label>
                <label class="adm-field">
                    <span>Division</span>
                    <span class="input-container">
                        <select name="division">
                            <option value="">Select a division</option>
                            <option value="south_east">South-East Division</option>
                            <option value="abuja">Abuja Division</option>
                        </select>
                    </span>
                </label>
                <label class="adm-field adm-field--file">
                    <span>Media (images &amp; videos)</span>
                    <input
                        type="file"
                        name="files[]"
                        multiple
                        accept=".jpg,.jpeg,.png,.mp4,.webm,.mov"
                    />
                    <small style="color:var(--adm-muted);font-size:12px;margin-top:4px;">
                        Up to 5 files · JPG / PNG / MP4 / WEBM / MOV · 5 MB each
                    </small>
                </label>
                <label class="adm-field">
                    <span>Body</span>
                    <span class="input-container">
                        <textarea name="body" id="default" cols="100">Enter body of the post</textarea>
                    </span>
                </label>
                <!-- <textarea name="body" id="default" cols="100"></textarea> -->
                <button type="submit" name="submit" class="submit-button white adm-btn adm-btn--primary adm-btn--block">Post property</button>
            </form>
        </div>
    </div>
</section>

<script>
     tinymce.init({
        selector: 'textarea#default',
    });
</script>
<?php include "includes/foot.php"; ?>
