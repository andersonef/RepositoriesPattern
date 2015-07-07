<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 10:13
 */

namespace Andersonef\Repositories\Abstracts;


use Andersonef\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Model;

abstract class CriteriaAbstract {

    /** Implements select rules (where clausules) of the criteria, that will be used inside a service or repository to get custom database data.
     * @param Model $model
     * @param RepositoryContract $rep
     * @return mixed
     */
    abstract public function apply(Model $model, RepositoryContract $rep);
}