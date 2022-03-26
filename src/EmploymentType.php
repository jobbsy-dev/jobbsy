<?php

namespace App;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';
}
