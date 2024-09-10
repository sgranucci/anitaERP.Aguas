<?php

namespace App\Repositories\Caja;

use App\Models\Caja\Conceptogasto;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConceptogastoRepository implements ConceptogastoRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Conceptogasto $conceptogasto)
    {
        $this->model = $conceptogasto;
    }

    public function all()
    {
        return $this->model->get();
    }

    public function create(array $data)
    {
        $conceptogasto = $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $conceptogasto = $this->model->findOrFail($id)->update($data);

		return $conceptogasto;
    }

    public function delete($id)
    {
    	$conceptogasto = $this->model->find($id);

        $conceptogasto = $this->model->destroy($id);

		return $conceptogasto;
    }

    public function find($id)
    {
        if (null == $conceptogasto = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $conceptogasto;
    }

    public function findPorId($id)
    {
		$retencionganancia = $this->model->where('id', $id)->first();

		return $retencionganancia;
    }

    public function findOrFail($id)
    {
        if (null == $conceptogasto = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $conceptogasto;
    }
}
