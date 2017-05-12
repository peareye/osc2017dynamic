<?php
/**
 * Comment Mapper
 */
namespace Blog\Models;

class CommentMapper extends DataMapperAbstract
{
    protected $table = 'comment';
    protected $tableAlias = 'c';
    protected $modifyColumns = array('reply_id', 'post_id', 'name', 'email', 'comment', 'approved');
    protected $domainObjectClass = 'Comment';
    protected $defaultSelect = 'select c.*, p.title, p.url from comment c join post p on c.post_id = p.id where 1=1';

    /**
     * Get Comments by Post ID
     *
     * @param int $postId Post record ID
     * @return array
     */
    public function getPostComments($postId)
    {
        $this->sql = $this->defaultSelect . ' and post_id = ? and approved = \'Y\' order by c.post_id, c.created_date';
        $this->bindValues[] = $postId;

        return $this->find();
    }

    /**
     * Get All Comments
     *
     * For moderation, joins to post table to get post title
     * @param int $postId Post record ID
     * @return array
     */
    public function getAllComments()
    {
        $this->sql = $this->defaultSelect . ' order by c.post_id, c.created_date desc';

        return $this->find();
    }

    /**
     * Get New Comment Count
     *
     * @return int
     */
    public function getNewCommentCount()
    {
        $this->sql = 'select count(*) comments from comment where approved = \'N\'';

        $this->execute();
        $result = $this->statement->fetch();
        $this->clear();

        return ($result->comments !== 0) ? $result->comments : null;
    }

    /**
     * Get Comment Count by Post ID
     *
     * @param int $postId Post record ID
     * @return int
     */
    public function getCommentCountByPostId($postId)
    {
        $this->sql = 'select count(*) comments from comment where approved = \'Y\' and post_id = ?';
        $this->bindValues[] = $postId;

        $this->execute();
        $result = $this->statement->fetch();
        $this->clear();

        return ($result->comments !== 0) ? $result->comments : null;
    }
}
