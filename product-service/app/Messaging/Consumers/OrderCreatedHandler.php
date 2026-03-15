<?php
    namespace App\Messaging\Consumers;


    use App\Stock\Application\Actions\ReserveStockAction;

    class OrderCreatedHandler
    {
        public function __construct(
            private ReserveStockAction $reserveStock
        ) {}

        public function handle(array $event): void
        {
            $this->reserveStock->handle($event);
        }
    }
