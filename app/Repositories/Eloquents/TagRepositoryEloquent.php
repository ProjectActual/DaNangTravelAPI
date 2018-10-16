<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\TagRepository;
use App\Entities\Tag;
use App\Validators\TagValidator;

/**
 * Class TagRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class TagRepositoryEloquent extends BaseRepository implements TagRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $fieldSearchable = [
        'tag'     => 'like',
        'uri_tag' => 'like',
    ];

    public function model()
    {
        return Tag::class;
    }

    public function presenter()
    {
        return 'App\\Presenters\\TagPresenter';
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByTag($tag)
    {
        return $this->model
            ->where('tag', $tag)
            ->first();
    }

    public function findId($id)
    {
        return $this->model->find($id);
    }

/**
 * findWithUri tìm theo uri của tag
 * @param  string $uri_tag
 * @return [type]          [description]
 */
    public function findWithUri($uri_tag)
    {
        $tag = $this->model->where('uri_tag', $uri_tag)->first();

        return $tag;
    }
}
