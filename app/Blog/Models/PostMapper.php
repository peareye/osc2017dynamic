<?php
/**
 * Post Mapper
 */
namespace Blog\Models;

class PostMapper extends DataMapperAbstract
{
    protected $table = 'post';
    protected $tableAlias = 'p';
    protected $modifyColumns = array('title', 'url', 'url_locked', 'page', 'meta_description', 'content', 'content_html', 'content_excerpt', 'published_date', 'template');
    protected $domainObjectClass = 'Post';
    protected $defaultSelect = 'select SQL_CALC_FOUND_ROWS p.* from post p';

    /**
     * Get Blog Posts with Offset
     *
     * Define limit and offset to limit result set.
     * Returns an array of Domain Objects (one for each record)
     * Note: Excludes rows marked as Pages by default
     * @param int $limit Limit
     * @param int $offset Offset
     * @param bool $publishedPostsOnly Only get published posts - defaults to true
     * @param bool $postsOnly Only get posts, not pages
     * @return array
     */
    public function getPosts($limit = null, $offset = null, $publishedPostsOnly = true, $postsOnly = true)
    {
        $this->sql = $this->defaultSelect . ' where 1=1 ';

        if ($publishedPostsOnly) {
            $this->sql .= ' and p.published_date <= curdate()';
        }

        if ($postsOnly) {
            $this->sql .= ' and page = \'N\'';
        }

        // Add order by
        if ($publishedPostsOnly) {
            $this->sql .= ' order by p.published_date desc, title asc';
        } else {
            $this->sql .= ' order by p.published_date is null desc, p.published_date desc, title asc';
        }

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Get Single Post
     *
     * Returns post by url segment, or by post ID
     * @param string|int $id Post URL segment or ID
     * @param bool $publishedPostsOnly Only get published posts - defaults to true
     * @return object
     */
    public function getSinglePost($id, $publishedPostsOnly = true)
    {
        $this->sql = $this->defaultSelect . ' where 1=1';

        // Was a numeric ID supplied?
        if (is_numeric($id)) {
            $where = ' and p.id = ?';
            $this->bindValues[] = (int) $id;
        } else {
            $where = ' and p.url = ?';
            $this->bindValues[] = $id;
        }

        $this->sql .= $where;

        if ($publishedPostsOnly) {
            $this->sql .= ' and p.published_date <= curdate()';
        }

        return $this->findRow();
    }

    /**
     * Verify URL
     *
     * Check if post URL is unique
     * @param string $url Cleaned title
     * @return boolean
     */
    public function postUrlIsUnique($url)
    {
        $this->sql = "select 1 from {$this->table} where url = ?";
        $this->bindValues[] = $url;

        // Execute the query
        $this->execute();
        $data = $this->statement->fetchAll();
        $this->clear();

        // Did we find anything?
        if (!empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * Get Pages
     *
     * Get all post records marked as a page
     * @return array
     */
    public function getPages()
    {
        $this->sql = $this->defaultSelect . ' where p.page = \'Y\' and p.published_date <= curdate()';

        return $this->find();
    }

    /**
     * Get Prior and Next Posts
     *
     * For use in navigation buttons, returns the post published before and after the current one
     * @param mixed $currentPost URL (string) or post ID (int)
     * @return array
     */
    public function getPriorAndNextPosts($currentPost)
    {
        // Determine what kind of post identifier was supplied
        if (is_string($currentPost)) {
            // We have a post URL slug, so bind a string
            $whereClause = ' url = ?';
        } else if (is_numeric($currentPost)) {
            // We have a post ID, so bind an integer
            $whereClause = ' id = ?';
        }

        // SQL to get the prior and next posts
        $this->sql = "
select
(select url
from post
where published_date is not null
and page = 'N'
and published_date < (select published_date from post where page = 'N' and {$whereClause})
order by published_date desc, title asc limit 1) priorPost,
(select url
from post
where published_date is not null
and page = 'N'
and published_date > (select published_date from post where page = 'N' and  {$whereClause})
order by published_date asc, title asc limit 1) nextPost";

        // Assign the bind variables
        $this->bindValues[] = $currentPost;
        $this->bindValues[] = $currentPost;

        return $this->findRow();
    }

    /**
     * Search Posts
     *
     * Uses MySQL fulltext index
     * @param string $terms Search terms
     * @param int $limit Limit
     * @param int $offset Offset
     * @param bool $publishedPostsOnly Only get published posts - defaults to true
     * @param bool $postsOnly Only get posts, not pages
     * @return mixed
     */
    public function search($terms, $limit = null, $offset = null, $publishedPostsOnly = true, $postsOnly = true)
    {
        // Build search statement
        $this->sql = $this->defaultSelect . ' where match(`title`, `content`) against (?)';
        $this->bindValues[] = $terms;

        if ($publishedPostsOnly) {
            $this->sql .= ' and p.published_date <= curdate()';
        }

        if ($postsOnly) {
            $this->sql .= ' and page = \'N\'';
        }

        // Add order by
        if ($publishedPostsOnly) {
            $this->sql .= ' order by p.published_date desc, title asc';
        } else {
            $this->sql .= ' order by p.published_date is null desc, p.published_date desc, title asc';
        }

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }
}
