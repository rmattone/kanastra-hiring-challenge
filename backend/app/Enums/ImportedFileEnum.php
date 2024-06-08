<?php

namespace App\Enums;
 
enum ImportedFileEnum:int {
    case PENDING = 1;
    case PROCESSING = 2;
    case ERROR = 3;
    case COMPLETED = 4;
}