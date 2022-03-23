<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;
use \Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\HtmlElement\HtmlElement;

abstract class BaseModel extends Eloquent 
{
    public static function getTableName($alias = NULL)
    {   
        $alias = $alias ? (' as '.$alias) : '';

        $tablename = with(new static)->getTable();

        return $tablename.$alias;
    }
    public static function as($alias=null)
    {
        return self::getTableName($alias);
    }
    public static function pas($alias=null) # with prefix
    {
        return self::getPrefix().self::getTableName($alias);
    }
    public static function fieldPrefix($alias=null)
    {
        return self::getPrefix().($alias ?? '');        
    }
    public static function getPrefix()
    {
        return DB::connection(with(new static)->getConnectionName())->getTablePrefix();
    }
    public static function getTableNameWithPrefix($alias = NULL)
    {
        $alias = $alias ? (' as '.$alias) : '';

        return self::getPrefix().self::getTableName().$alias;
    }
    public function scopeIsActive($query, $alias='', $is_raw=false) 
    {
        /* do not use if table has no column "active" */

        $alias .= strlen($alias) ? '.' : '';

    	$is_raw ? $query->where(DB::raw($alias.'active'), 1) : $query->where($alias.'active', 1);
    }
    public function scopeIsInactive($query, $alias='') 
    {
        /* do not use if table has no column "active" */

        $alias .= strlen($alias) ? '.' : '';

        $query->where($alias.'active', 0);
    }
}