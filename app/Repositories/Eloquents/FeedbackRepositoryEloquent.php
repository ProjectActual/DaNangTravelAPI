<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\FeedbackRepository;
use App\Entities\Feedback;
use App\Validators\FeedbackValidator;

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

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
