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
    protected $resourceKeyCollection = 'tags';
    protected $resourceKeyItem       = 'tags';
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
