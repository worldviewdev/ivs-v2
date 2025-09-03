<?php
/** This file is part of KCFinder project
 *
 *      @desc GD image detection class
 *   @package KCFinder
 *   @version 2.21
 *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
 * @copyright 2010 KCFinder Project
 *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
 *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
 *      @link http://kcfinder.sunhater.com
 */
class type_img
{
    public function checkFile($file, array $config)
    {
        error_log("type_img::checkFile: " . $file);
        // File harus ada
        if (!file_exists($file)) {
            return "File not found.";
        }
        // Cek apakah benar-benar image
        $info = @getimagesize($file);
        if ($info === false) {
            return "File is not a valid image.";
        }
        // Validasi MIME (opsional)
        $mime = mime_content_type($file);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'])) {
            return "Unsupported image type ($mime).";
        }
        // Kalau semua lolos → OK
        error_log("Valid image detected: {$info[0]}x{$info[1]} ($mime)");
        return true;
    }
}