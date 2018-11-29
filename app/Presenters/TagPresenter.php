<?php

namespace App\Presenters;

use App\Transformers\TagTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TagPresenter.
 *
 * @package namespace App\Presenters;
 */
class TagPresenter extends FractalPresenter
{
    protected $resourceKeyCollection = 'tag';
    protected $resourceKeyItem       = 'tag';
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TagTransformer();
    }
}
