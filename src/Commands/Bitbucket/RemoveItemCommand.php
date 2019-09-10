<?php

namespace Waglero\Commands\Bitbucket;

use Illuminate\Console\Command;
use Waglero\Traits\GitInformationTrait;

class RemoveItemCommand extends Command
{
    use GitInformationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitbucket:pipeline:remove
                    { branch? : If not informed it will get the name of the branch in use }';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an item from the pipeline configuration file';

    /**
     * @var string
     */
    protected $filePath = 'bitbucket-pipelines.yml';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $branchName = $this->getBranchName();
        $this->removeItemFromFile($branchName);
    }

    private function removeItemFromFile($branchName)
    {
        if (! file_exists($this->getFilePath())) {
            $this->error('No configuration file found');
            exit;
        }

        $data = \Spyc::YAMLLoad($this->getFilePath());

        if (empty($data['pipelines']['branches'][$branchName])) {
            $this->error('No configuration found for this branch');
            exit;
        }

        unset($data['pipelines']['branches'][$branchName]);

        return file_put_contents(
            $this->filePath,
            \Spyc::YAMLDump($data, 2, 80, true)
        );
    }

    /**
     * @return string
     */
    private function getFilePath()
    {
        return $this->filePath;
    }
}