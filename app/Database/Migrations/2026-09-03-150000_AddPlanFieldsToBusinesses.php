<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlanFieldsToBusinesses extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('businesses', [
            'plan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'trial',
                'after'      => 'is_active',
            ],
            'plan_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'plan',
            ],
            'trial_ends_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'plan_label',
            ],
            'next_billing_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'trial_ends_at',
            ],
            'nit' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'next_billing_at',
            ],
            'suspended' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'nit',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('businesses', [
            'plan', 'plan_label', 'trial_ends_at',
            'next_billing_at', 'nit', 'suspended',
        ]);
    }
}
