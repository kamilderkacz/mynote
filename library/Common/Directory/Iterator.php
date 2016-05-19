<?php
/* 
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage directory
 */
class Common_Directory_Iterator extends DirectoryIterator
{
    public function GetExtension()
    {
        $Filename = $this->GetFilename();
        $FileExtension = strrpos($Filename, ".", 1) + 1;
        if ($FileExtension != false)
            return strtolower(substr($Filename, $FileExtension, strlen($Filename) - $FileExtension));
        else
            return "";
    }
}