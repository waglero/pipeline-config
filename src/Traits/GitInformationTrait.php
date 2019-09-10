<?php

namespace Waglero\Traits;

trait GitInformationTrait
{
    protected $reservedBranchNames = [
        'stage',
        'master',
        'develop',
        'production'
    ];

    /**
     * @return array|string|null
     */
    private function getBranchName()
    {
        $branchName = $this->argument('branch');
        if (! $branchName) {
            $branchName = exec('git name-rev --name-only HEAD');
        }

        if (in_array($branchName, $this->reservedBranchNames)) {
            $this->error('You can not set configuration to this branch');
            exit;
        }

        return $branchName;
    }
}