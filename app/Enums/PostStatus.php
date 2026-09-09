<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
    case DELETED = 'deleted';
}
