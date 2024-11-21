<?php

namespace App\Job\Bridge\OpenAI;

use App\Entity\Job;

final class CreateJobPromptForClassification
{
    public static function create(Job $job): string
    {
        return \sprintf('Extract maximum 5 tech keywords separated by comma from this text: %s', $job->getDescription());
    }
}
