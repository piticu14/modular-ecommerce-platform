<?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use RabbitTopology;
    use Symfony\Component\Console\Command\Command as CommandAlias;

    class RabbitSetup extends Command
    {
        protected $signature = 'rabbit:setup';

        protected $description = 'Create RabbitMQ topology';

        public function handle(RabbitTopology $topology)
        {
            try {

                $topology->declare();

                $this->info('RabbitMQ topology created or already exists.');

                return CommandAlias::SUCCESS;

            } catch (\Throwable $e) {

                $this->error('RabbitMQ topology setup failed.');
                $this->error($e->getMessage());

                return Command::FAILURE;
            }
        }
    }
