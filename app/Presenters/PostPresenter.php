<?php

namespace App\Presenters;

use App\Transformers\PostTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PostPresenter.
 *
 * @package namespace App\Presenters;
 */
class PostPresenter extends FractalPresenter
{
    protected $resourceKeyCollection = 'posts';
    protected $resourceKeyItem       = 'posts';
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PostTransformer();
    }
}
