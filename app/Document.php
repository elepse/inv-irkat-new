<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Document
 *
 * @property int $id
 * @property int $type
 * @property string|null $date
 * @property int|null $from_employee
 * @property int|null $to_employee
 * @property string $create_time
 * @property string|null $edit_time
 * @property int $document_status
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereDocumentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereFromEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereToEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Document whereType($value)
 * @mixin \Eloquent
 */
class Document extends Model
{
    protected $table = 'documents';
    public $timestamps = false;
    protected $guarded = [''];
}