<?php include(APPPATH . 'language/'.$this->session->userdata('lang_iso').'/inter_lang.php');?>

<div class="container well">

    <form class="form-signin" action="<?= base_url() ?>admin/login/process" method="post" style="padding-bottom: 45px;">
        <h2 class="form-signin-heading"><?php echo $localization['sign_in_label'];?></h2>
        <input type="text" class="form-control" placeholder="<?php echo $localization['login'];?>" autofocus="" name="login">
        <input type="password" class="form-control" placeholder="<?php echo $localization['password'];?>" name="password">
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo $localization['sign_in'];?>&nbsp;<span class="glyphicon glyphicon-log-in"></span></button>
    </form>

</div>