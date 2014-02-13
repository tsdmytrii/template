<?php

/**
 * Description of Image
 *
 * @author andrew
 */
class Image {

    static $ci;

    private static function _init() {
        self::$ci = & get_instance();
        self::$ci->load->library('upload');
        //  self::$ci->load->helper('url');
//        self::$ci->load->model('core/image_model');
        log_message('debug', "Image Class Initialized");
    }

    public static function validate($type) {
        switch ($type) {
            case('resize'):
                $rules = array(
                    array(
                        'field' => 'filename',
                        'label' => 'Image file name',
                        'rules' => 'required|trim'),
                    array(
                        'field' => 'height',
                        'label' => 'Image height',
                        'rules' => 'required|integer'
                    ),
                    array(
                        'field' => 'width',
                        'label' => 'Image width',
                        'rules' => 'required|integer'
                    ),
                    array(
                        'field' => 'x1',
                        'label' => 'Image x1',
                        'rules' => 'required|integer'
                    ),
                    array(
                        'field' => 'y1',
                        'label' => 'Image y1',
                        'rules' => 'required|integer'
                    ),
                    array(
                        'field' => 'x2',
                        'label' => 'Image x2',
                        'rules' => 'required|integer'
                    ),
                    array(
                        'field' => 'y1',
                        'label' => 'Image y2',
                        'rules' => 'required|integer'
                    )
                );
                break;
        }

        self::$ci->validate->set_rules($rules);
        return self::$ci->validate->run();
    }

    public static function upload_tmp_image($upload_path = './uploads', $filename = '', $field_name = 'Filedata') {
        if (empty(self::$ci)) {
            self::_init();
        }
        if (empty($filename)) {
            $filename = microtime() * rand(0, 100);
            $filename = sha1($filename);
        }
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['overwrite'] = 'true';
        $file_extension = explode('.', $_FILES[$field_name]['name']);
        $file_extension = $file_extension['1'];
        $file_name = $filename . '.' . $file_extension;
        if ($file_extension == 'png') {
            $_FILES[$field_name]['type'] = "image/png";
        } elseif ($file_extension == 'gif') {
            $_FILES[$field_name]['type'] = "image/gif";
        } else {
            $_FILES[$field_name]['type'] = "image/jpeg";
        }
        $_FILES[$field_name]['name'] = "$file_name";
        self::$ci->upload->initialize($config);
        if (self::$ci->upload->do_upload($field_name)) {
            $data = self::$ci->upload->data(); // retrieving data about image
//            if ($data['image_width'] > $data['image_height'])
//                $side = $data['image_width'];
//            else
//                $side = $data['image_height'];
//            $diff = $side / self::$cache_size;


            $result = (array('type' => 'success', 'filename' => $file_name, 'width' => $data['image_width'], 'height' => $data['image_height']));
            //  print json_encode($result);
            log_message('error', json_encode($result));
            return $result;
            //     }
            //   else {
            //     return false;
            //         }
        } else {
            //   $error = array('error' => self::$ci->upload->display_errors());
            $result['result'] = "false";
            $result['errors'] = self::$ci->upload->display_errors();
            log_message('error', self::$ci->upload->display_errors());
            print json_encode($result);
            return false;
        }
    }

    private static function cache_image($image, $cacheImage, $size, $cache_dir, $imageType = 'jpg') {
        if (file_exists($image)) {
            if (!file_exists($cache_dir . $cacheImage)) {
                $imageGd = ($imageType == 'gif' ? imagecreatefromgif($image) : imagecreatefromjpeg($image));
                $x = imagesx($imageGd);
                $y = imagesy($imageGd);
                $max_x = ((int) $size) * 3;
                /* Size is already ok */
                if ($y < $size && $x <= $max_x)
                    copy($image, $cache_dir . $cacheImage);

                /* We need to resize */
                else {
                    $ratioX = $x / ($y / $size);
                    if ($ratioX > $max_x) {
                        $ratioX = $max_x;
                        $size = $y / ($x / $max_x);
                    }
                    $newImage = ($imageType == 'gif' ? imagecreate($ratioX, $size) : imagecreatetruecolor($ratioX, $size));

                    /* Allow to keep nice look even if resized */
                    $white = imagecolorallocate($newImage, 255, 255, 255);
                    imagefill($newImage, 0, 0, $white);
                    imagecopyresampled($newImage, $imageGd, 0, 0, 0, 0, $ratioX, $size, $x, $y);
                    imagecolortransparent($newImage, $white);

                    /* Quality alteration and image creation */
                    if ($imageType == 'gif')
                        imagegif($newImage, $cache_dir . $cacheImage);
                    else
                        imagejpeg($newImage, $cache_dir . $cacheImage, 86);
                }
            }
            return '<img src="../img/tmp/' . $cacheImage . '" alt="" class="imgm" />';
        }
        return '';
    }

    public static function resize_image($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg') {
        list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
        if (!$sourceWidth)
            return false;
        if ($destWidth == NULL)
            $destWidth = $sourceWidth;
        if ($destHeight == NULL)
            $destHeight = $sourceHeight;

        $sourceImage = self::create_src_image($type, $sourceFile);

        $widthDiff = $destWidth / $sourceWidth;
        $heightDiff = $destHeight / $sourceHeight;

        if ($widthDiff > 1 AND $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($widthDiff > $heightDiff) {
                $nextHeight = $destHeight;
                $nextWidth = intval(($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth = $destWidth;
            } else {
                $nextWidth = $destWidth;
                $nextHeight = intval($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = $destHeight;
            }
        }

        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        $white = imagecolorallocate($destImage, 255, 255, 255);
        imagefill($destImage, 0, 0, $white);

        //    imagecopyresampled($destImage, $sourceImage, (int) (($destWidth - $nextWidth) / 2), (int) (($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        //imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        imagecopyresized($destImage, $sourceImage, (int) (($destWidth - $nextWidth) / 2), (int) (($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        imagecolortransparent($destImage, $white);
        return (self::return_dest_image($fileType, $destImage, $destFile));
    }

    private static function return_dest_image($type, $ressource, $filename) {
        $flag = false;
        switch ($type) {
            case 'gif':
                $flag = imagegif($ressource, $filename);
                break;
            case 'png':
                $flag = imagepng($ressource, $filename, 7);
                break;
            case 'jpeg':
            default:
                $flag = imagejpeg($ressource, $filename, 90);
                break;
        }
        imagedestroy($ressource);
        return $flag;
    }

    private static function create_src_image($type, $filename) {
        switch ($type) {
            case 1:
                return imagecreatefromgif($filename);
                break;
            case 3:
                return imagecreatefrompng($filename);
                break;
            case 2:
            default:
                return imagecreatefromjpeg($filename);
                break;
        }
    }

    public static function crop_image($srcFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg', $destX = 0, $destY = 0) {
        if (!file_exists($srcFile))
            return false;

        // Source infos
        $srcInfos = getimagesize($srcFile);
        $src['width'] = $srcInfos[0];
        $src['height'] = $srcInfos[1];
        $src['ressource'] = self::create_src_image($srcInfos[2], $srcFile);

        // Destination infos
        $dest['x'] = $destX;
        $dest['y'] = $destY;
        $dest['width'] = $destWidth != NULL ? $destWidth : $src['width'];
        $dest['height'] = $destHeight != NULL ? $destHeight : $src['height'];
        $dest['ressource'] = self::create_dest_image($dest['width'], $dest['height']);

        $white = imagecolorallocate($dest['ressource'], 255, 255, 255);
        imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
        imagecolortransparent($dest['ressource'], $white);
        $return = self::return_dest_image($fileType, $dest['ressource'], $destFile);
        return ($return);
    }

    public static function create_dest_image($width, $height) {
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        return $image;
    }

    public static function create_logo($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg') {
        list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
        if (!$sourceWidth)
            return false;
        if ($destWidth == NULL)
            $destWidth = $sourceWidth;
        if ($destHeight == NULL)
            $destHeight = $sourceHeight;

        $sourceImage = self::create_src_image($type, $sourceFile);

        $widthDiff = $destWidth / $sourceWidth;
        $heightDiff = $destHeight / $sourceHeight;

        if ($widthDiff > 1 AND $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($widthDiff > $heightDiff) {
                $nextHeight = $destHeight;
                $nextWidth = intval(($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth = $destWidth;
            } else {
                $nextWidth = $destWidth;
                $nextHeight = intval($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = $destHeight;
            }
        }

        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        $white = imagecolorallocate($destImage, 255, 255, 255);
        imagefill($destImage, 0, 0, $white);

        imagecopyresampled($destImage, $sourceImage, (int) (($destWidth - $nextWidth) / 2), (int) (($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        // imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        imagecolortransparent($destImage, $white);
        return (self::return_dest_image($fileType, $destImage, $destFile));
    }

    /**
     * @todo make update of image, but it won't be needed, i think
     */
    public static function set_image($image_name, $source) {
        if (empty(self::$ci)) {
            self::_init();
        }
        /** @todo  do we need validation here? */
        ///    if(self::validate('set_logo') === true){
        /** @todo add preg_replace, checking, if exists file extension in passed argument and cut it */
        //  $pure_name = explode('.', self::$ci->input->post('filename'));
        return self::$ci->image_model->add_image(array('name' => $image_name, 'source' => $source));
    }

    public static function get_image($id) {
        if (empty(self::$ci)) {
            self::_init();
        }
        /** @todo make validation if post exists */
        // if (self::validate('get_images') !== false) {

        $image = self::$ci->image_model->get_image(
                array(
                    'id' => $id,
                )
        );
        if (!empty($image))
            return $image[0];
        else
            return false;
        //   }
        // else
        //   return false;
    }

    public static function delete_image_from_db($id) {
        if (empty(self::$ci)) {
            self::_init();
        }
        return self::$ci->image_model->delete_image(array('id' => $id));
    }

    public static function unlink_image($file) {
        return unlink($file);
    }

}

?>