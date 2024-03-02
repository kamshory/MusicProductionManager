<?php
namespace MusicProductionManager\Utility;

use GdImage;
use stdClass;

class ImageUtil
{
    
    /**
     * Create thumbnail image
     *
     * @param string $originalfile
     * @param integer $dwidth
     * @param integer $dheight
     * @param boolean $interlace
     * @return GdImage|resource|bool
     */
    public static function createThumbImage($originalfile, $dwidth, $dheight, $interlace=true) 
    {
        $imageinfo = getimagesize($originalfile);
        $image = new StdClass();
        if(!isset($imageinfo[0]) || !isset($imageinfo[1]))  
        {
            if (file_exists($originalfile)) {
                unlink($originalfile);
            }
            return false;
        }
        $image->width  = $imageinfo[0];
        $image->height = $imageinfo[1];
        $image->type   = $imageinfo[2];
        switch ($image->type) {
            case IMAGETYPE_GIF:
                if (function_exists('ImageCreateFromGIF')) 
                {
                    $im = @ImageCreateFromGIF($originalfile);
                } 
                else 
                {
                    unlink($originalfile);
                    return false;
                }
                break;
            case IMAGETYPE_JPEG:
                if (function_exists('ImageCreateFromJPEG')) 
                {
                    $im = @ImageCreateFromJPEG($originalfile);
                } 
                else 
                {
                    unlink($originalfile);
                    return false;
                }
                break;
            case IMAGETYPE_PNG:
                if (function_exists('ImageCreateFromPNG')) 
                {
                    $im = @ImageCreateFromPNG($originalfile);
                } 
                else 
                {
                    unlink($originalfile);
                    return false;
                }
                break;
            default:
                unlink($originalfile);
                return false;
        }
        
        // calculate ratio
        
        $ww = $image->width;
        $hh = $image->height;
        
        $ratio1 = $dheight/$dwidth;
        $ratio2 = $dwidth/$dheight;
        
        $resized = self::resizeWithAspectRatio($hh, $ww, $ratio1, $ratio2);

        $ww2 = $resized->ww2;
        $hh2 = $resized->hh2;
        $x2 = $resized->x2;
        $y2 = $resized->y2;

        $im1 = imagecreatetruecolor($ww2,$hh2); 
        imagecopyresampled($im1, $im, 0, 0, $x2, $y2, $ww2, $hh2, $ww2, $hh2);
        $im2 = imagecreatetruecolor($dwidth, $dheight); 	
        imagecopyresampled($im2, $im1, 0, 0, 0, 0, $dwidth, $dheight, $ww2, $hh2);
        if($interlace)
        {
            imageinterlace($im2, true);
        }
        return $im2;
    }

    /**
     * Resize with aspect ratio
     *
     * @param integer|float $hh
     * @param integer|float $ww
     * @param integer|float $ratio1
     * @param integer|float $ratio2
     * @return stdClass
     */
    public static function resizeWithAspectRatio($hh, $ww, $ratio1, $ratio2)
    {
        if($hh>= ($ratio1*$ww))
        {
            // tinggi gambar lebih dari rasio
            // pemotongan vertikal
            $hh2 = (int)($ratio1*$ww);
            $ww2 = $ww;
            $y2 = (int)(($hh-$hh2)/2);
            $x2 = 0;
        }
        else
        {
            $ww2 = (int)($ratio2*$hh);
            $hh2 = $hh;
            $x2 = (int)(($ww-$ww2)/2);
            $y2 = 0;
        }
        $resized = new stdClass;
        $resized->ww2 = $ww2;
        $resized->hh2 = $hh2;
        $resized->x2 = $x2;
        $resized->y2 = $y2;
        return $resized;
    }

    /**
     * Get binary data
     *
     * @param GdImage|resource|bool $imagedata
     * @return string
     */
    public static function imageToString($imagedata)
    {
        ob_start();
        imagepng($imagedata);
        $stringdata = ob_get_contents(); // read from buffer
        ob_end_clean(); // delete buffer
        return $stringdata;
    }
}