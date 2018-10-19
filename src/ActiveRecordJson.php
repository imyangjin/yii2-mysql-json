<?php
/**
 * ActiveRecordJson
 *
 * @uses     yangjin
 * @version  2018/10/19
 * @author   yangjin <imyangjin@vip.qq.com>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
namespace Imyangjin\Yii2MysqlJson;

use yii\db\ActiveRecord;

class ActiveRecordJson extends ActiveRecord
{
    /**
     * 支持json字段查询的扩展方法
     *
     * @example
     *      1.新增 jsonWhere 的子方法 ：
     *          $this->jsonWhere([Query::where()])
     *
     * @return ActiveQueryJson|object
     * @throws \yii\base\InvalidConfigException
     */
    public function findJson()
    {
        return \Yii::createObject(ActiveQueryJson::class, [get_called_class()]);
    }
}
