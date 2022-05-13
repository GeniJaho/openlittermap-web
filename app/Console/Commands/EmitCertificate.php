<?php

namespace App\Console\Commands;

use App\Actions\Certificates\EmitCertificateAction;
use Illuminate\Console\Command;

class EmitCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:emit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emits a certificate';

    /**
     * @var EmitCertificateAction
     */
    private $action;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EmitCertificateAction $action)
    {
        parent::__construct();
        $this->action = $action;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $certificate = $this->action->run();

        return 0;
    }
}
