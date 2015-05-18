<?php

/**
 * Simple class to translataing MG_Langs
 */

if(!class_exists('MG_Lang'))
{
    class MG_Lang
    {
        private static $lang = null;

        public static function translate($key)
        {
            if(self::$lang == null)
            {
                $language = '';
                if(isset($_SESSION['language'])) // GET LANG FROM SESSION
                { 
                    $language = strtolower($_SESSION['language']);
                }
                else
                {
                    $q = mysql_query("SELECT language FROM tblclients WHERE id = ".$_SESSION['uid']);
                    $row = mysql_fetch_assoc($q); 
                    if($row['language'])
                        $language = $row['language'];
                }

                if(!$language)
                {
                    $q = mysql_query("SELECT value FROM tblconfiguration WHERE setting = 'language' LIMIT 1");
                    $row = mysql_fetch_assoc($q);
                    $language = $row['language'];
                }

                if(!$language)
                {
                    $language = 'english';
                }

                if(file_exists(ADDON_DIR.'/lang/'.$language.'.php'))
                {
                    include ADDON_DIR.'/lang/'.$language.'.php';
                }

                if(isset($LANG))
                {
                    self::$lang = $LANG;
                }
            }

            if(isset(self::$lang[$key]))
            {
                return self::$lang[$key];
            }

            return $key;
        }
    }
}
