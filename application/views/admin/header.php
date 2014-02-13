<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">

        <title><?= (isset($layout_title) ? $layout_title : '') ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" href="<?= base_url() ?>favicon.ico"/>

        <meta http-equiv="cleartype" content="on">

	<script type='text/javascript' src='<?=base_url()?>js/steal/steal.js'>
	</script>

	<script type="text/javascript">
            <?php
                include(APPPATH . 'language/'.$this->session->userdata('lang_iso').'/inter_lang.php');
                echo 'var lang = ' . json_encode($localization).';';
                echo 'var pref = "' . $pref.'";';
                echo 'var langs = ' . json_encode($language).';';
            ?>
            var base_url = '<?=base_url()?>';
	</script>
