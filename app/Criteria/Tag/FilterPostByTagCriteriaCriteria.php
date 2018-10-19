<?php

namespace App\Criteria\Tag;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterPostByTagCriteriaCriteria.
 *
 * @package namespace App\Criteria\Tag;
 */
class FilterPostByTagCriteriaCriteria implements CriteriaInterface
{

    protected $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        // lọc bài viết theo tag
        return $model
            ->selectRaw('posts.*, tags.tag')
            ->join('post_tag', 'posts.id', '=', 'post_tag.post_id')
            ->join('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->where('post_tag.tag_id', $this->tag->id);
    }
}
