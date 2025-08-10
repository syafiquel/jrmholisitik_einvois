<?php
 include 'core/init.php';
 include "../assets/plugins/resize_image.php";
 $page_name = "Edit Product";
 $url = "edit_product.php";

 if (isset($_POST['update_product'])){
   $product_id = $_POST['product_id'];
   $name = $_POST['name'];
   $product_description = $_POST['product_description'];
   // var_dump($_FILES);
   // exit;
   $new_img = $_FILES['new_img'];

   if ( !empty($name) ){

     if(isset($_FILES['new_img']) && $_FILES['new_img']['size'] > 0){
       //  echo 'dah masyuk upload pls<br>';
       $file_name = $_FILES['new_img']['name'];
       $file_size =$_FILES['new_img']['size'];
       $file_tmp =$_FILES['new_img']['tmp_name'];
       $file_type=$_FILES['new_img']['type'];
       $tmp = explode('.', $file_name);
       $file_extension = end($tmp);
       $file_name_rand = round(microtime(true));
       $newfilename = $file_name_rand . '.' . $file_extension;
       // $newfilename_thumb = $file_name_rand . '_thumb.' . $file_extension;
       $file_ext=strtolower($file_extension);

       $expensions= array("jpeg","jpg","png", "webp");

       if(in_array($file_ext,$expensions)=== false){
         $errors_uploading[]="extension not allowed, please choose a JPEG or PNG file.";
       }

       // if($file_size > 2097152){
       //    $errors_uploading[]='File size must be less than 2 MB';
       // }

       $img_url_uploaded = $newfilename;

       if(empty($errors_uploading)==true){
         //   echo "VALUES('$username','', ' ', '$no_kp', '$email ', '$no_tel ', '$created_at', '88888888', 'Tiada Bayaran', '$img_url', '', '$no_idp'";
         $uploaded_img = "product_img = '$newfilename', ";

       }else{
         $errors = "<li>Terdapat isu dengan Gambar.</li>";
         $_SESSION['error'] = "Invalid image. Use format: JPG, JPEG & PNG. Please try again.";
         header("Location: $url?id=$product_id");
         exit;
       }
     }

     $sql =  "UPDATE product SET
       name = '$name',
       $uploaded_img
       product_description = '$product_description',
       updated_at = now()
       WHERE id = $product_id";

       // echo $sql;
       // exit;
       if(mysqli_query($db, $sql))
       {
         Product::updateRankPrice($_POST['update_rank_price']);
         if(isset($_FILES['new_img']) && $_FILES['new_img']['size'] > 0){
           $big_img = getImageSizeKeepAspectRatio($file_tmp, 600, 600);
           $thumbs_img = getImageSizeKeepAspectRatio($file_tmp, 30, 30);
           resize($file_tmp, "../uploads/product/". $newfilename, $big_img['width'], $big_img['height']);
           resize($file_tmp, "../uploads/product/thumbs/". $newfilename, $thumbs_img['width'], $thumbs_img['height']);
         }
         $_SESSION['success'] = "Product was successfully updated.";
         header("Location: products.php");
         exit;
       }else{
         $_SESSION['error'] = "Failed to update product. Please fill all details of the product. Please try again.";
         header("Location: $url?id=$product_id");
         exit;
       }

    }
    $_SESSION['error'] = "Product Details is not complete.";
    header("Location: $url");
    exit;

 }


 if (isset($_GET['id'])) {
   $id = $_GET['id'];
 }else {
   $_SESSION['error'] = "Product ID not Found. Please try again.";
   header("Location: products.php");
   exit;
 }

 $sql = "SELECT * FROM product WHERE id = $id";
 $result = mysqli_query($database, $sql);
 $row = mysqli_fetch_array($result,MYSQLI_ASSOC);


 $sqlPrice = "SELECT * FROM product_rank_price WHERE product_id = $id";
 $result_price = $db->query($sqlPrice);
 $rank_price = [];
 while($rowx = $result_price->fetch_assoc()) {
   $rank_price[$rowx['product_id']][$rowx['rank_id']] = $rowx['price'];

 }

 ?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>
  <link rel="stylesheet" href="../assets/plugins/dropify/dist/css/dropify.min.css">
  <style media="screen">
    .dropify-wrapper {
      border: 1px solid #bec8d0;
      border-radius: 8px;
    }
  </style>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ Sidebar Menu ] start -->
  <?php include 'partial/sidebar.php'; ?>
  <!-- [ Sidebar Menu ] end -->
  <!-- [ Header Topbar ] start -->
  <?php include 'partial/topnav.php'; ?>
  <!-- announcement modal -->
  <?php include 'partial/announcement_modal.php'; ?>
  <!-- [ Header ] end -->



  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ Main Content ] start -->
      <div class="row">
        <form method="post" enctype="multipart/form-data" >
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0"><?php echo $page_name ?></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12">

                    <div class="mb-3">
                      <label class="form-label">Name</label>
                      <input type="text" name="name" value="<?php echo $row['name'] ?>" class="form-control" required>
                    </div>
                    <?php foreach ($ranks_ref as $rank_id => $rank): ?>
                      <div class="mb-3 ">
                        <label class="form-label"><?php echo $rank ?> Price</label>
                        <input type="number" name="update_rank_price[<?php echo $row['id'] ?>][<?php echo $rank_id ?>]" value="<?php echo isset($rank_price[$row['id']][$rank_id]) ? $rank_price[$row['id']][$rank_id] : '0'?>" step="0.01" min="0.01" class="form-control" required>
                      </div>
                    <?php endforeach; ?>
                    <div class="mb-3">
                      <label class="form-label">Description</label>
                      <textarea name="product_description" class="form-control"><?php echo $row['product_description'] ?></textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Product Image</label>
                      <br>
                      <img src="../uploads/product/thumbs/<?php echo $row['product_img'] ?>" alt="user-image" class="wid-40 rounded" />
                      <hr>
                      <label class="form-label">New Product Image</label>
                      <input name="new_img" type="file" id="" class="dropify mt-2" />
                      <small>*Image will be resize to 500px </small>
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                    <div class="col-md-12 text-end">
                      <button type="submit" name="update_product" class="btn btn-primary">Submit</button>
                    </div>
                </div>

              </div>
            </div>

          </div>
        </form>

      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <!-- Ckeditor js -->
  <script src="../assets/js/plugins/ckeditor/classic/ckeditor.js"></script>
  <script src="../assets/plugins/dropify/dist/js/dropify.min.js"></script>
  <script type="text/javascript">

   $(document).ready(function () {
      $('.dropify').dropify();
   });
    (function () {
      ClassicEditor.create(document.querySelector('#classic-editor')).catch((error) => {
        console.error(error);
      });
    })();
  </script>
  <!-- [Page Specific JS] end -->


</body>
<!-- [Body] end -->

</html>
