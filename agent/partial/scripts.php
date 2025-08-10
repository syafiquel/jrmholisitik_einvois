<!-- Required Js -->
<script
src="https://code.jquery.com/jquery-3.7.1.min.js"
integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
crossorigin="anonymous"></script>
<script src="../assets/js/plugins/popper.min.js"></script>
<script src="../assets/js/plugins/simplebar.min.js"></script>
<script src="../assets/js/plugins/bootstrap.min.js"></script>
<script src="../assets/js/fonts/custom-font.js"></script>
<script src="../assets/js/pcoded.js"></script>
<script src="../assets/js/plugins/feather.min.js"></script>
<script src="../assets/js/plugins/sweetalert2.all.min.js"></script>
<!-- <script src="../assets/js/component.js"></script> -->
<!-- <div class="floting-button">
  <a href="https://1.envato.market/zNkqj6" class="btn btn btn-danger buynowlinks d-inline-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Buy Now">
    <i class="ph-duotone ph-shopping-cart"></i>
    <span>Buy Now</span>

  </a>
</div> -->
<script type="text/javascript">
const credit_info = [];
function mainUpdateCart(user_id = '<?php echo $user_id ?>') {
  $.ajax({
    url: "../shared/shop_executer/update_cart.php",
    data:{user_id},
    type: "POST",
    dataType: 'json',
    success:function(data){
      // console.log(data);
      $("#cartButton").text(data);
      // if (data > 0) {
      //   $("#cart-floating").show();
      // }
    },
    error:function (){}
  });
}
$(document).ready(function() {
  mainUpdateCart();
});

</script>

<script>
  layout_change('light');
</script>

<script>
  change_box_container('true');
</script>

<script>
  layout_caption_change('true');
</script>

<script>
  layout_rtl_change('false');
</script>

<script>
  preset_change('preset-3');
</script>


<script type="text/javascript">

<?php if(!empty($_SESSION["error"])) {
      $text = $_SESSION["error"] ?? '';
      $status = 'error';
    }else{
      $text = $_SESSION["success"] ?? '';
      $status = 'success';
    }
?>
<?php if(!empty($_SESSION["error"]) || !empty($_SESSION["success"])) { ?>
    $(document).ready(function() {
      Swal.fire({
        icon: "<?php echo $status ?>",
        title: "<?php echo strtoupper($status) ?>",
        text: "<?php echo $text ?>"
      });
      // swal({
      //   title: "<?php echo strtoupper($status) ?>",
      //   text: "<?php echo $text ?>",
      //   icon: "<?php echo $status ?>",
      //   button: "Ok",
      // });
    });
<?php
}
$_SESSION["error"]="";
$_SESSION["success"]="";
?>
</script>

<script>
  main_layout_change('<?php echo $main_orientation ?>');
</script>
