<?php
/**
 * Review Mapper
 */
namespace Blog\Models;

class ReviewMapper extends DataMapperAbstract
{
    protected $table = 'review';
    protected $tableAlias = 'r';
    protected $modifyColumns = array('title', 'content', 'content_html', 'who', 'review_date');
    protected $domainObjectClass = 'Review';
    protected $defaultSelect = 'select * from review r';

    /**
     * Get Reviews
     *
     * @return array
     */
    public function getReviews()
    {
        $this->sql = $this->defaultSelect . ' order by created_date desc';

        return $this->find();
    }

    /**
     * Get Single Review
     *
     * @param int $id Review ID
     * @return object
     */
    public function getSingleReview($id)
    {
        $this->sql = $this->defaultSelect . ' where id = ?';

        return $this->findRow();
    }
}
