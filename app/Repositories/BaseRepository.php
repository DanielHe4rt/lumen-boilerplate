<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * Model reference
     */
    protected $model;

    /**
     * Builder of Model
     */
    protected $builder;

    /**
     * Return a resource by id
     *
     * @param $id int
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getFieldManager()
    {
        return $this->model->getFieldManager();
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Return a resource by id
     *
     * @param $id int
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Return collection of resources
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * Return a new resource
     *
     * @param $data array
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Retrieve the resource by the attributes, or create it if it doesn't exist
     *
     * @param $data array
     * @return Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    /**
     * Update a resource by id
     *
     * @param $data array
     * @param $id int
     *
     * @return Model
     */
    public function update(array $data, $id)
    {
        $resource = $this->model->find($id);

        if (! $resource) {
            return '';
        }

        $resource->update($data);

        return $resource;
    }

    /**
     * Delete a resource by id
     *
     * @param $id int
     * @return boolean
     */
    public function delete($id)
    {
        $resource = $this->model->find($id);

        if (! $resource) {
            return '';
        }

        return $resource->delete();
    }

    /**
     * Return collection of resources
     *
     * @param $criteria array
     * @param $orderBy array
     * @param $limit int
     * @param $offset int
     * @param $include array
     * @param $fields string
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $include = null, $fields = null)
    {
        $this->builder = $this->model;

        if (count($criteria) == count($criteria, COUNT_RECURSIVE)) {
            if (count($criteria) > 0) {
                $this->builder = $this->builder->where($criteria[0], $criteria[1], $criteria[2]);
            }
        } else {
            foreach ($criteria as $c) {

                if($c[1] == 'between') {
                    $this->builder = $this->builder->whereBetween($c[0], explode(',', $c[2]));
                    continue;
                }
                $this->builder = $this->builder->where($c[0], $c[1], $c[2]);
            }
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $order) {
                $this->builder = $this->builder->orderBy($order[0], $order[1]);
            }
        }

        if ($limit !== null) {
            $this->builder = $this->builder->take((int) $limit);
        }

        if ($offset !== null) {
            $this->builder = $this->builder->skip((int) $offset);
        }

        if ($include !== null) {
            $this->builder = $this->builder->with($include);
        }

        if ($fields !== null) {
            $this->builder = $this->builder->select($fields);
        }

        return $this->builder->get();
    }

    /**
     * Return a resource by criteria
     *
     * @param $criteria array
     * @return Illuminate\Database\Eloquent\Model
     */
    public function findOneBy(array $criteria)
    {
        return $this->findBy($criteria)->first();
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($limit = null)
    {
        if ($limit) {
            $this->builder->take($limit);
        }
        return $this->builder->get();
    }

    /**
     * Return one.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function first()
    {
        return $this->builder->first();
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        /* Strip param page from URL */
        $url = preg_replace('/&?page=[^&]*/', '', Request::fullUrl());

        $paginate = $this->builder->paginate($perPage, $columns, $pageName, $page);
        $paginate->setPath($url);
        return $paginate;
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page')
    {
        /* Strip param page from URL */
        $url = preg_replace('/&?page=[^&]*/', '', Request::fullUrl());

        $paginate = $this->builder->simplePaginate($perPage, $columns, $pageName);
        $paginate->setPath($url);
        return $paginate;
    }

    /**
     * Get only fields fillable from Pivot Relation
     *
     * @return array
     */
    public function onlyFillablePivot($pivotRelation, $data)
    {
        $fillable = $this->getPivotFields($pivotRelation, 'pivotColumns');

        return array_only($data, $fillable);
    }

    /**
     * Get array fields fillable from Pivot Relation
     *
     * @return array
     */
    public function getPivotFields($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        $value = $property->getValue($obj);
        $property->setAccessible(false);

        /* Remove timestamp from pivot */
        return array_diff($value, [
            'deleted_at',
            'created_at',
            'updated_at'
        ]);
    }

    /**
     * Create child One-to-One OR Many-to-Many without Pivot data
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function storeChild($id, $relation, array $data)
    {
        $parent = $this->model->find($id);

        if (! $parent) {
            return null;
        }

        $resource = $parent->$relation()->create($data);

        return $resource;
    }

    /**
     * Attach relation
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function storeChildAndPivot($idParent, $relation, $data = [])
    {
        $parent = $this->find($idParent);
        $childEntity = $parent->$relation()->getRelated();

        $child = $childEntity->create($data);

        $data = $this->onlyFillablePivot($parent->$relation(), $data);

        $parent->$relation()->attach($child->id, $data);

        return $child;
    }

    /**
     * Attach relation
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function attach($idParent, $idChild, $relation, $data = [])
    {
        $parent = $this->find($idParent);

        $data = $this->onlyFillablePivot($parent->$relation(), $data);

        $parent->$relation()->attach($idChild, $data);

        return $parent->$relation()->find($idChild);
    }

    /**
     * Detach relation
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function detach($idParent, $idChild, $relation)
    {
        $parent = $this->find($idParent);

        $parent->$relation()->detach($idChild);

        return true;
    }

    /**
     * Return all childs from relation
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function getChilds($id, $relation, $filters = null)
    {
        $parent = $this->model->find($id);

        if (! $parent) {
            return null;
        }

        if (count($filters->request->all()) > 0) {
            $child = $parent->$relation()->getRelated();

            $search = new Search($child, $filters, $parent->$relation());
            $this->builder = $search->getBuilder();

            /* Retorna os dados apenas da table/resource atual */
            $this->builder->select("{$child->getTable()}.*");

            return $this->builder->get();
        }

        $resource = $parent->$relation;

        return $resource;
    }

    /**
     * Return one child by id
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function getChild($id, $relation, $idChild, $filters = null)
    {
        $parent = $this->model->find($id);

        if (! $parent) {
            return null;
        }

        if (count($filters->request->all()) > 0) {
            $child = $parent->$relation()->getRelated();

            $search = new Search($child, $filters, $parent->$relation());
            $this->builder = $search->getBuilder();

            /* Retorna os dados apenas da table/resource atual */
            $this->builder->select("{$child->getTable()}.*");

            /* N:N precisa add o id da outra tabela */
            if ($parent->$relation() instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $this->builder->where($parent->$relation()
                    ->getOtherKey(), $idChild);
            }

            $resource = $this->builder->get();
        } else {
            $resource = $parent->$relation()->find($idChild);
        }

        return $resource;
    }

    /**
     * Update Child
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function updateChild($id, $relation, $idChild, array $data)
    {
        $parent = $this->model->find($id);

        if (! $parent) {
            return null;
        }

        $resource = $parent->$relation()->find($idChild);

        if (! $resource) {
            return null;
        }
        $resource->update($data);

        return $resource;
    }

    /**
     * Delete Child
     *
     * @return boolean;
     */
    public function deleteChild($id, $relation, $idChild)
    {
        $parent = $this->model->find($id);

        if (! $parent) {
            return null;
        }

        $resource = $parent->$relation()->find($idChild);

        if (! $resource) {
            return null;
        }

        return $resource->delete();
    }

    /**
     * Autocomplete
     *
     * @return array;
     */
    public function autocomplete($text)
    {
        if (! $this->builder) {
            $this->builder = $this->model;
        }

        $fields = $this->model->getAutocomplete();

        foreach ($fields as $field) {

            $this->builder = $this->builder->where(function ($query) use ($field, $text) {
                $query->orWhere($field, 'like', "%$text%");
            });
        }

        return $this;
    }

    /**
     * Average field
     *
     * @param string $field
     * @return float
     */
    public function avg($field)
    {
        if (! $this->builder) {
            $this->builder = $this->model;
        }

        return $this->builder->avg($field);
    }
}
