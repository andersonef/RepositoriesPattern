<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 11:08
 */

namespace Andersonef\Repositories\Abstracts;


use \Andersonef\Repositories\Contracts\ServiceContract;
use Illuminate\Database\DatabaseManager;

/** Service Layer. Use this layer to store all you app rules. Instead write your app rules directly on your controllers,
 *  write it on the service layer, and makes your controller call the service. This way you will write your app rules only once.
 *  For instance: Instead of:
 *  Http/
 *      /Controllers/
 *          StoreController.php
 *              function postProcessTransaction() => transaction rules
 *          Admin/
 *              StoreController.php
 *                  function postProcessTransaction() => some specific validations and the same rules from /Controllers/StoreController.
 *
 * You will do:
 *  Http/
 *      Controllers/
 *          StoreController.php
 *              function postProcessTransaction() => $this->StoreService->processTransaction();
 *          Admin/
 *              StoreController.php
 *                  function postProcessTransaction() =>
 *                          //specific validations
 *                          $this->StoreService->processTransaction();
 *
 * This way, if you need to update your app rules, you will do it only at service layer.
 *
 * In this class you can use the default methods but you can implement your custom methods or rewrite this custom methods.
 * For Instance:
 *  class YourCustomService extends ServiceAbstract{
 *      protected $AnotherService;
 *
 *      function __construct(AnotherCustomService $service){
 *          $this->AnotherCustomService = $service;
 *      }
 *      public function create(array $data){
 *          //custom rules
 *          return parent::create($data);
 *      }
 *
 *      public function customMethod($param1, $param2){
 *          $object = parent::create($param1);
 *          //send mail
 *          $this->AnotherCustomService->create($param2);
 *      }
 * }
 * Class ServiceAbstract
 * @package Andersonef\Repositories\Abstracts
 */
class ServiceAbstract implements ServiceContract {

    protected $Repository;
    protected $db;


    function __construct(RepositoryAbstract $ra, DatabaseManager $db)
    {
        $this->Repository = $ra;
        $this->db = $db;
    }

    public function create(array $data)
    {
        try{
            $this->db->beginTransaction();
            $entity = $this->Repository->create($data);
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollback();
            throw $e;
        }
        return $entity;
    }

    public function update(array $data, $id)
    {
        try{
            $this->db->beginTransaction();
            $this->Repository->update($data, $id);
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollback();
            throw $e;
        }
    }

    public function destroy($id)
    {
        try{
            $this->db->beginTransaction();
            $this->Repository->delete($id);
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollback();
            throw $e;
        }
    }

    public function getRepository()
    {
        return $this->Repository;
    }

    public function __call($method, $arguments = [])
    {
        return call_user_func_array([$this->Repository, $method], $arguments);
    }


}