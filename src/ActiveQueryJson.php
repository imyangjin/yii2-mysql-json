<?php
/**
 * ActiveQueryJson
 *
 * @uses     yangjin
 * @version  2018/10/19
 * @author   yangjin <imyangjin@vip.qq.com>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */

namespace Imyangjin\Yii2MysqlJson;

use yii\db\ActiveQuery;
use yii\db\Query;

class ActiveQueryJson extends ActiveQuery
{
    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'AND' operator.
     * @param array $condition  the new WHERE condition. Please refer to [[where()]]
     *                          on how to specify this parameter.
     *                          example like :
     *                          1. ['>', 'content->"$.en.content"' , 1]
     *                          2. ['content->"$.en.content"' => 1]
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    public function jsonWhere($condition, $params = [])
    {
        $condition = \Yii::$app->db->queryBuilder->buildCondition($condition, $params);
        $condition = str_replace('`', '', $condition);

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        $this->addParams($params);
        return $this;
    }

    /**
     * Add Select.
     * This function must after select() if has.And This function only support json column；
     *
     * @param array $params
     *              ["expand.gid" => 'id', "expand.orderno"]
     *              eq. JSON_EXTRACT(expand, '$.gid') AS id, JSON_EXTRACT(expand, '$.orderno') AS orderno
     * @return $this
     */
    public function jsonSelect(array $params)
    {
        $columns = [];
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                list($cont, $column) = $this->splitJsonColumn($value);
                $valArr = explode('.', $value);
                $as     = end($valArr);
            } else {
                list($cont, $column) = $this->splitJsonColumn($key);
                $as = $value;
            }

            $columns[] = "JSON_EXTRACT($cont, '$column') AS $as";
        }

        $this->addSelect($columns);
        return $this;
    }

    /**
     * Support JSON_CONTAINS(target, candidate[, path]) in query
     * This query is equivalent to the query, but the difference is that the query is an inclusion relation, that is, the field contains the value of the value
     *
     * @param string     $column A multilevel field supporting JSON fields is segmented using '.'
     *                           example content.lang.en
     * @param string|int $value
     *
     * @return $this
     * @see https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html
     */
    public function jsonContainsWhere($column, $value)
    {
        $columns = explode('.', $column);
        $cond    = array_shift($columns);

        if (empty($columns)) {
            $condition = "JSON_CONTAINS($cond, '$value')";
        } else {
            array_unshift($columns, '$');
            $jsonCond  = implode('.', $columns);
            $condition = "JSON_CONTAINS($cond, '$value', '$jsonCond')";
        }

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        return $this;
    }

    /**
     * Support JSON_EXTRACT(json_doc, path[, path] ...) in query
     *
     * @param string     $column  A multilevel field supporting JSON fields is segmented using '.'
     *                            example content.lang.en
     * @param string|int $value
     * @param string     $operate Query Operators，default '='
     *                            example support : >|>=|<|<=
     * @return $this
     * @see https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html#function_json-extract
     */
    public function jsonExtractWhere($column, $value, $operate = '=')
    {
        list($cond, $jsonCond) = $this->splitJsonColumn($column, true);

        $condition = "JSON_EXTRACT($cond, '$jsonCond') $operate $value";

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        return $this;
    }

    public function splitJsonColumn($column, $jsonColumn = false)
    {
        $columns = explode('.', $column);
        $cond    = array_shift($columns);

        if (count($columns) == 0) {
            if ($jsonColumn == true) {
                throw new InvalidArgumentException(get_class($this) . ' has no json column "' . $column . '".');
            }
            $jsonColumn == '';
        } else {
            array_unshift($columns, '$');
            $jsonCond = implode('.', $columns);
        }

        return [$cond, $jsonCond];
    }
}
