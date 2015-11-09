<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 16:31
 */

namespace Andersonef\Repositories\Criterias;


use Andersonef\Repositories\Abstracts\CriteriaAbstract;
use Andersonef\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Model;

class FindUsingLikeCriteria extends CriteriaAbstract{

    public static $ST_USE_AT_BEGIN = "uselikeatbegin";
    public static $ST_USE_AT_END = "useatend";
    public static $ST_USE_AT_BOTH = "useatboth";

    private $query;
    private $type;
    private $fields;

    function __construct($query, $type = "useatboth", array $fields = null)
    {
        $this->query = $query;
        $this->type = $type;
        $this->fields = $fields;
    }

    public function apply(Model $model, RepositoryContract $repository)
    {
        $fields = $this->fields;
        $query = $this->query;
        if(!$fields)
            $fields = $model->getFillable();

        switch($this->type){
            case self::$ST_USE_AT_BEGIN: $query = '%'.$this->query; break;
            case self::$ST_USE_AT_END: $query = $this->query.'%'; break;
            case self::$ST_USE_AT_BOTH: $query = '%'.$this->query.'%'; break;
        }
        $query = $model->where(function($q) use($fields, $query){
            foreach($fields as $i => $field)
            {
                if($i == 0){
                    $q->where($field, 'like', $query);
                    continue;
                }
                $q->orWhere($field, 'like', $query);
            }
        });

        return $query;
    }



}