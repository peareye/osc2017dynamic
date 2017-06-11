<?php
/**
 * Review Mapper
 */
namespace Blog\Models;

class ReviewMapper extends DataMapperAbstract
{
    protected $table = 'review';
    protected $tableAlias = 'r';
    protected $modifyColumns = array('title', 'content', 'content_html', 'who', 'review_date', 'approved', 'rating');
    protected $domainObjectClass = 'Review';
    protected $defaultSelect = 'select * from review r';

    /**
     * Get Approved Reviews
     *
     * @param int|null $limit Number of reviews to return, returns all approved reviews if null
     * @return array
     */
    public function getReviews($limit = null)
    {
        $this->sql = $this->defaultSelect . ' where approved = ? order by created_date desc';
        $this->bindValues[] = 'Y';

        // Is there a limit?
        if ($limit !== null) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        return $this->find();
    }

    /**
     * Get All Reviews
     *
     * @param int|null $limit Number of reviews to return, returns all reviews if null
     * @return array
     */
    public function getAllReviews($limit = null)
    {
        $this->sql = $this->defaultSelect . ' order by created_date desc';

        // Is there a limit?
        if ($limit !== null) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

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
