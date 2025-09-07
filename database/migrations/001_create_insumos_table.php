<?php

class CreateInsumosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'insumo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'medida' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'custo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->createTable('insumos');
    }

    public function down()
    {
        $this->forge->dropTable('insumos');
    }
} 