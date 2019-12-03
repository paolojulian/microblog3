<?php
use Migrations\AbstractMigration;

class AddTableChats extends AbstractMigration
{
    public function up()
    {
        $chats = $this->table('chats');
        $chats
            ->addColumn('message', 'string', ['limit' => 255])
            ->addColumn('is_read', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addColumn('receiver_id', 'integer')
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ])
            ->addColumn('deleted', 'datetime', ['null' => true])
            ->save();
    }
}
