<?php
/**
 * Message Mapper
 */
namespace App\Models;

class MessageMapper extends DataMapperAbstract
{
    protected $table = 'message';
    protected $tableAlias = 'r';
    protected $modifyColumns = array('email', 'name', 'text');
    protected $domainObjectClass = 'Message';
    protected $defaultSelect = 'select * from message m order by created_date desc';
}
