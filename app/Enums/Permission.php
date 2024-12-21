<?php

namespace App\Enums;

enum Permission: string
{
    case ADMIN_APP = 'Access the Admin App';

    case COMPANY_LIST = 'See the List of Companies';
    case COMPANY_CREATE = 'Create a Company';
    case COMPANY_SHOW = 'See a Company';
    case COMPANY_EDIT = 'Edit a Company';
    case COMPANY_DELETE = 'Delete a Company';

    /**
     * Get all permissions.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
