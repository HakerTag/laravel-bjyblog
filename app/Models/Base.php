<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use DB;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Base extends Model
{
    // 软删除
    use SoftDeletes, Cachable;

    /**
     * 禁止被批量赋值的字段
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 批量更新的方法
     * 示例参数
     * $multipleData = [
     *    [
     *        'name' => 'name 1' ,
     *        'date' => 'date 1'
     *     ],
     *     [
     *        'name' => 'name 2' ,
     *        'date' => 'date 2'
     *      ]
     *   ]
     *
     * @param array $multipleData
     * @param bool  $flash        是否需要成功或者失败的提示
     *
     * @return bool|int
     */
    public function updateBatch($multipleData = [], $flash = true)
    {
        if (empty($multipleData)) {
            return false;
        }

        // 获取表名
        $tableName       = config('database.connections.mysql.prefix') . $this->getTable();
        $updateColumn    = array_keys($multipleData[0]);
        $referenceColumn = $updateColumn[0];

        unset($updateColumn[0]);

        $whereIn = '';
        // 组合sql语句
        $sql = 'UPDATE ' . $tableName . ' SET ';

        foreach ($updateColumn as $uColumn) {
            $sql .= $uColumn . ' = CASE ';

            foreach ($multipleData as $data) {
                $sql .= 'WHEN ' . $referenceColumn . " = '" . $data[$referenceColumn] . "' THEN '" . $data[$uColumn] . "' ";
            }

            $sql .= 'ELSE ' . $uColumn . ' END, ';
        }

        foreach ($multipleData as $data) {
            $whereIn .= "'" . $data[$referenceColumn] . "', ";
        }

        $sql = rtrim($sql, ', ') . ' WHERE ' . $referenceColumn . ' IN (' . rtrim($whereIn, ', ') . ')';

        // 更新
        $result = DB::update(DB::raw($sql));

        $result ? flash_success('操作成功', $flash) : flash_error('操作失败', $flash);

        return $result;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
