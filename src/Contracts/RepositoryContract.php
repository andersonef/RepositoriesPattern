<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 10:03
 */

namespace Andersonef\Repositories\Contracts;


interface RepositoryContract {

    public function all(array $columns = ['*']);

    public function paginate($perpage = 15, $columns = ['*']);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id, $columns = ['*']);

    public function findBy($fields, $columns = ['*']);

}