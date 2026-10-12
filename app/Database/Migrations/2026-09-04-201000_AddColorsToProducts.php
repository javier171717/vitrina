<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColorsToProducts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', [
            'colors' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'compatible',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'colors');
    }
}
