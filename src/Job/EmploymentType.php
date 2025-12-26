<?php

namespace App\Job;

enum EmploymentType: string
{
    case FULLTIME = 'fulltime';

    case CONTRACT = 'contract';

    case INTERNSHIP = 'internship';
}
