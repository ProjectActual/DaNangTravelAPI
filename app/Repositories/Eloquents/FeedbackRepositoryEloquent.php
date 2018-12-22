<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\FeedbackRepository;
use App\Entities\Feedback;
use App\Validators\FeedbackValidator;
use App\Entities\Role;

/**
 * Class FeedbackRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class FeedbackRepositoryEloquent extends BaseRepository implements FeedbackRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Feedback::class;
    }

    public function presenter()
    {
        return "App\\Presenters\\FeedbackPresenter";
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

/**
 * sắp xếp giảm dần với updated
 * @return mixed
 */
    public function latest()
    {
        return $this
            ->orderBy('created_at', 'desc');
    }
}
