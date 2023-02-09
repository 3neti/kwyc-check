<?php

namespace App\Enums;

enum FormatEnum:string {
    case TXT = 'Text';
    case CSV = 'Comma-Separated Values';
    case XLS = 'Excel Worksheet';
    case SQL = 'Structured Query Language';
}
