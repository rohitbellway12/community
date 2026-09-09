<?php

namespace App\Enums;

enum CommentStatus: string
{
    case ACTIVE = 'active';
    case HIDDEN = 'hidden';
    case DELETED = 'deleted';
}
