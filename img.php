<?php
/*
*图片主要（三通道）颜色判断
*author cuitengwei
*2014/1/16
*/
function imgColor($imgUrl)
{

    /*    $imageInfo = getimagesize($imgUrl);

        //图片类型

        $imgType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));

        //对应函数

        $imageFun = 'imagecreatefrom' . ($imgType == 'jpg' ? 'jpeg' : $imgType);

        $i = $imageFun($imgUrl);

        //循环色值

        $rColorNum=$gColorNum=$bColorNum=$total=0;

        for ($x=0;$x<imagesx($i);$x++) {

            for ($y=0;$y<imagesy($i);$y++) {

                $rgb = imagecolorat($i,$x,$y);

                //三通道

                $r = ($rgb >> 16) & 0xFF;

                $g = ($rgb >> 8) & 0xFF;

                $b = $rgb & 0xFF;

                $rColorNum += $r;

                $gColorNum += $g;

                $bColorNum += $b;

                $total++;

            }

        }

        $rgb = array();

        $rgb['r'] = round($rColorNum/$total);

        $rgb['g'] = round($gColorNum/$total);

        $rgb['b'] = round($bColorNum/$total);

    */

    $im = imagecreatefrompng($imgUrl);

    $rgb = imagecolorat($im, 10, 15);

    $r = ($rgb >> 16) & 0xFF;

    $g = ($rgb >> 8) & 0xFF;

    $b = $rgb & 0xFF;

    $rgb = array();

    $rgb['r'] = $r;

    $rgb['g'] = $g;

    $rgb['b'] = $rgb;

    return $rgb;

}

/*

*RGB TO HEX

*author cuitengwei

*2014/1/16

*/

function rgb2html($r, $g = -1, $b = -1)

{

    if (is_array($r) && sizeof($r) == 3)

        list($r, $g, $b) = $r;

    $r = intval($r);
    $g = intval($g);

    $b = intval($b);

    $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));

    $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));

    $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));

    $color = (strlen($r) < 2 ? '0' : '') . $r;

    $color .= (strlen($g) < 2 ? '0' : '') . $g;

    $color .= (strlen($b) < 2 ? '0' : '') . $b;

    return '#' . $color;

}

/*

*HEX TO RGB

*author cuitengwei

*2014/1/16

*/

function html2rgb($color)

{

    if ($color[0] == '#')

        $color = substr($color, 1);

    if (strlen($color) == 6)

        list($r, $g, $b) = array($color[0] . $color[1],

            $color[2] . $color[3],

            $color[4] . $color[5]);

    elseif (strlen($color) == 3)

        list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);

    else

        return false;

    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array($r, $g, $b);

}

//使用示例

$imgUrl = "e:/tt/4.png";//图片地址

$rgb_arr = imgColor($imgUrl);//提取图片中主颜色值 返回数组array('r'=>164,'g'=>194,'b'=>105)

$ys_16 = rgb2html($rgb_arr['r'], $rgb_arr['g'], $rgb_arr['b']);//RGB值转化成16进制，返回如：#ff0000

//html2rgb($ys_16);//16进制颜色值转化成RGB 返回数组array('0'=>240,'1'=>128,'2'=>152)

echo $ys_16;

?>

<div style="background:<?php echo $ys_16 ?>; height:100px; width:100px;"></div>