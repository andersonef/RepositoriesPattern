<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 10:04
 */

namespace Andersonef\Repositories\Contracts;


interface ServiceContract {

    public function create(array $data);

    public function update(array $data, $id);

    public function destroy($id);

    public function getRepository();


}