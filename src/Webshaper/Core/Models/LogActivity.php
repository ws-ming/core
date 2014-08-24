<?php namespace Webshaper\Core\Models;


class LogActivity extends BaseModel{

    protected $table = 'activitylog';
    protected $primaryKey = 'intPKActivityLog';
    protected $fillable=[];

    public function setUpdatedAtAttribute($value)
    {
        // Do nothing.
    }

    public function setCreatedAtAttribute($value)
    {
        // Do nothing.
    }
}