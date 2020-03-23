<?php

namespace App\Http\Controllers;

use App\Repositories\Repository;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\FieldManagers\FieldManager;

class ApiController extends BaseController
{
    use ApiResponse;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Model
     */
    protected $query;



    /**
     * @var int
     */
    protected $limit = 15;

    /**
     * @var boolean
     */
    protected $list = false;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = $this->query ?: $this->repository->getModel();

        if ($includes = $request->get('includes')) {
            $includes = is_array($includes) ? $includes : explode(',', $includes);
            $query = $query->with($includes);
        }

        if ($this->list || $request->get('search_type') == 'list') {
            $resources = $query->get();
        } else {
            $resources = $query->paginate($request->get('per_page', $this->limit));
        }

        return $this->success($resources);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $resource = $this->repository->create($request->all());
        return response()->json($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int | string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $resource = $this->repository->find($id);
        if (!$resource) {
            return $this->notFound();
        }

        if ($includes = $request->get('includes')) {
            $includes = is_array($includes) ? $includes : explode(',', $includes);
            $resource->load($includes);
        }

        return $this->success($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resource = $this->repository->update($request->all(), $id);

        if ($includes = $request->get('includes')) {
            $includes = is_array($includes) ? $includes : explode(',', $includes);
            $resource->load($includes);
        }

        return $this->success($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $this->repository->delete($id);
        return $this->success();
    }
}
