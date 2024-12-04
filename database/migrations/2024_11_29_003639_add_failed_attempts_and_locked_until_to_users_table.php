<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFailedAttemptsAndLockedUntilToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if the 'failed_attempts' column doesn't exist before adding it
            if (!Schema::hasColumn('users', 'failed_attempts')) {
                $table->integer('failed_attempts')->default(0);
            }

            // Check if the 'locked_until' column doesn't exist before adding it
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'failed_attempts')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('failed_attempts');
            });
        }

        if (Schema::hasColumn('users', 'locked_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('locked_until');
            });
        }
    }
}
