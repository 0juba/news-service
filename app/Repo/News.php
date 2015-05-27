<?php namespace NewsService\Repo;


use NewsService\Core\DI;

/**
 * Class News
 * @package NewsService\Repo
 */
class News
{

    /**
     * @var string
     */
    private $dateFormat = 'd-m-Y';

    /**
     * @var array
     */
    private $fields = ['title'=>\PDO::PARAM_STR,'alias'=>\PDO::PARAM_STR,'content'=>\PDO::PARAM_STR,'newsDate'=>\PDO::PARAM_INT];


    /**
     * @param array $item
     * @return array
     */
    private function afterFetch(array $item)
    {

        $item['newsDate']  = $this->timestampToDate($item['newsDate']);
        $item['createdAt'] = $this->timestampToDate($item['createdAt']);
        $item['updatedAt'] = $this->timestampToDate($item['updatedAt']);

        return $item;

    }


    /**
     * @return string
     */
    public function getDbal()
    {
        return $this->dbal;
    }

    /**
     * @param string $dbal
     */
    public function setDbal($dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @var string
     */
    private $dbal = '';

    /**
     *
     */
    function __construct()
    {
        $this->dbal = DI::getDi()->doctrine;
    }

    /**
     * @return mixed
     */
    public function createTable()
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS `news` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(200) NOT NULL,
              `alias` varchar(200) NOT NULL,
              `content` text NOT NULL,
              `newsDate` int(10) unsigned NOT NULL,
              `createdAt` int(10) unsigned NOT NULL,
              `updatedAt` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `alias` (`alias`),
              KEY `title` (`title`),
              KEY `newsDate` (`newsDate`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ';

        $stmt = $this->dbal->prepare($sql);
        return $stmt->execute();
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param null $start
     * @param null $end
     * @return array|null
     */
    public function listNews($limit=10,$offset=0,$sort='-newsDate',$start=null,$end=null)
    {

        $andWhere = [];

        $params = [];
        $types = [];

        if ( $start ){
            $start = $this->dateToTimestamp($start);
            $andWhere[] = 'newsDate > ?';
            $params[] = $start;
            $types[] = \PDO::PARAM_INT;
         }

        if ( $end ) {
            $end = $this->dateToTimestamp($end);
            $andWhere[] = 'newsDate < ?';
            $params[] = $end;
            $types[] = \PDO::PARAM_INT;
        }

        $where = count($andWhere) ? 'WHERE ' . implode(' AND ',$andWhere) : '';

        $order = substr($sort,0,1) == '-' ? 'DESC' : 'ASC';

        $sql = sprintf('SELECT * FROM news %s ORDER BY newsDate %s LIMIT %s OFFSET %s',$where,$order,$limit,$offset);

        $stmt = $this->dbal->executeQuery($sql,$params,$types);

        $records = $stmt->fetchAll();

        if ( count($records) ) {

            $o = $this;

            return array_map(function ($item) use($o) {
                return $o->afterFetch($item);
            }, $records);
        }

        return null;

    }


    /**
     * @param $date
     * @return int
     */
    public function dateToTimestamp($date)
    {
        $d = \DateTime::createFromFormat($this->getDateFormat(),$date);
        return $d->getTimestamp();
    }

    /**
     * @param $ts
     * @return bool|string
     */
    public function timestampToDate($ts)
    {
        return date($this->getDateFormat(),$ts);
    }


    /**
     * @param $data
     * @return null
     * @throws \Exception
     */
    public function insert($data)
    {

        if ( isset($data['newsDate'] ) )
            $data['newsDate'] = $this->dateToTimestamp($data['newsDate']);

        $params = [];
        $types  = [];

        if ( count ( $diff = array_diff_key($this->fields,$data) ) )
            throw new \Exception('Missed required fields: '.implode(', ',array_keys($diff) ) );

        if ( count ( $diff = array_diff_key($data,$this->fields) ) )
            throw new \Exception('Request has wrong fields: '.implode(', ',array_keys($diff) ) );

        foreach($this->fields as $f=>$type) {
            $params[] = $data[$f];
            $types[]  = $type;
        }

        $t   = time();
        $sql = sprintf('INSERT INTO news(%s,createdAt,updatedAt) VALUES (?,?,?,?,%s,%s);',implode(',',array_keys($this->fields)),$t,$t);

        $stmt = $this->dbal->executeUpdate($sql,$params,$types);

        if ( !$stmt )
            return null;

        $id = $this->dbal->lastInsertId();

        return $this->get($id);

    }


    /**
     * @param $id
     * @param $data
     * @return null
     * @throws \Exception
     */
    public function update($id,$data)
    {
        if ( isset($data['newsDate'] ) )
            $data['newsDate'] = $this->dateToTimestamp($data['newsDate']);

        $params = [];
        $types  = [];
        $fields = [];

        if ( count ( $diff = array_diff_key($data,$this->fields) ) )
            throw new \Exception('Request has wrong fields: '.implode(', ',array_keys($diff) ) );

        foreach($data as $f => $v){
            $params[]   = $v;
            $types[]    = $this->fields[$f];
            $fields[]   = $f . '=?';
        }

        $params[] = time(); $params[] = $id;
        $types[]  = \PDO::PARAM_INT; $types[] = \PDO::PARAM_INT;

        $sql = sprintf( 'UPDATE news SET %s, updatedAt=? WHERE id = ?', implode(',',$fields) );

        $stmt = $this->dbal->executeUpdate($sql,$params,$types);

        if ( !$stmt )
            return null;

        return $this->get($id);
    }


    /**
     * @param $id
     * @param $data
     * @return null
     */
    public function push($id,$data)
    {

        if ( $id ) {

            return $this->update($id,$data);

        } else {

            return $this->insert($data);
        }

    }


    /**
     * @param $id
     * @return null
     */
    public function get($id)
    {
        $sql = 'SELECT * FROM `news` WHERE id = ?';

        $stmt = $this->dbal->executeQuery($sql,[$id],[\PDO::PARAM_INT]);

        foreach($stmt->fetchAll() as $row)
            return $this->afterFetch($row);

        return null;
    }
}