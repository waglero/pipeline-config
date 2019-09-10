<?php

namespace Waglero\Commands\Bitbucket;

use Illuminate\Console\Command;
use Waglero\Traits\GitInformationTrait;

class AddItemCommand extends Command
{

    use GitInformationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitbucket:pipeline:add
                    { environment : The server environment name. }
                    { branch? : If not informed it will get the name of the branch in use. Will overwrite if it already exists }
                    { --dump-only : Dump content to the terminal}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an item to the pipeline configuration file';

    /**
     * @var string
     */
    protected $filePath = 'bitbucket-pipelines.yml';

    /**
     * @var array 
     */
    protected $stepTemplate = [
        "step" => [
            "name" => "Deploy to beanstalk",
            "image" => "python:3.8.0a2-stretch",
            "script" => [
                "export APPLICATION_ENVIRONMENT_WEB=\"[[ENV_NAME]]\"",
                "apt-get update",
                "apt-get install -y zip",
                "pip install boto3==1.3.0",
                "mv .env.example .env",
                "zip /tmp/projeto.zip -r * .[^.]* -x *.git*",
                "python beanstalk_deploy.py"
            ]
        ]
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $environment = $this->getEnvironment();
        $branchName = $this->getBranchName();
        $this->addItemToFile($branchName, $environment);
    }

    /**
     * @return array
     */
    private function loadExistentData()
    {
        if (! file_exists($this->getFilePath())) {
            return ['pipelines' => ['branches' => []]];
        }

        return \Spyc::YAMLLoad($this->getFilePath());
    }

    /**
     * @return array|string|null
     */
    private function getEnvironment()
    {
        return $this->argument('environment');
    }

    /**
     * @return string
     */
    private function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param array $data
     * @return bool|int
     */
    private function writeYmlFile(array $data)
    {
        if ($this->option('dump-only')) {
            echo \Spyc::YAMLDump($data, 2, 80, true);
            return true;
        }

        return file_put_contents(
            $this->filePath,
            \Spyc::YAMLDump($data, 2, 80, true)
        );
    }

    /**
     * @param string $branchName
     * @param string $environment
     * @return bool|int
     */
    private function addItemToFile(string $branchName, string $environment)
    {
        $step = $this->stepTemplate;
        $step['step']['script'][0] = str_replace('[[ENV_NAME]]', $environment, $step['step']['script'][0]);
        $existentData = $this->loadExistentData();
        $existentData['pipelines']['branches'][$branchName][0] = $step;

        return $this->writeYmlFile($existentData);
    }
}