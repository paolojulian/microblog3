<?php
use Migrations\AbstractMigration;

class AddTableChat extends AbstractMigration
{
    public function up()
    {
        $chats = $this->table('chat');
        $chats
            ->addColumn('message', 'string', ['limit' => 255])
            ->addColumn('is_read', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addColumn('receiver_id', 'integer')
            ->addColumn('created', 'datetime')
            ->addColumn('updated', 'datetime', ['null' => true])
            ->save();
    }
}
