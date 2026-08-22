<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case SUBMITTED = 'submitted';
    case REVISION = 'revision';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
