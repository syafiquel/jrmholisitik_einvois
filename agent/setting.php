<?php
 include 'core/init.php';
 $page_name = "My Profile";
$url = 'setting.php';
$user_id = $user['id'];

 if (isset($_POST['edit_pengguna'])){
   $nama = $_POST['nama'];
   $no_tel = $_POST['no_tel'];
   $email = $_POST['email'];
   $address_1 = $_POST['address_1'];
   $poskod = $_POST['poskod'];
   $daerah = $_POST['daerah'];
   $state = $_POST['state'];

   if (
     !$nama
     || !$no_tel
     || !$email
   ) {
     $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
     header("Location: $url");
     exit;
   }

   $sql =  "UPDATE user SET
     nama = '$nama',
     no_tel = '$no_tel',
     email = '$email',
     alamat_1 = '$address_1',
     poskod = '$poskod',
     daerah = '$daerah',
     negeri = '$state'

     WHERE id = $user_id";

   // echo $sql;
   // exit;
   if(mysqli_query($db, $sql))
   {
     $_SESSION['success'] = "Agent $nama was successfully updated.";
     header("Location: $url");
     exit;
   }else{
     $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
     header("Location: $url");
     exit;
   }
 }

 $addressCheck = $user['alamat_1'] && $user['poskod'] && $user['daerah'] && $user['negeri'];
 ?>



<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>


</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="<?php echo $main_orientation ?>" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
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
        <form method="post">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">My Profile</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="nama" value="<?php echo $user['nama'] ?>" placeholder="Enter full name" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $user['email'] ?>" placeholder="Enter email" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Phone No </label>
                    <input type="text" class="form-control" name="no_tel" value="<?php echo $user['no_tel'] ?>" placeholder="Enter Phone No"/>
                  </div>
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Address  <?php echo $addressCheck ? '<i class="ti ti-circle-check text-success"></i>':'' ?></label>
                    <div class="row">
                      <div class="col-12">
                        <input type="text" class="form-control mb-1" name="address_1" value="<?php echo $user['alamat_1'] ?>" placeholder="Enter Street" />
                      </div>
                      <div class="col-4 pe-0">
                        <input id="poskod"  type="number"  autocomplete="off" class="form-control mb-1" name="poskod" value="<?php echo $user['poskod'] ?>" placeholder="Enter Postcode" onblur="getCityByPoskod(this.value)"/>
                      </div>
                      <div class="col-8 ps-1">
                        <input id="daerah" type="text" class="form-control mb-1" name="daerah" value="<?php echo $user['daerah'] ?>" placeholder="Enter City" />
                      </div>
                      <div class="col-12">
                        <select id="negeri" name="state" class="form-control mb-1" required >
                          <option value="">Pilih Negeri</option>
                          <?php foreach ($all_negeri as $each_negeri): ?>
                          <option value="<?php echo $each_negeri ?>" <?php $user['negeri'] == $each_negeri ? 'selected' : '' ?>> <?php echo $each_negeri ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 text-end">
                  <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                  <button type="submit" name="edit_pengguna" class="btn btn-primary">Submit</button>
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

  <script type="text/javascript">
  function getCityByPoskod(poskod) {
    if (poskod) {
      $.ajax({
        url: '../shared/fetcher/getCityByPoskod.php',
        data: {
          poskod
        },
        type: 'get',
        dataType: 'json',
        success: function(data) {
          console.log(data.list_bandar);
          if (data.list_bandar.length == 0) {
            return alert('Poskod Not Found')
          }

          if (data.list_bandar.length > 1) {

            var output = '';
            var outputClass = 'border border-danger p-1 text-danger mb-1 text-center rounded cursor-pointer';
            $("#city_list_wrapper").html('');
            $.each(data.list_bandar, function(index, bandar) {
              $("#city_list_wrapper").append('<div class="' + outputClass + '" onclick="selectCity(`' + bandar + '`)">' + bandar + '</div>')
            });
            $("#modalPoskodList").modal('show');
          }
          if (data.list_bandar.length == 1) {
            $("#daerah").val(data.list_bandar[0]);

          }
          $("#negeri").val(data.negeri).change();
        }
      });

    }
  }

  function selectCity(city) {
    $("#daerah").val(city);
    $("#modalPoskodList").modal('hide');

  }

  </script>
</body>
<!-- [Body] end -->

</html>
