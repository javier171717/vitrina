<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWholesaleMinTotalToBusinesses extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('businesses', [
            'wholesale_min_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'show_prices',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('businesses', 'wholesale_min_total');
    }
}
