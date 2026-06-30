<?php 
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
function redirect($url, $time=0){
   if(headers_sent()){
      echo '<script>window.location.href="'.$url.'";</script>';
      exit();
   }else{
      header("Refresh: $time; URL=$url");
   }
   exit();
} 

function uploadImage($con, $email, $path, $temp, $ext){
   $newPath = $path.substr(md5(time()), 0, 10).'.'.$ext;
   $actualPath= 'view/'.$newPath;
   if(move_uploaded_file($temp, $newPath)){
      $q = "UPDATE affiliate SET pic = ? WHERE email = ?";
      $stmt = mysqli_prepare($con, $q);
      mysqli_stmt_bind_param($stmt, 'ss', $actualPath, $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_affected_rows($stmt) == 1){
         mysqli_stmt_free_result($stmt);
         return TRUE;
      }else{
         mysqli_stmt_free_result($stmt);
         return FALSE;
      }
   }
}
function uploadPayment($con, $email, $path, $temp, $ext){
   $newPath = $path.substr(md5(time()), 0, 10).'.'.$ext;
   $actualPath= 'view/'.$newPath;
   if(move_uploaded_file($temp, $newPath)){
      $q = "UPDATE affiliate SET payment_upload = ? WHERE email = ?";
      $stmt = mysqli_prepare($con, $q);
      mysqli_stmt_bind_param($stmt, 'ss', $actualPath, $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_affected_rows($stmt) == 1){
         mysqli_stmt_free_result($stmt);
         return TRUE;
      }else{
         mysqli_stmt_free_result($stmt);
         return FALSE;
      }
   }
}
function uploadUpgrade($con, $email, $path, $temp, $ext){
   $newPath = $path.substr(md5(time()), 0, 10).'.'.$ext;
   $actualPath= 'view/'.$newPath;
   if(move_uploaded_file($temp, $newPath)){
      $q = "UPDATE affiliate SET upgrade_upload = ? WHERE email = ?";
      $stmt = mysqli_prepare($con, $q);
      mysqli_stmt_bind_param($stmt, 'ss', $actualPath, $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_affected_rows($stmt) == 1){
         mysqli_stmt_free_result($stmt);
         return TRUE;
      }else{
         mysqli_stmt_free_result($stmt);
         return FALSE;
      }
   }
}
function checkUser($con, $email, $uname, $phone) {
   $q = "SELECT * FROM affiliate WHERE email = ? OR uname = ? OR phone = ?";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'sss', $email, $uname, $phone);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   if(mysqli_stmt_num_rows($stmt) > 0){
      mysqli_stmt_free_result($stmt);
       return TRUE;
   } else {
      mysqli_stmt_free_result($stmt);
      return FALSE;
   }
}
function createUser($con, $uname, $fname, $email, $pic, $phone, $address, $gender, $work, $acct_no, $acct_name, $bank, $ref_id, $uplink, $pass, $verified, $paid, $earnings, $withdrawal, $balance, $type, $date) {
   //$acct_no = "35" . substr(rand(500000, 2000000) * date("Y"), 0, 8);
   $q = "INSERT INTO affiliate (uname, fname, email, pic, phone, address, gender, work, acct_no, acct_name, bank, ref_id, uplink, pass, verified, paid, earnings, withdrawal, balance, type, date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
   $r = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($r, 'ssssssssssssssssiiiss', $uname, $fname, $email, $pic, $phone, $address, $gender, $work, $acct_no, $acct_name, $bank, $ref_id, $uplink, $pass, $verified, $paid, $earnings, $withdrawal, $balance, $type, $date);
   mysqli_stmt_execute($r);

   if(mysqli_stmt_affected_rows($r) == 1){
      return TRUE;
   }else{
      return FALSE;
   }  
}

function createQuote($con, $name, $email, $interest, $budget, $notes, $date){
   $q = "INSERT INTO quotes (name, email, interest, budget, notes, date) VALUES (?,?,?,?,?,?)";
   $stmt = mysqli_prepare ($con, $q);
   mysqli_stmt_bind_param($stmt, 'ssssss', $name, $email, $interest, $budget, $notes, $date);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   if(mysqli_stmt_affected_rows($stmt) == 1){
      return TRUE;
   }else{
      return FALSE;
   } 
}

function createContact($con, $name, $email, $phone, $notes, $date){
   $q = "INSERT INTO contact (name, email, phone, notes, date) VALUES(?,?,?,?,?) ";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $notes, $date);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   if(mysqli_stmt_affected_rows($stmt) == 1){
      return TRUE;
   }else{
      return FALSE;
   }  
}

// function updatePost($con, $path, $temp, $ext, $title, $body, $date, $category, $id){
//    $newPath = $path.substr(md5(time()), 0, 10).'.'.$ext;
//    $actualPath= 'admin/'.$newPath;
//    if(move_uploaded_file($temp, $newPath)){
//       $q = "UPDATE posts SET title = ?, body = ?, date = ?, img = ?, category = ?  WHERE id = ?";
//       $stmt = mysqli_prepare($con, $q);
//       mysqli_stmt_bind_param($stmt, 'sssssi', $title, $body, $date, $actualPath, $category, $id);
//       mysqli_stmt_execute($stmt);
//       mysqli_stmt_store_result($stmt);
//       if(mysqli_stmt_affected_rows($stmt) == 1){
//          return TRUE;
//       }else{
//          return FALSE;
//       }
//    }
// }
function updateProp($con, $path, $files, $title, $date, $price, $location, $body, $id) {
   $uploadedPaths = [];

   foreach ($files as $file) {
      $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
      $newPath = $path . substr(md5(time() . $file['name']), 0, 10) . '.' . $ext;
      $actualPath = 'admin/' . $newPath;

      if (move_uploaded_file($file['tmp_name'], $newPath)) {
         $uploadedPaths[] = $actualPath;
      } else {
         return FALSE;
      }
   }

   $mediaPaths = json_encode($uploadedPaths);

   $q    = "UPDATE properties SET title = ?, date = ?, img = ?, price = ?, location = ?, body = ? WHERE id = ?";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'ssssssi', $title, $date, $mediaPaths, $price, $location, $body, $id);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);

   return mysqli_stmt_affected_rows($stmt) === 1 ? TRUE : FALSE;
}

function updatePropImg($con, $title, $date, $price, $location, $body, $id) {
   $q    = "UPDATE properties SET title = ?, date = ?, price = ?, location = ?, body = ? WHERE id = ?";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'sssssi', $title, $date, $price, $location, $body, $id);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);

   return mysqli_stmt_affected_rows($stmt) === 1 ? TRUE : FALSE;
}

function updatePostImg($con, $title, $body, $date, $category, $id){
   $q = "UPDATE posts SET title = ?, body = ?, date = ?, category = ?  WHERE id = ?";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'ssssi', $title, $body, $date, $category, $id);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   if(mysqli_stmt_affected_rows($stmt) == 1){
      return TRUE;
   }else{
      return FALSE;
   }
}

function withdraw($con, $email, $amount, $status, $date){
   $q = "INSERT INTO withdraw (email, amount, status, date) VALUES (?,?,?,?) ";
   $stmt = mysqli_prepare($con, $q);
   mysqli_stmt_bind_param($stmt, 'siss',$email, $amount, $status, $date);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   if(mysqli_stmt_affected_rows($stmt) == 1){
      return TRUE;
   }else{
      return FALSE;
   }
}
function createSale($con, $path, $temp, $ext, $email, $title, $description, $payment, $commission, $status, $date){
   $newPath = $path.substr(md5(time()), 0, 10).'.'.$ext;
   $actualPath= 'view/'.$newPath;
   if(move_uploaded_file($temp, $newPath)){
      $q = "INSERT INTO affiliatesales (email, title, description, payment, commission, img, status, date) VALUES (?,?,?,?,?,?,?,?) ";
      $stmt = mysqli_prepare($con, $q);
      mysqli_stmt_bind_param($stmt, 'sssiisss',$email, $title, $description, $payment, $commission, $actualPath, $status, $date);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_affected_rows($stmt) == 1){
         return TRUE;
      }else{
         return FALSE;
      }
   }
}

function createProp($con, $path, $files, $title, $date, $price, $location, $division, $body) {
    $uploadedPaths = [];

    foreach ($files as $file) {
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newPath = $path . substr(md5(time() . $file['name']), 0, 10) . '.' . $ext;
        $actualPath = 'admin/' . $newPath;

        if (move_uploaded_file($file['tmp_name'], $newPath)) {
            $uploadedPaths[] = $actualPath;
        } else {
            return FALSE; // Roll back is not possible here, but flag the failure
        }
    }

    $mediaPaths = json_encode($uploadedPaths); // e.g. ["admin/uploads/abc123.jpg","admin/uploads/def456.mp4"]

    $q    = "INSERT INTO properties (title, date, img, price, location, division, body) VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'sssssss', $title, $date, $mediaPaths, $price, $location, $division, $body);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    return mysqli_stmt_affected_rows($stmt) === 1 ? TRUE : FALSE;
}

function createPost($con, $path, $files, $title, $date, $subtitle, $body) {
    $uploadedPaths = [];

    foreach ($files as $file) {
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newPath = $path . substr(md5(time() . $file['name']), 0, 10) . '.' . $ext;
        $actualPath = 'admin/' . $newPath;

        if (move_uploaded_file($file['tmp_name'], $newPath)) {
            $uploadedPaths[] = $actualPath;
        } else {
            return FALSE; // Roll back is not possible here, but flag the failure
        }
    }

    $mediaPaths = json_encode($uploadedPaths); // e.g. ["admin/uploads/abc123.jpg","admin/uploads/def456.mp4"]

    $q    = "INSERT INTO posts (title, date, img, subtitle, body) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'sssss', $title, $date, $mediaPaths, $subtitle, $body);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    return mysqli_stmt_affected_rows($stmt) === 1 ? TRUE : FALSE;
}
