<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Onlinepets\TimedMigrations\Contracts\RunsInTimeframe;

class CreateTimedUsersOneTable extends Migration implements RunsInTimeframe
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timed_users_one', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timed_users_one');
    }

    /**
     * @return \Illuminate\Support\Carbon[]
     */
    public function getTimesToRunBetween(): array
    {
        return [
            new Carbon('-1 hour'),
            new Carbon('+1 hour'),
        ];
    }
}
