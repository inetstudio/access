<?php

namespace InetStudio\AccessPackage\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:access-package:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup access package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Fields setup',
                'command' => 'inetstudio:access-package:fields:setup',
            ],
        ];
    }
}
