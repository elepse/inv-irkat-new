<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DocumentFile
 *
 * @property int $id
 * @property int $document_id
 * @property string $file_link
 * @property string $create_time
 * @property string|null $edit_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile whereFileLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile whereId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentFile query()
 */
class DocumentFile extends Model
{
    protected $table = 'document_files';
    public $timestamps = false;
    protected $guarded = [];
}
