<?php
  function check_role($role_no){
    // echo $role_no. "|". $team;
    // exit;
    if($role_no == '0')
    {
      return "trial";
    }else{
      if($role_no == 99)
      { return "superadmin"; }
      elseif($role_no == 66)
      { return "admin"; }
      elseif($role_no == 88)
      { return "designer"; }
      elseif($role_no == 1)
      { return "agent"; }
    }
  }
?>
