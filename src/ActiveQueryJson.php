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
     * 支持 JSON_CONTAINS(target, candidate[, path])查询
     * 此查询方式和等于查询等效,但不同在于，此查询是包含关系，即字段中包含value的值
     *
     * @param string     $column 支持json字段的多级字段，用'.'分割；
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
}
