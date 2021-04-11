<?php
/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Package related functions
 *
 */
final class DUPX_Package
{

    /**
     *
     * @staticvar string $path
     * @return string
     */
    public static function getWpconfigArkPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = $GLOBALS['DUPX_AC']->installSiteOverwriteOn ? $GLOBALS['DUPX_ROOT'].'/dup-wp-config-arc__'.$GLOBALS['DUPX_AC']->package_hash.'.txt' : $GLOBALS['DUPX_ROOT'].'/wp-config.php';
        }
        return $path;
    }

    /**
     * 
     * @staticvar bool|string $packageHash
     * @return bool|string false if fail
     */
    public static function getPackageHash()
    {
        return $GLOBALS['DUPX_AC']->package_hash;
    }

    /**
     * 
     * @staticvar string $fileHash
     * @return string
     */
    public static function getArchiveFileHash()
    {
        static $fileHash = null;

        if (is_null($fileHash)) {
            $fileHash = preg_replace('/^.+_([a-z0-9]+)_[0-9]{14}_archive\.(?:daf|zip)$/', '$1', $GLOBALS['FW_PACKAGE_PATH']);
        }

        return $fileHash;
    }

    /**
     *
     * @staticvar string $path
     * @return string
     */
    public static function getHtaccessArkPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = $GLOBALS['DUPX_ROOT'].'/.htaccess__'.$GLOBALS['DUPX_AC']->package_hash;
        }
        return $path;
    }

    /**
     *
     * @staticvar string $path
     * @return string
     */
    public static function getOrigWpConfigPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = $GLOBALS['DUPX_INIT'].'/dup-orig-wp-config__'.$GLOBALS['DUPX_AC']->package_hash.'.txt';
        }
        return $path;
    }

    /**
     *
     * @staticvar string $path
     * @return string
     */
    public static function getOrigHtaccessPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = $GLOBALS['DUPX_INIT'].'/dup-orig-wp-config__'.$GLOBALS['DUPX_AC']->package_hash.'.txt';
        }
        return $GLOBALS['DUPX_INIT'].'/dup-orig-htaccess__'.$GLOBALS['DUPX_AC']->package_hash.'.txt';
    }
}