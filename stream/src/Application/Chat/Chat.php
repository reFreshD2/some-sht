<?php

namespace App\Application\Chat;

use App\Domain\Chat\Actions\ActionInterface;
use App\Domain\Chat\Enum\ChatFlag;

class Chat
{
    private ?string $currentUser = null;
    private ?string $currentChat = null;
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

            if (!$action) {
                fwrite($this->channel, 'Undefined command' . PHP_EOL);
                $action = $this->actions[ChatFlag::HELP];
            }

            if ($action->isModifyUser()) {
                $this->currentUser = $action(
                    $this->channel,
                    $this->currentUser,
                    $this->currentChat,
                    $command->getMessage(),
                );
            } elseif ($action->isModifyChat()) {
                $this->currentChat = $action(
                    $this->channel,
                    $this->currentUser,
                    $this->currentChat,
                    $command->getMessage(),
                );
            } else {
                $action($this->channel, $this->currentUser, $this->currentChat, $command->getMessage());
            }

            $command = $this->commandReceiver->getCommand($this->channel);
        }
        $this->currentChat = null;
        $this->currentUser = null;
    }

    /**
     * @param resource $channel
     */
    public function setChannel($channel): void
    {
        $this->channel = $channel;
    }
}
