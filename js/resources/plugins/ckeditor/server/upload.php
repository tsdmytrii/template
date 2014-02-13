<?php

include "../config.php";


$_FILES['upload']['type'] = strtolower($_FILES['upload']['type']);

if ($_FILES['upload']['type'] == 'image/png'
|| $_FILES['upload']['type'] == 'image/jpg'
|| $_FILES['upload']['type'] == 'image/gif'
|| $_FILES['upload']['type'] == 'image/jpeg'
|| $_FILES['upload']['type'] == 'image/pjpeg')
{
	$filename = md5(date('YmdHis')).'.jpg';

	copy($_FILES['upload']['tmp_name'], IMAGES_ROOT.$filename);

//        $string = '<script>window.parent.CKEDITOR.tools.callFunction(2, '.IMAGES_ROOT.$filename.', "");</script>';
        $string = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$_GET['CKEditorFuncNum'].', "'.HTTP_IMAGES_ROOT.$filename.'", "");</script>';

	echo $string;
}
?>




