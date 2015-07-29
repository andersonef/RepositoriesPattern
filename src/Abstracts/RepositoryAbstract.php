<?php
/**
 * Created by PhpStorm.
 * User: ansilva
 * Date: 06/07/2015
 * Time: 10:02
 */

namespace Andersonef\Repositories\Abstracts;


use Andersonef\Repositories\Contracts\CriteriaContract;
use Andersonef\Repositories\Contracts\RepositoryContract;
use Andersonef\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/** Repository class to implement the Repository pattern. Use this class like a DAO.
 * Its responsible for the data access and manipulation rules. Dont write your application rules here.
 * Class RepositoryAbstract
 * @package Andersonef\Repositories\Abstracts
 */
abstract class RepositoryAbstract implements RepositoryContract, CriteriaContract {

    protected $entity;
    protected $skipCriteria = false;
    protected $criterias;

    function __construct(Collection $criterias)
    {

        $this->makeEntity();
        $this->criterias = $criterias;
    }

    /** Must return the class of entity this repository will work
     * @return string
     */
    abstract function entity();

    /** Initialize the entity property in this repository and check the type of it.
     * @throws RepositoryException when the return of entity() method doesn't returns a valid eloquent model class name.
     */
    protected function makeEntity()
    {
        $model = app($this->entity());
        if(!$model instanceof Model)
            throw new RepositoryException("Class {$this->entity()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        $this->entity = $model;
    }

    /** Return the entity this repository works with.
     * @return Model
     */
    public function getEntity()
    {
        return $this->entity;
    }


    /** If this method receives true, so the result of your query will ignore the criterias applied to it.
     * @param bool $skip
     */
    public function skipCriteria($skip = true)
    {
        $this->skipCriteria = $skip;
    }

    /** Return the collection of criterias
     * @return Collection
     */
    public function getCriterias()
    {
        return $this->criterias;
    }

    /** Return a query formed by the criteria received. Usage: $postRepository->findByCriteria(new OlderPostsCriteria())->paginate(10);
     * @param CriteriaAbstract $criteria
     * @return $this
     */
    public function findByCriteria(CriteriaAbstract $criteria)
    {
        $this->entity = $criteria->apply($this->entity, $this);
        return $this;
    }

    /** Insert a criteria on the stack. You can use this method as a chain.
     * Usage: $postRepository->pushCriteria(new OlderPostsCriteria())->pushCriteria(new UnreadPostsCriteria())->paginate(10);
     * @param CriteriaAbstract $criteria
     * @return RepositoryAbstract
     */

    public function pushCriteria(CriteriaAbstract $criteria, $clausule = 'AND')
    {
        $this->criterias->push($criteria);
        return $this;
    }

    /** Applies the criterias in the stack before return the result.
     * @return $this
     */
    public function applyCriteria()
    {
        if($this->skipCriteria) return $this;
        foreach($this->getCriterias() as $criteria)
        {
            $this->model = $criteria->apply($this->model, $this);
        }
        return $this;
    }


    /** Return all objects from database, using the criterias on the stack.
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->entity->all($columns);
    }

    /** Paginates all objects from database, using the criterias on the stack.
     * @param int $perpage
     * @param array $columns
     */
    public function paginate($perpage = 15, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->entity->paginate($perpage, $columns);
    }

    /** Insert a new entity on database
     * @param array $data
     * @return Model created
     */
    public function create(array $data)
    {
        return $this->entity->create($data);
    }

    /** Update entity on database.
     * @param array $data
     * @param $id
     */
    public function update(array $data, $id)
    {
        $this->entity->find($id)->update($data);
    }

    /** Delete the entity on database
     * @param $id
     */
    public function delete($id)
    {
        $this->entity->destroy($id);
    }

    /** Find an entity using its primary key
     * @param $id
     * @param array $columns
     * @return Model
     */
    public function find($id, $columns = ['*'])
    {
        return $this->entity->find($id, $columns);
    }

    /** Find a collection of entities using one or more fields and its values.
     *  Usage: $postRepository->findBy(['author_id' => 5, 'post_status' => 1])->paginate(10);
     * @param $fields associative array representing the entities fields and its values
     * @param array $columns
     * @return $this
     */
    public function findBy($fields, $columns = ['*'])
    {
        foreach($fields as $field => $value)
        {
            $this->entity = $this->entity->where($field, '=', $value);
        }
        return $this;
    }


    /** Magic method: Use this method to call entity method directly. Instead of $repository->getEntity()->find(5), you can use: $repository->find(5).
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if(method_exists($this, $method)) return call_user_func_array([$this, $method], $arguments);
        return call_user_func_array([$this->entity, $method], $arguments);
    }


}