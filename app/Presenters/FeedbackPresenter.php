<?php

namespace App\Presenters;

use App\Transformers\FeedbackTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FeedbackPresenter.
 *
 * @package namespace App\Presenters;
 */
class FeedbackPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FeedbackTransformer();
    }
}
