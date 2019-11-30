<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Support\RegistryManagerInterface;

class GlobalVars implements RegistryManagerInterface
{

    private static $_Globals = [];

    const CACHE_KEY = 'fwk-console';


    public static function Destroy()
    {
        self::$_Globals = [];

        self::Save();
    }

    public static function Store($key, $value)
    {
        self::$_Globals[$key] = $value;

        self::Save();
    }

    public static function Read($key)
    {
        self::Load();

        return self::$_Globals[$key] ?? null;
    }

    public static function Clear($key)
    {
        if (isset(self::$_Globals[$key]))

            unset(self::$_Globals[$key]);

        self::Save();
    }

    /**
     * @param $key
     * @return bool
     */
    public static function Has($key)
    {
        return isset(self::$_Globals[$key]);
    }

    public static function List()
    {
        return self::$_Globals;
    }

    public static function Use(array $array)
    {
        self::$_Globals = $array;
    }

    public static function Load()
    {
        static $loaded;

        if ($loaded) return;

        self::$_Globals = FrameworkCache::ReadVar(self::CACHE_KEY);

        $loaded = true;

    }

    public static function Save()
    {
        FrameworkCache::StoreVar(self::CACHE_KEY, self::$_Globals);
    }

}