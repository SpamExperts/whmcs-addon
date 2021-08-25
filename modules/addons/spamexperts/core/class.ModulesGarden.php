<?php

if(!class_exists('ModulesGarden'))
{
    class ModulesGarden
    {
        /**
         * Get Module Class Name
         * @param string $path
         * @return string
         */
        public static function getModuleClass($path)
        {
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
            return basename(dirname($path));
        }
    }
}

?>
