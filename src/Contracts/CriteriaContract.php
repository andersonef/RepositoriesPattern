<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 10:11
 */

namespace Andersonef\Repositories\Contracts;


use Andersonef\Repositories\Abstracts\CriteriaAbstract;

interface CriteriaContract {

    public function skipCriteria($skip = true);

    public function getCriterias();

    public function findByCriteria(CriteriaAbstract $criteria);

    public function pushCriteria(CriteriaAbstract $criteria);

    public function applyCriteria();

}