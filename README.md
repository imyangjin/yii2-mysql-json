# Yii2 Mysql Json
The extension yii2-ActiceRecord And yii2-ActiveQuery with use mysql json make simple.

## Installation
The preferred way to install this extension is through composer.

Either run

> composer require --prefer-dist imyangjin/yii2-mysql-json


or add

> "imyangjin/yii2-mysql-json": "~1.0"


to the require section of your `composer.json`.

## Basic Usage

Your Model file must extend this extension class；
```
use Imyangji\Yii2MysqlJson\ActiveRecordJson；

class YourModel extent ActiveRecordJson
{

}

```

Then if you want to use it to search column with json.

### jsonWhere

This func like use `Model::find()->where(\[Query::where()\])`;
column search is `column->"$.jsonColumn1.jsonColumn2..."`;
```
public function search()
{
    YourModel::findJson()
        ->jsonWhere(['content->"$.en.content"' => 'who'])
        ->jsonWhere(['>', 'content->"$.en.content"' , 'who'])
}
```

### jsonContainsWhere

This func Support mysql `JSON_CONTAINS(target, candidate[, path])`;
This query is equivalent to the query, but the difference is that the query is an inclusion relation, that is, the field contains the value of the value;
Column A multilevel field supporting JSON fields is segmented using '.';

```
public function search()
{
    YourModel::findJson()
        ->jsonContainsWhere('content.en.content', 'who')
}
```

### jsonExtractWhere

This func Support JSON_EXTRACT(json_doc, path[, path] ...) in query;
Column A multilevel field supporting JSON fields is segmented using '.';
Can use operate to search。

```
public function search()
{
    YourModel::findJson()
        ->jsonContainsWhere('content.en.content', 'who', '>')
}
```




