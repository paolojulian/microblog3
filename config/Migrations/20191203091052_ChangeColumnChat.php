<?php
use Migrations\AbstractMigration;

class ChangeColumnChat extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $chat = $this->table('chat');
        $chat->changeColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->changeColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ]);
    }
}
