<?php
namespace App\Enums;

enum UserType:string {
    case COMUM = 'comum';
    case ADMIN = 'admin';
    case MASTER = 'master';
}