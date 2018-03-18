<?php
/**
 * Review Mapper
 */
namespace App\Models;

class ReviewMapper extends DataMapperAbstract
{
    protected $table = 'review';
    protected $tableAlias = 'r';
    protected $modifyColumns = array('title', 'content', 'content_html', 'who', 'review_date', 'approved', 'rating', 'email', 'token');
    protected $domainObjectClass = 'Review';
    protected $defaultSelect = 'select SQL_CALC_FOUND_ROWS r.* from review r';

    /**
     * Get Approved Reviews
     *
     * @param int|null $limit Number of reviews to return, returns all approved reviews if null
     * @param int|null $offset Offset
     * @return array
     */
    public function getApprovedReviews($limit = null, $offset = null)
    {
        $this->sql = $this->defaultSelect . ' where approved = ? order by created_date desc';
        $this->bindValues[] = 'Y';

        // Add query limit
        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        // Add query offset
        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
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
