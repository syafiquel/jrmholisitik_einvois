<?php

include 'core/init.php';
include "../assets/plugins/resize_image.php";
$page_name = "Agent";
$url = "agents.php";

if (isset($_POST['simpan_pengguna'])){

  $nama = $_POST['nama'];
  $no_tel = $_POST['no_tel'];
  $email = $_POST['email'];
  $rank_id = $_POST['rank_id'];
  $credit_limit = $_POST['credit_limit'];
  $password = $_POST['password'] ?? 'secret#123';


  if (
    !$nama
    || !$no_tel
    || !$email
    || !$rank_id
  ) {
    $_SESSION['error'] = "Failed to add agent. Please fill all details. Please try again.";
    header("Location: $url");
    exit;
  }

  $password = mysqli_real_escape_string($db, $password);
  $secret_salt = mysqli_real_escape_string($db, '??..jrmholisampang||2024..??');
  $password = md5($password.$secret_salt);

  $sql =  "INSERT INTO user
  ( nama,
    no_tel,
    email,
    rank_id,
    credit_limit,
    password,
    access_level,
    role,
    created_at
  )"

. " VALUES(
  '$nama',
  '$no_tel',
  '$email',
  '$rank_id',
  '$credit_limit',
  '$password',
  '1',
  'agent',
  now()
  )";

  // echo $sql;
  // exit;
  if(mysqli_query($db, $sql))
  {
    $_SESSION['success'] = "Agent $nama was successfully added.";
    header("Location: $url");
    exit;
  }else{
    $_SESSION['error'] = "Failed to add agent. Please fill all details. Please try again.";
    header("Location: $url");
    exit;
  }
}

if (isset($_POST['edit_pengguna'])){
  $nama = $_POST['nama'];
  $no_tel = $_POST['no_tel'];
  $email = $_POST['email'];
  $rank_id = $_POST['rank_id'];
  $credit_limit = $_POST['credit_limit'];
  $user_id = $_POST['edit_user_id'];


  if (
    !$nama
    || !$no_tel
    || !$email
    || !$rank_id
  ) {
    $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
    header("Location: $url");
    exit;
  }

  $sql =  "UPDATE user SET
    nama = '$nama',
    no_tel = '$no_tel',
    email = '$email',
    credit_limit = '$credit_limit',
    rank_id = '$rank_id'
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
if (isset($_POST['edit_password'])){
  $password = $_POST['new_password'] ?? 'secret#123';
  $user_id = $_POST['edit_user_id_p'];


  if (
    !$password
    || !$user_id
  ) {
    $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
    header("Location: $url");
    exit;
  }

  $password = stripslashes($password);
  $password = mysqli_real_escape_string($db, $password);
  $secret_salt = mysqli_real_escape_string($db, '??..jrmholisampang||2024..??');
  $password = md5($password.$secret_salt);

  $sql =  "UPDATE user SET
    password = '$password'
    WHERE id = $user_id";

  // echo $sql;
  // exit;
  if(mysqli_query($db, $sql))
  {
    $_SESSION['success'] = "Password agent $nama was successfully updated.";
    header("Location: $url");
    exit;
  }else{
    $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
    header("Location: $url");
    exit;
  }
}

if (isset($_POST['id_members_to_delete'])) {
  $id = $_POST['id_members_to_delete'];
  $sql = "UPDATE user SET isDeleted = now() WHERE id = $id";
  // $sql = "DELETE FROM agent WHERE id = $id";

  if (mysqli_query($db,$sql)) {
    $_SESSION['success'] = "The product was successfully deleted.";
    header("Location: $url");
    exit;
  }else {
    $_SESSION['error'] = "Action failed.";
    header("Location: $url");
    exit;
  }
}

$sqlBase = "SELECT * FROM user WHERE access_level <> 66 AND isDeleted IS NULL";
$result = $db->query("$sqlBase ");
$totalRecords = mysqli_num_rows($result);

// include '../assets/plugins/paginator.php';
// $paginator = new Paginator();
// $paginator->total = $totalRecords;
// $paginator->paginate();
//
// $start = ($paginator->currentPage-1)*$paginator->itemsPerPage;
// $sql = "SELECT * FROM user WHERE status = 'Selesai Bayaran' AND track_no <> '' AND no_idp <> '88888888' $where_clause ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";
$sql = "$sqlBase ORDER BY id DESC LIMIT 0,  10";

$result_c = $db->query($sql);
 ?>

 <!doctype html>
 <html lang="en">
 <!-- [Head] start -->

 <head>
   <!-- [Meta] -->
   <?php include 'partial/header.php'; ?>
   <link rel="stylesheet" href="../assets/plugins/dropify/dist/css/dropify.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.css" />
   <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css" />

   <style media="screen">
     .dropify-wrapper {
       border: 1px solid #bec8d0;
       border-radius: 8px;
     }
   </style>

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
       <div class="page-header">
         <div class="page-block">
           <div class="row align-items-center">
             <div class="col-md-12">
               <div class="page-header-title">
                 <h2 class="mb-0">Agent list</h2>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="row">
         <!-- [ sample-page ] start -->
         <div class="col-sm-12">
           <div class="card table-card">
             <div class="card-body">
               <div class="text-end p-4 pb-sm-2">
                 <a href="#" data-bs-toggle="offcanvas" data-bs-target="#add-agent-form" aria-controls="add-agent-form" class="btn btn-primary d-inline-flex align-items-center gap-2">
                   <i class="ti ti-plus f-18"></i> Add Agent
                 </a>
               </div>

               <!-- edit user modal -->
               <div
                 id="modalEditUser"
                 class="modal fade"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="modalEditUserContent"
                 aria-hidden="true"
               >
                 <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                   <div class="modal-content">
                     <div class="modal-header">
                       <h5 class="modal-title" id="modalEditUserContent">Edit Agent</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <form method="post">

                       <div class="modal-body">
                         <div class="row">
                           <div class="col-md-12">
                             <div class="mb-3">
                               <label class="form-label">Full Name</label>
                               <input name="nama" id="nama" type="text" class="form-control" placeholder="Enter full name" />
                             </div>
                           </div>
                           <div class="col-md-6">
                             <div class="mb-3">
                               <label class="form-label">Email</label>
                               <input name="email" id="email" type="email" class="form-control" placeholder="Enter email" />
                             </div>
                           </div>
                           <div class="col-md-6">
                             <div class="mb-3">
                               <label class="form-label">Phone No </label>
                               <input name="no_tel" id="no_tel" type="text" class="form-control" placeholder="Enter Phone No"/>
                             </div>
                           </div>
                           <div class="col-md-6">
                             <div class="mb-3">
                               <label class="form-label">Rank</label>
                               <select class="form-select" required id="rank_id" name="rank_id">
                                 <option>Select Rank</option>
                                 <?php foreach ($ranks_ref as $rank_id => $rank_name): ?>
                                   <option value="<?php echo $rank_id ?>"><?php echo $rank_name ?></option>
                                 <?php endforeach; ?>
                               </select>
                             </div>
                           </div>
                           <div class="col-md-6">
                             <div class="mb-3">
                               <label class="form-label">Credit Limit </label>
                               <input name="credit_limit" id="credit_limit" type="text" class="form-control" placeholder="Enter Credit Limit "/>
                             </div>
                           </div>

                         </div>
                       </div>
                       <div class="modal-footer">
                         <input type="hidden" id="edit_user_id" name="edit_user_id" value="">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" name="edit_pengguna" class="btn btn-primary">Save changes</button>
                       </div>
                     </form>
                   </div>
                 </div>
               </div>
               <!-- end edit user modal -->

               <!-- edit password modal -->
               <div
                 id="modalEdiPassword"
                 class="modal fade"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="modalEdiPasswordContent"
                 aria-hidden="true"
               >
                 <div class="modal-dialog modal-dialog-centered" role="document">
                   <div class="modal-content">
                     <div class="modal-header">
                       <h5 class="modal-title" id="modalEditUserContent">Edit Agent</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <form method="post">

                       <div class="modal-body">
                         <div class="row">
                           <div class="col-md-12">
                             <div class="mb-3">
                               <label class="form-label">Full Name</label>
                               <input disabled id="display_nama" type="text" class="form-control" placeholder="Enter full name" />
                             </div>
                           </div>
                           <div class="col-md-12">
                             <div class="mb-3">
                               <label class="form-label">New Password</label>
                               <input name="new_password" id="new_password" type="text" class="form-control" placeholder="Enter new password" autocomplete="false"/>
                             </div>
                           </div>

                         </div>
                       </div>
                       <div class="modal-footer">
                         <input type="hidden" id="edit_user_id_p" name="edit_user_id_p" value="">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" name="edit_password" class="btn btn-primary">Save changes</button>
                       </div>
                     </form>
                   </div>
                 </div>
               </div>
               <!--end edit password modal -->

               <!--table orders -->
               <div class=" dt-responsive table-responsive p-2">

                 <table class="table table-hover" id="table-orders">
                   <thead>
                     <tr>
                       <th class="text-center">#</th>
                       <th class="text-center">Id</th>
                       <th class="text-center">Agent</th>
                       <th class="text-center">Phone</th>
                       <th class="text-center">Rank</th>
                       <th class="text-center">Register At</th>
                       <th class="text-center">Actions</th>
                     </tr>
                   </thead>
                   <tbody>

                   </tbody>
                 </table>
               </div>

             </div>
           </div>
         </div>
         <!-- [ sample-page ] end -->
       </div>
       <!-- [ Main Content ] end -->
     </div>
   </div>


   <!-- [ Main Content ] end -->
   <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="add-agent-form" aria-labelledby="announcementLabel">
     <div class="offcanvas-header">
       <h5 class="offcanvas-title" id="announcementLabel">Add New Agent</h5>
       <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
     </div>
     <div class="offcanvas-body">
       <form method="post" enctype="multipart/form-data" >
         <div class="mb-3">
           <label class="form-label">Full Name</label>
           <input required  name="nama" type="text" class="form-control" placeholder="Enter full name" />
         </div>
         <div class="mb-3 ">
           <label class="form-label">Email</label>
           <input required name="email" type="email" class="form-control" placeholder="Enter email" />
         </div>
         <div class="mb-3">
           <label class="form-label">Phone No </label>
           <input required name="no_tel" type="text" class="form-control" placeholder="Enter Phone No"/>
         </div>
         <div class="mb-3">
           <label class="form-label">Agent Rank</label>
           <select class="form-select" required name="rank_id">
             <option>Select Rank</option>
             <?php foreach ($ranks_ref as $rank_id => $rank_name): ?>
               <option value="<?php echo $rank_id ?>"><?php echo $rank_name ?></option>
             <?php endforeach; ?>
           </select>
         </div>
         <div class="mb-3">
           <label class="form-label">Password </label>
           <input required name="password" type="text" class="form-control" placeholder="" value="secret#123"/>
         </div>

         <div class="mb-3">
           <label class="form-label">Credit Limit </label>
           <input name="credit_limit" type="text" class="form-control" placeholder="Enter Credit Limit "/>
         </div>

         <button  class="btn btn-sm btn-primary" type="submit" name="simpan_pengguna" >Submit</button>
         <input class="pull-right btn btn-sm btn-outline-primary" type="Reset" name="" value="Reset">
         <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="offcanvas">Cancel</button>
       </form>
     </div>
   </div>


   <?php include 'partial/footer.php'; ?>
   <?php include 'partial/scripts.php'; ?>

  <script src="../assets/js/plugins/sweetalert2.all.min.js"></script>

   <script src="../assets/plugins/dropify/dist/js/dropify.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
  <script src="../assets/js/plugins/dataTables.min.js"></script>
  <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
   <script type="text/javascript">

    $(document).ready(function () {
       $('.dropify').dropify();
    });
   </script>
   <script src="../assets/js/plugins/simple-datatables.js"></script>
   <script>
      var ranks_ref = <?php echo json_encode($ranks_ref) ?>;
       var tableOrder = $('#table-orders').on('init.dt', function () {
           $(".dt-empty").text('No data available');
           console.log('Table initialisation complete: ' + new Date().getTime());
       }).DataTable({
         serverSide: true,
         ajax: {
           url: '../shared/agents/get_agents.php',
           type: 'POST'
         },
         pageLength: 10,
         lengthMenu: [
           5, 10, 20, 50
         ],
         columns: [
           {
               data: 'DT_RowIndex',
               render: function ( data, type, full, meta ) {
                 var pageInfo = $('#table-orders').DataTable().page.info();
                 return meta.row + 1 + pageInfo.start;
                    return  meta.row + 1;
                },
           },
           {
             data: 'id',
             render: function(data, type, row, meta) {
               // Display badges for statuses
               if (type === 'display') {
                 return `<div>Agent#${data}</div>`;

               }
               return data;
             }
           }, {
             data: 'nama',
             render: function(data, type, row, meta) {
               // Display badges for statuses
               if (type === 'display') {
                 return `<div>${data}</div>
                  <small clas="text-muted">${row.email}</small>
                 `;

               }
               return data;
             }
           },{
            data: 'no_tel'
          }, {
            data: 'rank_id',
            render: function(data, type, row, meta) {
              // Display badges for statuses
              if (type === 'display') {
                return `<span class="badge bg-${ranks_ref[data]}">${ranks_ref[data]}</span>`;
              }
              return data;
            }
           }, {
             data: 'created_at'
           },{
             data: 'id',
             render: function(data, type, row, meta) {
               // Display badges for statuses
               if (type === 'display') {
                 // var userData = JSON.stringify(row);
                 return `<ul class="list-inline me-auto mb-0 text-center">
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                              <a href="#" onclick="editUser(this)" data-row='${JSON.stringify(row)}' class="avtar avtar-xs btn-link-success btn-pc-default">
                                <i class="ti ti-edit-circle f-18"></i>
                              </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Change Password">
                              <a href="#" onclick="editPassword(this)" data-row='${JSON.stringify(row)}' class="avtar avtar-xs btn-link-success btn-pc-default">
                                <i class="ti ti-shield-lock f-18"></i>
                              </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                              <form id="form_product${data}" method="post">

                                <input type="hidden" name="id_members_to_delete" value="${data}">
                                <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default"  onclick="deleteThisItem('#form_product${data}')">

                              </form>

                                <i class="ti ti-trash f-18"></i>
                              </a>
                            </li>
                          </ul>
                          `;
               }
               return data;
             }
           }
         ],
         columnDefs: [
           {
             targets: 0,
             className: 'dt-center'
           }, {
             targets: [1],
             className: 'dt-center'
           }, {
             targets: [2],
             className: 'dt-center'
           }, {
             targets: [3],
             className: 'dt-center'
           }, {
             targets: [4],
             className: 'dt-center'
           }, {
             targets: [5],
             className: 'dt-center'
           },
         ]
       });

     function deleteThisItem(the_item_form) {
       Swal.fire({
         title: 'Are you sure?',
         showDenyButton: true,
         showCancelButton: true,
         confirmButtonText: `Yes, delete it.`,
         denyButtonText: `No`
       }).then((result) => {
         if (result.isConfirmed) {
           $(the_item_form).submit();
           // Swal.fire('Saved!', '', 'success');
         } else if (result.isDenied) {
           // Swal.fire('Changes are not saved', '', 'info');
         }
       });
     }

  function editUser(button) {
    const user = JSON.parse(button.getAttribute('data-row'));

    $("#nama, #email, #no_tel, #rank_id, #edit_user_id").val('');
    $("#nama").val(user.nama);
    $("#email").val(user.email);
    $("#no_tel").val(user.no_tel);
    $("#rank_id").val(user.rank_id);
    $("#credit_limit").val(user.credit_limit);
    $("#edit_user_id").val(user.id);
    $("#modalEditUser").modal('show');
  }

  function editPassword(button) {
    const user = JSON.parse(button.getAttribute('data-row'));

    $("#display_nama, #edit_user_id_p").val('');
    $("#display_nama").val(user.nama);
    $("#edit_user_id_p").val(user.id);
    $("#modalEdiPassword").modal('show');
  }

   </script>

 </body>
 <!-- [Body] end -->

 </html>
