<?php

namespace App\Presenters;

use App\Transformers\CategoryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CategoryPresenter.
 *
 * @package namespace App\Presenters;
 */
class CategoryPresenter extends FractalPresenter
{
    protected $resourceKeyCollection = 'category';
    protected $resourceKeyItem       = 'category';
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CategoryTransformer();
    }
}
