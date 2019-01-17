<?php
/**
 * Jill Query module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license    http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package    Jill Query
 * @since      2.5
 * @author     jill lee(tnjaile@gmail.com)
 * @version    $Id $
 **/

// function xoops_module_uninstall_jill_query($module)
// {
//     global $xoopsDB;
//     $date = date("Ymd");

//     rename(XOOPS_ROOT_PATH . "/uploads/jill_query", XOOPS_ROOT_PATH . "/uploads/jill_query_bak_{$date}");

//     return true;
// }

//刪除目錄
// function delete_directory($dirname)
// {
//     if (is_dir($dirname)) {
//         $dir_handle = opendir($dirname);
//     }

//     if (!$dir_handle) {
//         return false;
//     }

//     while ($file = readdir($dir_handle)) {
//         if ($file != "." && $file != "..") {
//             if (!is_dir($dirname . "/" . $file)) {
//                 unlink($dirname . "/" . $file);
//             } else {
//                 delete_directory($dirname . '/' . $file);
//             }

//         }
//     }
//     closedir($dir_handle);
//     rmdir($dirname);
//     return true;
// }

//拷貝目錄
// function full_copy($source = "", $target = "")
// {
//     if (is_dir($source)) {
//         @mkdir($target);
//         $d = dir($source);
//         while (false !== ($entry = $d->read())) {
//             if ($entry == '.' || $entry == '..') {
//                 continue;
//             }

//             $Entry = $source . '/' . $entry;
//             if (is_dir($Entry)) {
//                 full_copy($Entry, $target . '/' . $entry);
//                 continue;
//             }
//             copy($Entry, $target . '/' . $entry);
//         }
//         $d->close();
//     } else {
//         copy($source, $target);
//     }
// }
