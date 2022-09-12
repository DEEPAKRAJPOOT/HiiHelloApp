<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $usersTable = [
        'users'
    ];

    public function truncateTables(array $tableNames = []): void
    {
        if (0 == count($tableNames)) {
            $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        }

        DB::statement("SET foreign_key_checks=0");
        foreach ($tableNames as $tableName) {
            if ($tableName == 'migrations') {
                continue;
            }
            DB::table($tableName)->truncate();
        }

        DB::statement("SET foreign_key_checks=1");
    }
}
