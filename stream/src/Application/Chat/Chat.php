<?php

namespace App\Application\Chat;

use App\Domain\Chat\Actions\ActionInterface;
use App\Domain\Chat\Enum\ChatFlag;

class Chat
{
    private ?string $currentUser;
    private ?string $currentChat;
    /**
     * @var resource
     */
    private $channel;

    /**
     * @param ActionInterface[] $actions
     */
    public function __construct(
        private CommandReceiver $commandReceiver,
        private array $actions,
    ) {
    }

    public function startChat(): void
    {
        $command = $this->commandReceiver->getCommand($this->channel);
        while ($command->getFlag() !== ChatFlag::CHAT_EXIT) {
            $action = $this->actions[$command->getFlag()];

            if ($action->isModifyUser()) {
                $this->currentUser = $action($this->channel, $this->currentUser, $this->currentChat);
            }

            if ($action->isModifyChat()) {
                $this->currentChat = $action($this->channel, $this->currentUser, $this->currentChat);
            }

            $action($this->channel);
            $command = $this->commandReceiver->getCommand($this->channel);
        }
    }

    /**
     * @param resource $channel
     */
    public function setChannel($channel): void
    {
        $this->channel = $channel;
    }
}
