<?php

use App\Jobs\PublishOutboxJob;

Schedule::job(new PublishOutboxJob)->everySecond();
