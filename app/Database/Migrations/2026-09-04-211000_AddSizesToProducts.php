<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSizesToProducts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', [
            'sizes' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'colors',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'sizes');
    }
}
