<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Schema\Blueprint;
use support\Model;
use WebmanTech\LaravelDatabase\Facades\DB;

class User extends Model
{
    protected $table = 'users';

    protected static function booting()
    {
        DB::connection()->getSchemaBuilder()
            ->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->nullable();
                $table->string('name')->nullable();
                $table->string('password')->nullable();
                $table->string('column_string')->nullable();
                $table->text('column_text')->nullable();
                $table->decimal('column_decimal', 12)->nullable();
                $table->timestamps();
            });
    }
}
